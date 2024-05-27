<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTweetsTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'tweetID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '1000',
            ],
            'tweetBy' => [
              'type' => 'INT',
            ],
            'retweetID' => [
              'type' => 'INT',
            ],
            'retweetBy' => [
              'type' => 'INT',
            ],
            'tweetImage' => [
              'type' => 'VARCHAR',
              'constraint' => '225',
            ],
            'likesCount' => [
              'type' => 'INT',
            ],
            'retweetCount' => [
              'type' => 'INT',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
            ],
            'retweetMsg' => [
              'type' => 'VARCHAR',
              'constraint' => '140',
            ],
        ]);
        $this->forge->addKey('tweetID', true);
        $this->forge->createTable('tweets');
    }

    public function down()
    {
        $this->forge->dropTable('tweets');
    }
}
