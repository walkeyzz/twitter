<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TrendsSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('trends');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\TrendsData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
