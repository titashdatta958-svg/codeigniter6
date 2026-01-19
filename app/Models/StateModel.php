<?php namespace App\Models;
use CodeIgniter\Model;

class StateModel extends Model {
    protected $table = 'izifiso_state';
    protected $primaryKey = 'id';
    protected $allowedFields = ['state'];
}
