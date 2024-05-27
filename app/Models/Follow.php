<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Messages;

class Follow extends BaseModel
{
    protected $table = 'follow';
    protected $primaryKey = 'followID';
    protected $allowedFields = [
      'followID',
      'sender',
      'receiver',
      'created_at'
    ];

    protected $returnType = 'object'; // Return data as objects

    public function __construct()
    {
        parent::__construct();
        $this->message = new Messages(); // Create an instance of the Message model
    }

    public function follow($followID, $user_id, $profileID)
    {
        // Follow a user
        $data = [
            'sender' => $user_id,
            'receiver' => $followID,
            'created_at' => date("Y-m-d H:i:s")
        ];
        $this->insert($data);

        $this->addFollowCount($followID, $user_id);

        $db = \Config\Database::connect();
        $sql = 'SELECT `user_id`, `following`, `followers` FROM `users` LEFT JOIN `follow` ON `sender` = ? AND CASE WHEN `receiver` = ? THEN `sender` = `user_id` END WHERE `user_id` = ?';
        $builder = $db->query($sql, [$user_id, $user_id, $profileID]);

        $query = $builder->getResultArray();

        $this->message->sendNotification($followID, $user_id, 'follow');
        return $query; // Return a single row
    }

    public function unfollow($followID, $user_id, $profileID)
    {
        // Unfollow a user
        $this->where('sender', $user_id)
             ->where('receiver', $followID)
             ->delete();

        $this->removeFollowCount($followID, $user_id, $profileID);

        // Create a database instance
        $db = \Config\Database::connect();
        // Get a table builder for 'users'
        // $builder = $db->table('users');

        $sql = 'SELECT `user_id`, `following`, `followers` FROM `users` LEFT JOIN `follow` ON `sender` = ? AND CASE WHEN `receiver` = ? THEN `sender` = `user_id` END WHERE `user_id` = ?';
        $builder = $db->query($sql, [$user_id, $user_id, $profileID]);

        $query = $builder->getResultArray();


        return $query; // Fetch a single row as an associative array
    }

    public function addFollowCount($followID, $user_id)
    {
        // Increment follow counts
        $builder = \Config\Database::connect()->table('users');
        $builder->set('following', 'following + 1', false)
                ->where('user_id', $user_id)
                ->update();

        $builder->set('followers', 'followers + 1', false)
                ->where('user_id', $followID)
                ->update();
    }

    public function removeFollowCount($followID, $user_id)
    {
        // Decrement follow counts
        $builder = \Config\Database::connect()->table('users');
        $builder->set('following', 'following - 1', false)
                ->where('user_id', $user_id)
                ->update();

        $builder->set('followers', 'followers - 1', false)
                ->where('user_id', $followID)
                ->update();
    }

    public function followingList($profileID)
    {
        // Get list of users a user is following
        $builder = $this->table('follow');
        $builder->select('users.*');
        $builder->join('users', 'users.user_id = follow.receiver');
        $builder->where('follow.sender', $profileID);
        $query = $builder->get();

        return $query->getResult();
    }

    public function followersList($profileID)
    {
        // Get list of users following a specific user
        $builder = $this->table('follow');
        $builder->select('users.*');
        $builder->join('users', 'users.user_id = follow.sender');
        $builder->where('follow.receiver', $profileID);
        $query = $builder->get();

        return $query->getResult();
    }

    public function whoToFollow($user_id)
    {
        // Suggest users to follow
        $db = \Config\Database::connect();
        $builder = $db->table('users');

        // Build a raw SQL condition for the subquery
        $subquery = "(SELECT receiver FROM follow WHERE sender = " . (int) $user_id . ")";

        $builder->where('user_id !=', $user_id);
        $builder->where("user_id NOT IN {$subquery}"); // Using the raw subquery
        $builder->orderBy('RAND()');
        $builder->limit(3);

        $query = $builder->get();
        return $query->getResult();
    }

}
