<?php namespace App\Models;
use CodeIgniter\Model;

class DepartmentModel extends Model {
    protected $table = 'izifiso_department';
    protected $primaryKey = 'id';
    protected $allowedFields = ['department_name'];
}