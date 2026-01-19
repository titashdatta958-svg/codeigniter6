<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzifisoZoneAssign extends Migration
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
            'zone_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // CRITICAL: This allows "Office Based" employees
            ],
            'member_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'assigned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'assigned_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        // 1. FOREIGN KEY FOR ZONE (SAFE NOW)
        // Since 'null' => true above, this FK will only trigger if a number is provided.
        // If zone_id is NULL, the DB skips this check.
        $this->forge->addForeignKey('zone_id', 'izifiso_zone_m', 'id', 'CASCADE', 'SET NULL');

        // 2. FOREIGN KEY FOR MEMBER
        $this->forge->addForeignKey('member_id', 'izifiso_employee', 'id', 'CASCADE', 'CASCADE');
        
        // 3. FOREIGN KEY FOR ASSIGNED_BY
        $this->forge->addForeignKey('assigned_by', 'izifiso_employee', 'id', 'CASCADE', 'CASCADE');
        
        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->createTable('izifiso_zone_assign', true, $attributes);
        $this->db->enableForeignKeyChecks();    
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->dropTable('izifiso_zone_assign');
        $this->db->enableForeignKeyChecks();
    }
}