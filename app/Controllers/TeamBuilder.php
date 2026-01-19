<?php namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\JobTypeModel;
use App\Models\ZoneModel;
use App\Models\EmpZoneMappingModel;
use App\Models\DepartmentModel;

class TeamBuilder extends BaseController
{
    /* ----------------------------------------
       ROLE CHECK HELPER
    ---------------------------------------- */
    private function canEdit()
    {
        $role = session('system_role');
        return in_array($role, ['Manager', 'Super Manager']);
    }

    private function forbidden()
    {
        return $this->response->setJSON([
            'status'  => 'error',
            'message' => 'You do not have permission to perform this action.'
        ], 403);
    }

  public function index()
{
    $deptModel = new DepartmentModel();
    $zoneModel = new ZoneModel();

    $role = session('system_role');

    $isSuperManager = ($role === 'Super Manager');
    $isManager      = ($role === 'Manager');
    $isViewer       = in_array($role, ['Member','Intern']);

    return view('team_builder_view', [
        'departments'     => $deptModel->findAll(),
        'zones'           => $zoneModel->findAll(),

        // 🔐 ROLE FLAGS (VERY IMPORTANT)
        'isSuperManager'  => $isSuperManager,
        'isManager'       => $isManager,
        'isViewer'        => $isViewer,
        'canEdit'         => ($isSuperManager || $isManager),
        'canRegister'     => ($isSuperManager),
    ]);
}


    /* VIEW – ALLOWED FOR ALL */
public function get_dept_data($deptId)
{
    $role = session()->get('system_role');

    $empModel = new EmployeeModel();
    $jobModel = new JobTypeModel();
    $mapModel = new EmpZoneMappingModel();
    $zoneModel = new ZoneModel();

    $currentTeam = $mapModel->getTeamList($deptId);

    $assignedEmpIds = array_values(array_unique(
        array_column($currentTeam, 'employee_id')
    ));

    // Everyone gets same JSON structure
    return $this->response->setJSON([
        'employees'        => $empModel->where('department_id', $deptId)->findAll(),
        'job_types'        => $jobModel->where('department_id', $deptId)->findAll(),
        'current_team'     => $currentTeam,
        'assigned_emp_ids' => $assignedEmpIds,
        'zones'            => $zoneModel->findAll() // <-- Add this line
    ]);
}


    /* ----------------------------------------
       SAVE ROLE – MANAGER & SUPER MANAGER ONLY
    ---------------------------------------- */
    public function save_role_only()
    {
        if (!$this->canEdit()) {
            return $this->forbidden();
        }

        $json = $this->request->getJSON();
        $jobModel = new JobTypeModel();

        if (empty($json->job_type_name) || empty($json->department_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Role name and Department are required.'
            ]);
        }

        if ($jobModel->where('job_type_name', $json->job_type_name)
                     ->where('department_id', $json->department_id)
                     ->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Role already exists in this department.'
            ]);
        }

        $jobModel->insert([
            'job_type_name' => $json->job_type_name,
            'department_id' => $json->department_id
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    /* ----------------------------------------
       ASSIGN MEMBER – MANAGER & SUPER MANAGER
    ---------------------------------------- */
    public function assign_member()
    {
        if (!$this->canEdit()) {
            return $this->forbidden();
        }

        $mapModel = new EmpZoneMappingModel();

        $employeeId   = $this->request->getPost('employee_id');
        $jobTypeIds   = $this->request->getPost('job_type_ids');
        $zoneIds      = $this->request->getPost('zone_ids');
        $departmentId = $this->request->getPost('department_id');

        if (empty($jobTypeIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'At least one job role must be selected.'
            ]);
        }

        $fieldDepts = ['1','2','3'];

        if (in_array($departmentId, $fieldDepts) && empty($zoneIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'At least one zone is required.'
            ]);
        }

        if (!in_array($departmentId, $fieldDepts)) {
            $zoneIds = [null];
        }

        if (in_array($departmentId, $fieldDepts)) {
            $db = \Config\Database::connect();
            foreach ($zoneIds as $zoneId) {
                $count = $db->table('izifiso_zone_destination_mappings')
                            ->where('zone_id', $zoneId)
                            ->countAllResults();
                if ($count == 0) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Zone must have destinations.'
                    ]);
                }
            }
        }

        $alreadyInTeam = $mapModel->db->table('izifiso_emp_zone_mapping m')
            ->join('izifiso_employee e', 'e.id = m.employee_id')
            ->where('e.id', $employeeId)
            ->where('e.department_id', $departmentId)
            ->get()->getRow();

        if ($alreadyInTeam) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'This member already exists.'
            ]);
        }

        foreach ($jobTypeIds as $jobTypeId) {
            foreach ($zoneIds as $zoneId) {
                $mapModel->insert([
                    'employee_id' => $employeeId,
                    'job_type_id' => $jobTypeId,
                    'zone_id'     => $zoneId
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Team member assigned successfully.'
        ]);
    }

    /* ----------------------------------------
       REMOVE MEMBER – MANAGER & SUPER MANAGER
    ---------------------------------------- */
    public function remove_member()
    {
        if (!$this->canEdit()) {
            return $this->forbidden();
        }

        $mapModel = new EmpZoneMappingModel();
        $ids = $this->request->getPost('map_ids');

        if (!$ids) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No IDs provided.'
            ]);
        }

        $mapModel->whereIn('id', explode(',', $ids))->delete();

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Member roles removed successfully.'
        ]);
    }

    /* ----------------------------------------
       UPDATE ASSIGNMENT – MANAGER & SUPER MANAGER
    ---------------------------------------- */
    public function update_assignment()
    {
        if (!$this->canEdit()) {
            return $this->forbidden();
        }

        $mapModel = new EmpZoneMappingModel();

        $mapIds     = $this->request->getPost('map_id');
        $employeeId = $this->request->getPost('employee_id');
        $jobTypeIds = $this->request->getPost('job_type_ids');
        $zoneIds    = $this->request->getPost('zone_ids');

        if (!$mapIds || empty($jobTypeIds)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Missing data.'
            ]);
        }

        if (empty($zoneIds)) {
            $zoneIds = [null];
        }

        $mapModel->whereIn('id', explode(',', $mapIds))->delete();

        foreach ($jobTypeIds as $roleId) {
            foreach ($zoneIds as $zoneId) {
                $mapModel->insert([
                    'employee_id' => $employeeId,
                    'job_type_id' => $roleId,
                    'zone_id'     => $zoneId
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Team member updated successfully.'
        ]);
    }

    /* ----------------------------------------
       SAVE ZONE – MANAGER & SUPER MANAGER
    ---------------------------------------- */
    public function save_zone_only()
    {
        if (!$this->canEdit()) {
            return $this->forbidden();
        }

        $json = $this->request->getJSON();
        $zoneModel = new ZoneModel();

        if (empty($json->zone_name)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Zone name is required.'
            ]);
        }

        if ($zoneModel->where('zone_name', $json->zone_name)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'This zone already exists.'
            ]);
        }

        $zoneModel->insert(['zone_name' => $json->zone_name]);

        return $this->response->setJSON([
            'status' => 'success',
            'zone' => [
                'id' => $zoneModel->getInsertID(),
                'zone_name' => $json->zone_name
            ]
        ]);
    }

    /* VIEW – ALLOWED FOR ALL */
    public function get_zone_destinations($zoneId)
    {
        $mapModel = new \App\Models\ZoneDestMappingModel();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $mapModel->getDestinationsByZone($zoneId)
        ]);
    }
}
