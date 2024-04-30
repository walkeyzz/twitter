<?php

namespace App\Models;

use CodeIgniter\Model;

class Trends extends Model
{
    protected $table = 'trends';
    protected $primaryKey = 'trendID';
    protected $allowedFields = [
      'trendID',
      'hashtag'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}
