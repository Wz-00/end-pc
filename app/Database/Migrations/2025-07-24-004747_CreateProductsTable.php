<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'product_id'        => [
                'type'          => 'INT',
                'unsigned'      => true,
                'auto_increment'=> true
            ],
            'slug'              => [
                'type'          => 'VARCHAR',
                'constraint'    => 255
            ],
            'cat_id'            => [
                'type'          => 'id'
            ],
            'product_name'      => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'image'             => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'description'       => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'stock'             => [
                'type'          => 'INT', 
                'default'       => 0
            ],
            'price'             => [
                'type'          => 'DECIMAL', 
                'constraint'    => '10,2'
            ]
        ]);
        $this->forge->addKey('product_id', true);
        $this->forge->addForeignKey('cat_id', 'categories', 'category_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');
    }

    public function down()
    {
        $this->forge->dropTable('products');
    }
}
