<?php namespace App\Controllers;

use App\Models\ZoneModel;
use App\Models\DestinationModel;
use App\Models\ZoneDestMappingModel;

class Zones extends BaseController
{
  public function index()
{
    $zoneModel = new ZoneModel();

    $role = session()->get('system_role');

    // Member & Intern → viewer only
    $isViewer = in_array($role, ['Member', 'Intern']);

    return view('zones/index', [
        'zones'    => $zoneModel->getZonesWithDestinationCount(),
        'isViewer' => $isViewer
    ]);
}


  public function getDestinations()
{
    return $this->response->setJSON(
        (new DestinationModel())
            ->select('id, destination_name')
            ->orderBy('destination_name')
            ->findAll()
    );
}

public function getZoneDestinations($zoneId)
{
    $rows = (new ZoneDestMappingModel())
        ->where('zone_id', $zoneId)
        ->findAll();

    return $this->response->setJSON(
        array_column($rows, 'destination_id')
    );
}

    public function saveMapping()
    {
        $zoneId = $this->request->getPost('zone_id');
        $destinations = $this->request->getPost('destination_ids');

        if (!$zoneId || empty($destinations)) {
            return $this->response->setJSON([
                'status'=>'error',
                'message'=>'Select at least one destination'
            ]);
        }

        $mapModel = new ZoneDestMappingModel();
        $mapModel->deleteByZone($zoneId);

        foreach ($destinations as $destId) {
            $mapModel->insert([
                'zone_id'=>$zoneId,
                'destination_id'=>$destId
            ]);
        }

        return $this->response->setJSON(['status'=>'success']);
    }





    public function update()
{
    $id = $this->request->getPost('id');
    $name = trim($this->request->getPost('zone_name'));

    if (!$id || !$name) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid data'
        ]);
    }

    $zoneModel = new ZoneModel();

    //  Duplicate check
    $exists = $zoneModel
        ->where('zone_name', $name)
        ->where('id !=', $id)
        ->first();

    if ($exists) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Zone name already exists'
        ]);
    }

    $zoneModel->update($id, [
        'zone_name' => $name
    ]);

    return $this->response->setJSON([
        'status' => 'success'
    ]);
}


    
}
