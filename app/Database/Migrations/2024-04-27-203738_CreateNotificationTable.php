<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'ID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'notificationFor' => [
                'type' => 'INT',
            ],
            'notificationFrom' => [
              'type' => 'INT',
            ],
            'target' => [
              'type' => 'INT',
            ],
            'type' => [
              'type'       => 'ENUM',
              'constraint' => ['follow', 'retweet', 'like', 'mention'],
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
        $this->forge->addKey('ID', true);
        $this->forge->createTable('notification');
    }

    public function down()
    {
        $this->forge->dropTable('notification');
    }
}
