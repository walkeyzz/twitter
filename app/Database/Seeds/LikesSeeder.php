<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LikesSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('likes');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\LikesData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
