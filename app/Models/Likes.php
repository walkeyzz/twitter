<?php

namespace App\Models;

use CodeIgniter\Model;

class Likes extends Model
{
    protected $table = 'likes';
    protected $primaryKey = 'likeID';
    protected $allowedFields = [
      'likeID',
      'likeBy',
      'likeOn'
    ];
}
