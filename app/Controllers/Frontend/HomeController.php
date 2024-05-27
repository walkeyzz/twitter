<?php

namespace App\Controllers\Frontend;

class HomeController extends BaseController
{
    public function index() {

      if(!$this->data['logged_user'])
  			return view('login/loginorsignup', $this->data);

      return view('home', $this->data);
    }
}
