<?php namespace App\Models;

use CodeIgniter\Model;

class EmpZoneMappingModel extends Model {
    protected $table = 'izifiso_emp_zone_mapping';
    protected $primaryKey = 'id';
    protected $allowedFields = ['employee_id', 'zone_id', 'job_type_id'];

public function getTeamList($deptId) {
    $team = $this->db->table($this->table)
        ->select('
            izifiso_employee.id as employee_id, 
            izifiso_employee.employee_name, 
            GROUP_CONCAT(DISTINCT izifiso_zone_m.zone_name SEPARATOR ", ") as zone_names,
            GROUP_CONCAT(DISTINCT izifiso_zone_m.id) as zone_ids,
            GROUP_CONCAT(DISTINCT izifiso_job_type.job_type_name SEPARATOR ", ") as job_roles,
            GROUP_CONCAT(DISTINCT izifiso_job_type.id) as job_type_ids,
            GROUP_CONCAT(izifiso_emp_zone_mapping.id) as map_ids
        ')
        ->join('izifiso_employee', 'izifiso_employee.id = izifiso_emp_zone_mapping.employee_id')
        ->join('izifiso_job_type', 'izifiso_job_type.id = izifiso_emp_zone_mapping.job_type_id')
        ->join('izifiso_zone_m', 'izifiso_zone_m.id = izifiso_emp_zone_mapping.zone_id', 'left')
        ->where('izifiso_employee.department_id', $deptId)
        ->groupBy('izifiso_employee.id')
        ->orderBy('izifiso_employee.employee_name', 'ASC')
        ->get()->getResultArray();

    // PHP post-processing
    foreach ($team as &$member) {
        // If no zones assigned, set as Office Based
        if (empty($member['zone_names'])) {
            $member['zone_names'] = 'Office Based';
            $member['zone_ids']   = null; // so frontend knows it's not a real zone
        }
    }

    return $team;
}




}