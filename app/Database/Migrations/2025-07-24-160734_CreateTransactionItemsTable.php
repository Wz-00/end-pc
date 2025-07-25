<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionItemsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => [
                'type'          => 'INT',
                'unsigned'      => true,
                'auto_increment'=> true
            ],
            'transaction_id'   => [
                'type'          => 'VARCHAR',
                'constraint'    => 100
            ],
            'product_id'       => [
                'type'          => 'INT'
            ],
            'quantity'         => [
                'type'          => 'INT',
                'constraint'    => 11
            ],
            'price_per_item'   => [
                'type'          => 'DECIMAL',
                'constraint'    => '10,2'
            ],
            'subtotal'         => [
                'type'          => 'DECIMAL',
                'constraint'    => '10,2'
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('transaction_id', 'transactions', 'transaction_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('transaction_items');
    }

    public function down()
    {
        $this->forge->dropTable('transaction_items');
    }
}
