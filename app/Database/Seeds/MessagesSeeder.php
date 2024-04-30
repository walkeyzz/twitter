<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MessagesSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('messages');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\MessagesData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
