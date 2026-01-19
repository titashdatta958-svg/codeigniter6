<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzifisoEmpZoneMapping extends Migration
{
    public function up()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
            'zone_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
       
            'job_type_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
      
        ]);

        $this->forge->addKey('id', true); 
        //make foreign key between employee_id and izifiso_employee id    
        $this->forge->addForeignKey('employee_id', 'izifiso_employee', 'id', 'CASCADE', 'CASCADE');
        //make foreign key between zone_id and izifiso_zone_m id    
        $this->forge->addForeignKey('zone_id', 'izifiso_zone_m', 'id', 'CASCADE', 'CASCADE'); 
        //make foreign key between job_type_id and izifiso_job_type id    
        $this->forge->addForeignKey('job_type_id', 'izifiso_job_type', 'id', 'CASCADE', 'CASCADE');

            

        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->createTable('izifiso_emp_zone_mapping', true, $attributes);
        $this->db->enableForeignKeyChecks();

        
    }

    public function down()
    {

        $this->db->disableForeignKeyChecks();
        $this->forge->dropTable('izifiso_emp_zone_mapping');
        $this->db->enableForeignKeyChecks();

    }
}
