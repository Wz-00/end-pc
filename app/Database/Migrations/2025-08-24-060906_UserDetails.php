<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'uid'        => [
                'type' => 'INT', 
                'auto_increment' => true
            ],
            'user_id'    => [
                'type' => 'INT',
                'null' => true
            ],
            'guest_identifier' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'country'    => [
                'type' => 'ENUM', 
                'constraint' => ['Indonesia', 'Malaysia', 'Singapore', 'Thailand', 'Vietnam', 'Philippines', 'Brunei', 'Cambodia', 'Laos', 'Myanmar'],
                'default' => 'Indonesia'
            ],
            'address'    => [
                'type' => 'TEXT',
                'null' => true
            ],
            'city'       => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'postal_code'=> [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'phone'      => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);
        $this->forge->addKey('uid', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('guest_identifier');
        $this->forge->createTable('user-details');
    }

    public function down()
    {
        $this->forge->dropTable('user-details');
    }
}
