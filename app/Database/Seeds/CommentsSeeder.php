<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CommentsSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('comments');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\CommentsData.json');
      $data = json_decode($jsonContent, true);
      if (!empty($data)) {
          $builder->insertBatch($data);
      }
    }
}
