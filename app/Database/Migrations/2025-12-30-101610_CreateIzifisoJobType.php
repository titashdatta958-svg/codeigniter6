<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzifisoJobType extends Migration
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
            'job_type_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
            'department_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => false,
            ],
      
        ]);

        $this->forge->addKey('id', true);
        //make foreign key between department_id and izifiso_department id
        $this->forge->addForeignKey('department_id', 'izifiso_department', 'id', 'CASCADE', 'CASCADE'); 
        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];
        $this->forge->createTable('izifiso_job_type', true, $attributes);
        $this->db->enableForeignKeyChecks();    
        
    }

    public function down()
    {
        
        $this->db->disableForeignKeyChecks();
        $this->forge->dropTable('izifiso_job_type');
        $this->db->enableForeignKeyChecks();
    }
}
