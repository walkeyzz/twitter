<?php
use Config\Database;
use CodeIgniter\Database\BaseBuilder;

// Ensure the helper is not already loaded
if (!function_exists('getMessages')) {
    function getMessages($messageFrom, $user_id)
    {
      // Get database instance
      $db = Database::connect();
      $sql = "SELECT * FROM `messages` LEFT JOIN `users` ON `messageFrom` = `user_id` WHERE `messageFrom` =? AND `messageTo` =? OR `messageTo` =? AND `messageFrom` =?";
      $builder = $db->query($sql, [$messageFrom, $user_id, $messageFrom, $user_id]);

      $messages = $builder->getResult();
      $output = '';
      foreach ($messages as $message) {
        if ($message->messageFrom === $user_id) {
          $output .= '<div class="main-msg-body-right">
              <div class="main-msg">
                <div class="msg-img">
                  <a href="#"><img src="'.site_url().$message->profileImage.'"/></a>
                </div>
                <div class="msg">'.$message->message.'
                  <div class="msg-time">
                    '.timeAgo($message->created_at).'
                  </div>
                </div>
                <div class="msg-btn">
                  <a><i class="fa fa-ban" aria-hidden="true"></i></a>
                  <a class="deleteMsg" data-message="'.$message->messageID.'"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>
              </div>
            </div>';
        }else{
          $output .= '<div class="main-msg-body-left">
            <div class="main-msg-l">
              <div class="msg-img-l">
                <a href="#"><img src="'.site_url().$message->profileImage.'"/></a>
              </div>
              <div class="msg-l">'.$message->message.'
                <div class="msg-time-l">
                    '.timeAgo($message->created_at).'
                </div>
              </div>
              <div class="msg-btn-l">
                <a><i class="fa fa-ban" aria-hidden="true"></i></a>
                <a class="deleteMsg" data-message="'.$message->messageID.'"><i class="fa fa-trash" aria-hidden="true"></i></a>
              </div>
            </div>
          </div> ';
        }
      }

      return $output;
  }
}
