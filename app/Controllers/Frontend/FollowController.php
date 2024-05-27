<?php

namespace App\Controllers\Frontend;

class FollowController extends BaseController
{
    public function following($username = false) {
      if($username != false){
        $this->data['username'] = $this->users->checkInput($username);
        $profileId = $this->users->userIdByUsername($username);
        $this->data['profileId'] = $profileId;
        $this->data['profileData'] = $this->users->userData($profileId);
        $this->data['tweets_count'] = $this->tweets->countTweets($profileId);

        if (!$this->data['profileData']) {
          return redirect()->to('/');
        }
      }

      return view('following', $this->data);
    }

    public function followers($username = false) {
      if($username != false){
        $this->data['username'] = $this->users->checkInput($username);
        $profileId = $this->users->userIdByUsername($username);
        $this->data['profileId'] = $profileId;
        $this->data['profileData'] = $this->users->userData($profileId);
        $this->data['tweets_count'] = $this->tweets->countTweets($profileId);

        if (!$this->data['profileData']) {
          return redirect()->to('/');
        }
      }

      return view('followers', $this->data);
    }

}
