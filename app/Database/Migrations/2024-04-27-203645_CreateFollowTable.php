<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFollowTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'followID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'sender' => [
                'type' => 'INT',
            ],
            'receiver' => [
                'type' => 'INT',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('followID', true);
        $this->forge->createTable('follow');
    }

    public function down()
    {
        $this->forge->dropTable('follow');
    }
}
