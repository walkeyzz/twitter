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

    public function editCover()
    {
      $profileCover = $this->request->getFile('profileCover');
      $allowedExtensions = ['jpg', 'jpeg', 'png'];
      $maxFileSize = 2097152; // 2 MB in bytes

      // Check if a profile cover image is uploaded
      if ($profileCover && $profileCover->isValid() && !$profileCover->hasMoved())
      {
        if ($profileCover->getSize() < $maxFileSize)
        {
          // Check file extension
          $fileExtension = $profileCover->getExtension();
          if (in_array(strtolower($fileExtension), $allowedExtensions))
          {
            // Upload the file to a specific path
            $newName = $profileCover->getRandomName();
            $fileRoot = $profileCover->move(FCPATH . 'uploads/cover', $newName);

            if ($fileRoot)
            {
               // If the file is successfully uploaded, update the database
               $this->users->update($this->user_id, ['profileCover' => 'uploads/cover/' . $newName]);
            }
          } else {
            // Return an error or handle as needed
            $this->data['imgError'] = "Invalid file type. Allowed extensions are jpg, jpeg, png.";
          }
        } else {
          // Return an error or handle as needed
          $this->data['imgError'] = "File is too large. Maximum allowed size is 2 MB.";
        }
      }
      return redirect()->to('profileedit');
    }

    public function editProfileImage()
    {
      $profileImage = $this->request->getFile('profileImage');
      $allowedExtensions = ['jpg', 'jpeg', 'png'];
      $maxFileSize = 2097152; // 2 MB in bytes

      if ($profileImage && $profileImage->isValid() && !$profileImage->hasMoved()) {
        if ($profileImage->getSize() < $maxFileSize) {
          // Check file extension
          $fileExtension = $profileImage->getExtension();
          if (in_array(strtolower($fileExtension), $allowedExtensions)) {

            // Upload the file to a specific path
            $newName = $profileImage->getRandomName();
            $fileRoot = $profileImage->move(FCPATH . 'uploads/profile', $newName);

            if ($fileRoot) {
               // If the file is successfully uploaded, update the database
               $this->users->update($this->user_id, ['profileImage' => 'uploads/profile/' . $newName]);
            }

          } else {
            // Return an error or handle as needed
            $this->data['imgError'] = "Invalid file type. Allowed extensions are jpg, jpeg, png.";
          }
        } else {
          // Return an error or handle as needed
          $this->data['imgError'] = "File is too large. Maximum allowed size is 2 MB.";
        }
      }
      return redirect()->to('profileedit');
    }

}
