<?php

namespace App\Controllers\Frontend;

class Home extends BaseController
{
    public function index() {
      helper(['form', 'url']);

      if(!$this->session->has('user_id'))
  			return view('login/loginorsignup', $this->data);

      $user_id = $this->session->get('user_id');
      $this->data['user'] = $this->users->UserData($user_id);
      $this->data['notify'] = $this->Messages->getNotificationCount($user_id);

      return view('home', $this->data);
    }
}
