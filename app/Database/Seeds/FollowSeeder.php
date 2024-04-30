<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FollowSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('follow');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\FollowData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
