<?php namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table      = 'izifiso_employee';
    protected $primaryKey = 'id';

    // ADD ALL THESE FIELDS HERE
    protected $allowedFields = [
        'employee_name', 
        'email', 
        'password', 
        'phone_no', 
        'manager', 
        'department_id', 
        'system_role', 
        'designation', 
        'location',
        'profile_image'
    ];

    protected $useTimestamps = true;
}