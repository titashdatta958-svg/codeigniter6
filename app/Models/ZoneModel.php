<?php namespace App\Models;
use CodeIgniter\Model;

class ZoneModel extends Model {
    protected $table = 'izifiso_zone_m';
    protected $primaryKey = 'id';
    protected $allowedFields = ['zone_name'];





public function getZonesWithDestinationCount()
{
    return $this->db->table('izifiso_zone_m z')
        ->select("
            z.id,
            z.zone_name,
            COUNT(m.destination_id) AS destination_count,
            GROUP_CONCAT(d.destination_name ORDER BY d.destination_name SEPARATOR ', ') AS destination_names
        ")
        ->join('izifiso_zone_destination_mappings m', 'm.zone_id = z.id', 'left')
        ->join('izifiso_destinations d', 'd.id = m.destination_id', 'left')
        ->groupBy('z.id')
        ->get()
        ->getResultArray();
}


}