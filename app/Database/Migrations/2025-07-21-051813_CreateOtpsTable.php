<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtpsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'email'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'otp'        => ['type' => 'VARCHAR', 'constraint' => 10],
            'expires_at' => ['type' => 'DATETIME'],
            'type'       => ['type' => 'ENUM', 'constraint' => ['register', 'forgot']],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('otps');
    }

    public function down()
    {
        $this->forge->dropTable('otps');
    }
}
