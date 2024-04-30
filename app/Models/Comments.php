<?php

namespace App\Models;

use CodeIgniter\Model;

class Comments extends Model
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
}
