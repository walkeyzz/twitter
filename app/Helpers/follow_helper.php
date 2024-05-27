<?php
use Config\Database;
use Config\Services;


if (!function_exists('checkFollow')) {
  function checkFollow($followerID, $user_id)
  {
      // Check if a user is following another user
      $db = Database::connect();
      $builder = $db->table('follow');
      $builder->where('sender', $user_id);
      $builder->where('receiver', $followerID);
      $query = $builder->get();

      return $query->getRow(); // Return single row
  }
}





