<?php

namespace App\Controllers\Frontend;

class TweetController extends BaseController
{
    public function index() {

      if(!$this->session->has('user_id'))
  			return view('login/loginorsignup', $this->data);

      // $user_id = $this->session->get('user_id');
      // $this->data['user'] = $this->users->UserData($user_id);
      // $this->data['notify'] = $this->Messages->getNotificationCount($user_id);

      return view('home', $this->data);
    }

    public function submit() {

      $status = $this->request->getPost('status');
      $tweetImage = '';
      // $user_id = $this->session->get('user_id'); // Assuming the user is logged in and has a session

      $this->data['error'] = '';

      if (!empty($status) || !empty($this->request->getFile('file')->getName())) {
          if (!empty($this->request->getFile('file'))) {
              // $tweetImage = $this->users->uploadImage($this->request->getFile('file'));
              $tweet_image = $this->request->getFile('file');
              $allowedExtensions = ['jpg', 'jpeg', 'png'];
              $maxFileSize = 2097152; // 2 MB in bytes

              // Check if a profile cover image is uploaded
              if ($tweet_image && $tweet_image->isValid() && !$tweet_image->hasMoved())
              {
                if ($tweet_image->getSize() < $maxFileSize)
                {
                  // Check file extension
                  $fileExtension = $tweet_image->getExtension();
                  if (in_array(strtolower($fileExtension), $allowedExtensions))
                  {
                    // Upload the file to a specific path
                    $newName = $tweet_image->getRandomName();
                    $fileRoot = $tweet_image->move(FCPATH . 'uploads/tweets', $newName);

                    if ($fileRoot)
                    {
                       // If the file is successfully uploaded, update the database
                       $tweetImage = 'uploads/tweets/' . $newName;
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
          }

          if (strlen($status) > 1000) {
              $this->data['error'] = 'The text of your tweet is too long';
          }

          if ($this->data['error'] === '') {
              $tweetData = [
                  'status' => $status,
                  'tweetBy' => $this->user_id,
                  'tweetImage' => $tweetImage,
                  'created_at' => date( 'Y-m-d H:i:s' )
              ];

              $tweet_id = $this->tweets->createRecord('tweets', $tweetData);

              preg_match_all('/#+([a-zA-Z0-9_]+)/i', $status, $hashtag);

              if (!empty($hashtag[1])) {
                  $this->tweets->addTrend($status);
              }

              $this->users->addMention($status, $this->user_id, $tweet_id);
              return redirect()->to('/'); // Redirect to home page on success
          }
      } else {
          $this->data['error'] = 'Type or choose image to tweet';
      }

      return view('home', $this->data); // Return to form with error message
    }

}
