<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
      $db = \Config\Database::connect();
      $builder = $db->table('users');
      $jsonContent = file_get_contents(APPPATH . 'Database\Data\UsersData.json');
      $data = json_decode($jsonContent, true);
      print_r($data);
      if (!empty($data)) {
          $builder->insertBatch($data);
      } else {
        echo 'here';
        json_last_error();
      }
    }
}
