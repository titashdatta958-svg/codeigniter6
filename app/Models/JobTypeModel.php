<?php namespace App\Models;
use CodeIgniter\Model;

class JobTypeModel extends Model {
    protected $table = 'izifiso_job_type';
    protected $primaryKey = 'id';
    protected $allowedFields = ['job_type_name', 'department_id'];

    // Fetch Job Types only for a specific department
    public function getByDept($deptId) {
        return $this->where('department_id', $deptId)->findAll();
    }
}