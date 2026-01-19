<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateIzifisoZoneM extends Migration
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
            'zone_name' => [
                'type'           => 'VARCHAR',
                'constraint'     => 255,
                'null'           => false,
            ],
      
        ]);

        $this->forge->addKey('id', true);
        $attributes = [
            'ENGINE' => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];
        $this->forge->createTable('izifiso_zone_m', true, $attributes);
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->dropTable('izifiso_zone_m');
        $this->db->enableForeignKeyChecks();
    }
}
