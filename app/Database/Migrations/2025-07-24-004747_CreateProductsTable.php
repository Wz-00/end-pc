<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_id'        => [
                'type'          => 'VARCHAR', 
                'constraint'    => 13
            ],
            'cat_id'            => [
                'type'          => 'INT'
            ],
            'product_name'      => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'image'          => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'description'       => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'price'             => [
                'type'          => 'DECIMAL', 
                'constraint'    => '10,2'
            ]
        ]);
        $this->forge->addKey('product_id', true);
        $this->forge->addForeignKey('cat_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
