<?php namespace App\Controllers;

use App\Models\DestinationModel;
use App\Models\StateModel;
use App\Models\ZoneModel;
use CodeIgniter\API\ResponseTrait;

class Destination extends BaseController
{
    use ResponseTrait;

    protected $destModel;
    protected $stateModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->destModel  = new DestinationModel();
        $this->stateModel = new StateModel();
        $this->zoneModel  = new ZoneModel();
    }

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
        return $this->respond([
            'status'  => 'error',
            'message' => 'You do not have permission to perform this action.'
        ], 403);
    }

    /* VIEW – ALL ROLES */
    public function index()
    {
        $role = session('system_role');

        $isSuperManager = ($role === 'Super Manager');
        $isManager      = ($role === 'Manager');
        $isViewer       = in_array($role, ['Member', 'Intern']);

        return view('destinations_view', [
            'states'         => $this->stateModel->orderBy('state','ASC')->findAll(),
            'zones'          => $this->zoneModel->orderBy('zone_name','ASC')->findAll(),
            'isSuperManager' => $isSuperManager,
            'isManager'      => $isManager,
            'isViewer'       => $isViewer,
        ]);
    }

    /* VIEW – ALL ROLES */
    public function listData()
    {
        $destinations = $this->destModel
            ->select('izifiso_destinations.*, izifiso_state.state')
            ->join('izifiso_state', 'izifiso_state.id = izifiso_destinations.state_id')
            ->findAll();

        return $this->respond([
            'status' => 'success',
            'destinations' => $destinations
        ]);
    }

    /* SAVE – MANAGER & SUPER MANAGER ONLY */
    public function save()
    {
        if (!$this->canEdit()) return $this->forbidden();

        $name    = trim($this->request->getPost('destination_name'));
        $stateId = $this->request->getPost('state_id');

        if (!$name || !$stateId) {
            return $this->respond(['status' => 'error', 'message' => 'Destination and state required'], 422);
        }

        if ($this->destModel->where('destination_name', $name)->where('state_id', $stateId)->first()) {
            return $this->respond(['status' => 'error', 'message' => 'Destination already exists'], 409);
        }

        $this->destModel->insert([
            'destination_name' => $name,
            'state_id' => $stateId,
            'description' => $this->request->getPost('description')
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Destination added'], 201);
    }

    /* SAVE STATE – MANAGER & SUPER MANAGER ONLY */
    public function saveState()
    {
        if (!$this->canEdit()) return $this->forbidden();

        $data = $this->request->getJSON(true);
        $state = trim($data['state'] ?? '');

        if (!$state) {
            return $this->respond(['status' => 'error', 'message' => 'State name required'], 422);
        }

        if ($this->stateModel->where('state', $state)->first()) {
            return $this->respond(['status' => 'error', 'message' => 'State already exists'], 409);
        }

        $this->stateModel->insert(['state' => $state]);

        return $this->respond([
            'status' => 'success',
            'state'  => ['id' => $this->stateModel->getInsertID(), 'state' => $state]
        ], 201);
    }

    /* DELETE – MANAGER & SUPER MANAGER ONLY */
    public function delete()
    {
        if (!$this->canEdit()) return $this->forbidden();

        $id = $this->request->getPost('id');
        if (!$id) return $this->respond(['status' => 'error', 'message' => 'Invalid ID'], 422);

        $this->destModel->delete($id);

        return $this->respond(['status' => 'success', 'message' => 'Deleted']);
    }

    /* UPDATE – MANAGER & SUPER MANAGER ONLY */
    public function update()
    {
        if (!$this->canEdit()) return $this->forbidden();

        $id = $this->request->getPost('id');
        if (!$id) return $this->respond(['status' => 'error', 'message' => 'Invalid ID'], 422);

        $destinationName = trim($this->request->getPost('destination_name'));

        $exists = $this->destModel
            ->where('destination_name', $destinationName)
            ->where('id !=', $id)
            ->first();

        if ($exists) return $this->respond(['status' => 'error', 'message' => 'Destination name already exists'], 409);

        $this->destModel->update($id, [
            'destination_name' => $destinationName,
            'state_id'         => $this->request->getPost('state_id'),
            'description'      => $this->request->getPost('description')
        ]);

        return $this->respond(['status' => 'success', 'message' => 'Destination updated successfully']);
    }
}
