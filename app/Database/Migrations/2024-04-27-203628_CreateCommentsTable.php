<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommentsTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'commentID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'comment' => [
                'type' => 'VARCHAR',
                'constraint' => '140',
            ],
            'commentOn' => [
                'type' => 'INT',
            ],
            'commentBy' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('commentID', true);
        $this->forge->createTable('comments');
    }

    public function down()
    {
        $this->forge->dropTable('comments');
    }
}
