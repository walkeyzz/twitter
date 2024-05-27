<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLikesTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'likeID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'likeBy' => [
                'type' => 'INT',
            ],
            'likeOn' => [
                'type' => 'INT',
            ],
        ]);
        $this->forge->addKey('likeID', true);
        $this->forge->createTable('likes');
    }

    public function down()
    {
        $this->forge->dropTable('likes');
    }
}
