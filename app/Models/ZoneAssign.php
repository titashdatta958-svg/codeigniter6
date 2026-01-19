<?php namespace App\Models;
use CodeIgniter\Model;
class ZoneAssign extends Model {
    protected $table = 'izifiso_zone_assign';
    protected $primaryKey = 'id';
    protected $allowedFields = ['zone_id', 'member_id', 'assigned_by', 'assigned_at'];
}   
