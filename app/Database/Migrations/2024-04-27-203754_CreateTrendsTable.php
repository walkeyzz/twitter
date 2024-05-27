<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTrendsTable extends Migration
{
    public function up()
    {
      $this->forge->addField([
            'trendID' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'hashtag' => [
              'type' => 'VARCHAR',
              'constraint' => '140',
            ],
            'created_at' => [
                'type'    => 'TIMESTAMP',
            ],
        ]);
        $this->forge->addKey('trendID', true);
        $this->forge->createTable('trends');
    }

    public function down()
    {
        $this->forge->dropTable('trends');
    }
}
