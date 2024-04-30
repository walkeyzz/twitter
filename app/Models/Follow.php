<?php

namespace App\Models;

use CodeIgniter\Model;

class Follow extends Model
{
    protected $table = 'follow';
    protected $primaryKey = 'followID';
    protected $allowedFields = [
      'followID',
      'sender',
      'receiver'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}
