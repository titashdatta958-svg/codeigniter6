<?php namespace App\Models;
use CodeIgniter\Model;

class DestinationModel extends Model {
    protected $table = 'izifiso_destinations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['destination_name','state_id','description','created_at','updated_at'];
    protected $useTimestamps = true;
}
