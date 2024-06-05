<?php

namespace App\Models;

use CodeIgniter\Model;

class Notification extends BaseModel
{
    protected $table = 'notifications';
    protected $primaryKey = 'ID';
    protected $allowedFields = [
      'ID',
      'notificationFor',
      'notificationFrom',
      'target',
      'type',
      'status'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
}
