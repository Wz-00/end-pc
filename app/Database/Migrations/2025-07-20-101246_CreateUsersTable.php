<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true, 
                'auto_increment' => true
            ],
            'name'      => [
                'type' => 'VARCHAR', 
                'constraint' => 100
            ],
            'username'   => [
                'type' => 'VARCHAR', 
                'constraint' => 100]
                ,
            'email'      => [
                'type' => 'VARCHAR', 
                'constraint' => 100
            ],
            'password'   => [
                'type' => 'VARCHAR', 
                'constraint' => 255
            ],
            'role'       => [
                'type' => 'ENUM', 
                'constraint' => ['admin', 'user'], 
                'default' => 'user'
            ],
            'created_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME', 
                'null' => true
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        $now = Time::now(); // atau $now = time();
        $this->db->query("UPDATE users SET created_at = '{$now->format('Y-m-d H:i:s')}', updated_at = '{$now->format('Y-m-d H:i:s')}'");
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
