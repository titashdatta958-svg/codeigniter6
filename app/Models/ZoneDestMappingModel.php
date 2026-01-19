<?php namespace App\Models;
use CodeIgniter\Model;
class ZoneDestMappingModel extends Model {
    protected $table = 'izifiso_zone_destination_mappings';
    protected $primaryKey = 'id';   
    protected $allowedFields = ['zone_id', 'destination_id', 'created_at', 'updated_at'];






public function getDestinationsByZone($zoneId)
{
    return $this->db->table('izifiso_destinations d')
        ->join('izifiso_zone_destination_mappings m','m.destination_id = d.id')
        ->where('m.zone_id',$zoneId)
        ->get()
        ->getResultArray();
}

public function deleteByZone($zoneId)
{
    return $this->where('zone_id',$zoneId)->delete();
}










}   

