<?php

namespace App\Models;

use CodeIgniter\Model;

class Tweets extends Model
{
    protected $table = 'tweets';
    protected $primaryKey = 'tweetID';
    protected $allowedFields = [
      'tweetID',
      'status',
      'tweetBy',
      'retweetID',
      'retweetBy',
      'tweetImage',
      'likesCount',
      'retweetCount',
      'retweetMsg'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}
