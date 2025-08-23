<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCartsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cart_id'           => [
                'type'          => 'INT',
                'constraint'    => 11,
                'unsigned'      => true,
                'auto_increment'=> true
            ],
            'user_id'           => [
                'type'          => 'INT',
                'constraint'    => 11,
                'unsigned'      => true,
                'null'          => true
            ],
            'product_id'        => [
                'type'          => 'INT',
                'constraint'    => 11,
                'unsigned'      => true
            ],
            'guest_identifier'   => [
                'type'          => 'VARCHAR',
                'constraint'    => 64,
                'null'          => true
            ],
            'quantity'          => [
                'type'          => 'INT',
                'constraint'    => 11,
                'default'       => 1
            ],
            'created_at'        => [
                'type'          => 'DATETIME',
                'null'          => true
            ],
            'updated_at'        => [
                'type'          => 'DATETIME',
                'null'          => true
            ]
        ]);
        $this->forge->addKey('cart_id', true);
        $this->forge->addForeignKey('user_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'product_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('carts');
    }

    public function down()
    {
        $this->forge->dropTable('carts');
    }
}
