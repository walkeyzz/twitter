<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
      $this->call('CommentsSeeder');
      $this->call('FollowSeeder');
      $this->call('LikesSeeder');
      $this->call('MessagesSeeder');
      $this->call('NotificationSeeder');
      $this->call('TrendsSeeder');
      $this->call('TweetsSeeder');
      $this->call('UsersSeeder');
    }
}
