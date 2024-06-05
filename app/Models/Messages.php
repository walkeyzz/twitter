<?php

namespace App\Models;

use CodeIgniter\Model;

class Messages extends BaseModel
{
    protected $table = 'messages';
    protected $primaryKey = 'messageID';
    protected $allowedFields = [
      'messageID',
      'message',
      'messageTo',
      'messageFrom',
      'status',
      'created_at'
    ];
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_at';

    protected $returnType = 'object'; // Default return type for database queries

    public function recentMessages($user_id)
    {
        // Fetch recent messages
        $builder = $this->table('messages');
        $builder->select('messages.*, users.username, users.profileImage');
        $builder->join('users', 'messages.messageFrom = users.user_id');
        $builder->where('messages.messageTo', $user_id);
        $builder->groupBy('messages.messageFrom'); // Group by sender
        $builder->orderBy('messages.messageID', 'DESC');
        $query = $builder->get();

        return $query->getResult(); // Return as an array of objects
    }

    public function deleteMsg($messageID, $user_id)
    {
        // Delete a message
        $builder = $this->table('messages');
        $builder->groupStart()
                    // First condition: messageID matches and messageFrom is the current user
                    ->where('messageID', $messageID)
                    ->where('messageFrom', $user_id)
                ->groupEnd() // End grouping for the first set of conditions

                ->orGroupStart() // Start a new group for the OR condition
                    // Second condition: messageID matches and messageTo is the current user
                    ->where('messageID', $messageID)
                    ->where('messageTo', $user_id)
                ->groupEnd(); // End grouping for the OR condition
        // $builder->where("(messageID = :messageID AND messageFrom = :user_id) OR (messageID = :messageID AND messageTo = :user_id)", ['messageID' => $messageID, 'user_id' => $user_id]);
        $builder->delete();
    }

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

    // public function getNotificationCount($user_id)
    // {
    //     // Initialize query builder
    //     $builder = $this->db->table('messages');
    //
    //     // SQL statement with a subquery and parameterized binding
    //     $sql = "COUNT(messageID) AS totalM,
    //            (SELECT COUNT(ID) FROM notification WHERE notificationFor = ? AND status = '0') AS totalN";
    //
    //     // Select statement with the query
    //     $builder->select($sql, false); // Set to 'false' for non-escaping
    //
    //     // Execute query with parameterized input
    //     $query = $builder->where('messageTo', $user_id)->get();
    //
    //     return $query->getRow(); // Get the first result row as an object
    // }

    public function messagesViewed($user_id)
    {
        // Mark all messages as viewed
        $builder = $this->table('messages');
        $builder->set('status', '1');
        $builder->where('messageTo', $user_id);
        $builder->where('status', '0');
        $builder->update();
    }

    public function notificationViewed($user_id)
    {
        // Mark all notifications as viewed
        $builder = \Config\Database::connect()->table('notification');
        $builder->set('status', '1');
        $builder->where('notificationFor', $user_id);
        $builder->update();
    }

    public function notification($user_id)
    {
        // Establish a database connection
        $db = \Config\Database::connect();

        // Create a builder for 'notification'
        $builder = $db->table('notification');

        // Build the query with multiple LEFT JOINs
        $builder->select('notification.*, users.*, tweets.*, likes.*, follow.*') // Adjust fields as needed
                ->join('users', 'notification.notificationFrom = users.user_id', 'left')
                ->join('tweets', 'notification.target = tweets.tweetID', 'left')
                ->join('likes', 'notification.target = likes.likeOn', 'left')
                ->join('follow', 'notification.notificationFrom = follow.sender AND notification.notificationFor = follow.receiver', 'left')
                ->where('notification.notificationFor', $user_id) // Filter by user_id
                ->where('notification.notificationFrom !=', $user_id) // Exclude self-generated notifications
                ->groupBy('ID'); // Group by ID

        // Execute the query and get the results as an array of objects
        $query = $builder->get();
        return $query->getResult(); // Return as an array of objects
    }

    public function sendNotification($target, $user_id, $type)
    {
        // Send a notification to a user
        $date = date('Y-m-d H:i:s');
        $data = [
            'notificationFor' => $target,
            'notificationFrom' => $user_id,
            'target' => $target,
            'type' => $type,
            'created_at' => $date
        ];

        $builder = \Config\Database::connect()->table('notification');
        $builder->insert($data);
    }
}
