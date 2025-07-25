<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => [
                'type'          => 'INT', 
                'constraint'    => 11, 
                'unsigned'      => true, 
                'auto_increment'=> true
            ],
            'transaction_id'   => [
                'type'          => 'VARCHAR', 
                'constraint'    => 100
            ],
            'user_id'           => [
                'type'          => 'INT'
            ],
            'product_id'        => [
                'type'          => 'INT'
            ],
            'quantity'          => [
                'type'          => 'INT', 
                'constraint'    => 11
            ],
            'total_price'       => [
                'type'          => 'DECIMAL', 
                'constraint'    => '10,2'
            ],
            'transaction_date'  => [
                'type'          => 'DATETIME'
            ],
            'status'            => [
                'type'          => 'ENUM',
                'constraint'    => ['pending', 'completed', 'cancelled'],
                'default'       => 'pending'
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('transaction_id');
        $this->forge->addUniqueKey(['transaction_id', 'user_id', 'product_id']); // Unique combination
        $this->forge->createTable('transactions');
    }

    public function down()
    {
        $this->forge->dropTable('transactions');
    }
}
