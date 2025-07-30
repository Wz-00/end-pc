<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\I18n\Time;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id'         => [
                'type' => 'INT', 
                'constraint' => 11, 
                'unsigned' => true, 
                'auto_increment' => true
            ],
            'name'     => [
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
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('users');

        
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
