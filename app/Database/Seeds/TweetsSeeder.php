<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TweetsSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('tweets');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\TweetsData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
