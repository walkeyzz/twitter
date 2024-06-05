<?php

namespace App\Models;

use CodeIgniter\Model;

class Comments extends BaseModel
{
    protected $table = 'comments';
    protected $primaryKey = 'commentID';
    protected $allowedFields = [
      'commentID',
      'comment',
      'commentOn',
      'commentBy'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $returnType = 'object'; // Default return type for database queries

    public function comments($tweet_id) {
        $builder = $this->table('comments');
        $builder->select('comments.*, users.*'); // Select specific fields as needed
        $builder->join('users', 'users.user_id = comments.commentBy');
        $builder->where('comments.commentOn', $tweet_id);
        $query = $builder->get();

        // Return the result set as an array of objects
        return $query->getResult();
    }
}
