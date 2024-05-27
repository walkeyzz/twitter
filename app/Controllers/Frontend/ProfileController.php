<?php

namespace App\Controllers\Frontend;

class ProfileController extends BaseController
{
    public function index($username = false) {
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

      return view('profile', $this->data);
    }

    public function edit(){
      return view('profileedit', $this->data);
    }

    public function submit()
    {
      if(!empty($this->request->getPost('screenName'))){
        $screenName = $this->request->getPost('screenName');
    		$profileBio = $this->request->getPost('bio');
    		$country = $this->request->getPost('country');
        $website = $this->request->getPost('website');

  			if(strlen($screenName) > 20){
  				$this->data['error']  = "Name must be between in 6-20 characters";
  			}else if(strlen($profileBio) > 120){
  				$this->data['error'] = "Description is too long";
  			}else if(strlen($country) > 80){
  				$this->data['error'] = "Country name is too long";
  			}else {
  				 $this->users->update($this->user_id,
           array(
             'screenName' => $screenName,
             'bio' => $profileBio,
             'country' => $country,
             'website' => $website
           ));

  				 return redirect()->to('profile/'.$this->data['user']->username);
  			}
  		}else{
  			$this->data['error'] = "Name field can't be blank";
  		}

  		return view('profileedit', $this->data);
    }

   
}
