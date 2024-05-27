<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => '40',
            ],
            'email' => [
              'type' => 'VARCHAR',
              'constraint' => '225',
            ],
            'password' => [
              'type' => 'VARCHAR',
              'constraint' => '32',
            ],
            'screenName' => [
              'type' => 'VARCHAR',
              'constraint' => '40',
            ],
            'profileImage' => [
              'type' => 'VARCHAR',
              'constraint' => '225',
            ],
            'profileCover' => [
              'type' => 'VARCHAR',
              'constraint' => '225',
            ],
            'following' => [
                'type' => 'INT',
            ],
            'followers' => [
                'type' => 'INT',
            ],
            'bio' => [
                'type' => 'VARCHAR',
                'constraint' => '140',
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'website' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addKey('user_id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
