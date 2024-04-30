<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagesTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'messageID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'messageTo' => [
                'type' => 'INT',
            ],
            'messageFrom' => [
                'type' => 'INT',
            ],
            'created_at' => [
              'type'    => 'TIMESTAMP',
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => '0',
            ],
        ]);
        $this->forge->addKey('messageID', true);
        $this->forge->createTable('messages');
    }

    public function down()
    {
        $this->forge->dropTable('messages');
    }
}
