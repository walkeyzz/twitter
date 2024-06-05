<?php

namespace App\Controllers\Frontend;

class NotificationsController extends BaseController
{
    public function index($username = false) {
      $this->messages->notificationViewed($this->user_id);
      $this->data['notification'] = $this->messages->notification($this->user_id);

      return view('notifications', $this->data);
    }

}
