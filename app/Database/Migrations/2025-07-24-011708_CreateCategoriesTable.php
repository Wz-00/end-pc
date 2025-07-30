<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'category_id'       => [
                'type'          => 'INT', 
                'constraint'    => 11, 
                'unsigned'      => true, 
                'auto_increment'=> true
            ],
            'slug'             => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255
            ],
            'category'          => [
                'type'          => 'VARCHAR', 
                'constraint'    => 255]
        ]);
        $this->forge->addKey('category_id', true);
        $this->forge->createTable('categories');
    }

    public function down()
    {
        $this->forge->dropTable('categories');
    }
}
