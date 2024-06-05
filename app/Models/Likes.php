<?php

namespace App\Models;

use CodeIgniter\Model;

class Likes extends BaseModel
{
    protected $table = 'likes';
    protected $primaryKey = 'likeID';
    protected $allowedFields = [
      'likeID',
      'likeBy',
      'likeOn'
    ];

    protected $returnType = 'object'; // Return data as objects

    public function countLikes($user_id) {
        $builder = $this->table('likes');
        $builder->selectCount('likeID', 'totalLikes'); // Alias 'likeID' count as 'totalLikes'
        $builder->where('likeBy', $user_id);
        $query = $builder->get();

        // Retrieve a single row as an object and access 'totalLikes'
        $row = $query->getRow();
        return $row ? $row->totalLikes : 0;
    }

    public function insertLike($user_id, $tweet_id)
    {
        // Insert a record into the 'likes' table
        $builder = $this->table('likes');
        $builder->insert(['likeBy' => $user_id, 'likeOn' => $tweet_id]);
    }

    public function deleteLike($user_id, $tweet_id)
    {
        // Delete a record from the 'likes' table
        $builder = $this->table('likes');
        $builder->where('likeBy', $user_id);
        $builder->where('likeOn', $tweet_id);
        $builder->delete();
    }
}
