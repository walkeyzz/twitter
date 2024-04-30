<?php

namespace App\Models;

use CodeIgniter\Model;

class Messages extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'messageID';
    protected $allowedFields = [
      'messageID',
      'message',
      'messageTo',
      'messageFrom',
      'status'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';

    public function getNotificationCount($user_id)
    {
        // Start a subquery for notifications
        $notificationsSubQuery = $this->db->table('notification')
                                          ->select('COUNT(ID)', false)
                                          ->where('notificationFor', $user_id)
                                          ->where('status', '0');

        // Prepare the main query
        $builder = $this->select('COUNT(messageID) AS totalM', false)
                        ->select("({$notificationsSubQuery->getCompiledSelect()}) AS totalN", false)
                        ->where('messageTo', $user_id)
                        ->where('status', '0');

        // Get the result
        $query = $builder->get();
        return $query->getRow();
    }
}
