<?php

namespace App\Controllers\Frontend;

class AjaxController extends BaseController
{

  public function addTweet()
  {
    $result = [];

      // Check if there's POST data
      if ($this->request->getMethod() === 'POST') {
          $status = $this->request->getPost('status'); // Get the tweet status
          $tweetImage = ''; // Initialize tweet image variable
          $user_id = $this->user_id; // Get user ID from session
          $file = $this->request->getFile('file');

          if (!empty($status) || !empty($file)) {
              $allowedExtensions = ['jpg', 'jpeg', 'png'];
              $maxFileSize = 2097152; // 2 MB in bytes

              if ($file && $file->isValid() && !$file->hasMoved()) {
                if ($file->getSize() < $maxFileSize) {
                  // Check file extension
                  $fileExtension = $file->getExtension();
                  if (in_array(strtolower($fileExtension), $allowedExtensions)) {

                    // Upload the file to a specific path
                    $newName = $file->getRandomName();
                    $fileRoot = $file->move(FCPATH . 'uploads/tweets', $newName);

                    if ($fileRoot) {
                      // If the file is successfully uploaded, update the database
                      $tweetImage = 'uploads/tweets/' . $newName;
                    }

                  } else {
                    // Return an error or handle as needed
                    $result['imgError'] = "Invalid file type. Allowed extensions are jpg, jpeg, png.";
                  }
                } else {
                  // Return an error or handle as needed
                  $result['imgError'] = "File is too large. Maximum allowed size is 2 MB.";
                }
              }

              if (strlen($status) > 140) {
                  $result['error'] = "The text of your tweet is too long";
                  return $this->response->setJSON($result);
              }

              // Create the tweet (assumes a TweetModel with a create method)
              $tweet_data = [
                  'status' => $status,
                  'tweetBy' => $user_id,
                  'tweetImage' => $tweetImage,
                  'created_at' => date('Y-m-d H:i:s')
              ];


              $tweet_id = $this->tweets->createRecord('tweets', $tweet_data);

              preg_match_all("/#+([a-zA-Z0-9_]+)/i", $status, $hashtag);

        			if(!empty($hashtag)){
        				$this->trends->addTrend($status);
        			}
        			$this->users->addMention($status, $user_id, $tweet_id);

              $result['success'] = "Your Tweet has been posted";
              return $this->response->setJSON($result); // Return success
          } else {
              $result['error'] = "Please type or choose image to tweet";
              return $this->response->setJSON($result);
          }
      }
  }

  public function comment()
  {
      // Check if there's POST data
      if ($this->request->getMethod() === 'POST')
      {
          $comment = $this->request->getPost('comment');
          if (isset($comment) && !empty($comment))
          {
              $user_id = $this->user_id;
  	          $tweetID = $this->request->getPost('tweet_id');

              $comment_data = array(
                'comment' => $comment,
                'commentOn' => $tweetID,
                'commentBy' => $user_id
              );

              $this->comments->createRecord('comments', $comment_data);
          		$comments = $this->comments->comments($tweetID);
          		$tweet = $this->tweets->tweetPopup($tweetID);

              $output = '';

              foreach ($comments as $comment) {
                  $output .= '
          	 			   <div class="tweet-show-popup-comment-box">
          						<div class="tweet-show-popup-comment-inner">
          							<div class="tweet-show-popup-comment-head">
          								<div class="tweet-show-popup-comment-head-left">
          									 <div class="tweet-show-popup-comment-img">
          									 	<img src="'.site_url().$comment->profileImage.'">
          									 </div>
          								</div>
          								<div class="tweet-show-popup-comment-head-right">
          									  <div class="tweet-show-popup-comment-name-box">
          									 	<div class="tweet-show-popup-comment-name-box-name">
          									 		<a href="'.site_url().$comment->username.'">'.$comment->screenName.'</a>
          									 	</div>
          									 	<div class="tweet-show-popup-comment-name-box-tname">
          									 		<a href="'.site_url().$comment->username.'">@'.$comment->username.'</a>
          									 	</div>
          									 </div>
          									 <div class="tweet-show-popup-comment-right-tweet">
          									 		<p><a href="'.site_url().$tweet->username.'">@'.$tweet->username.'</a> '.$comment->comment.'</p>
          									 </div>
          								 	<div class="tweet-show-popup-footer-menu">
          										<ul>
          											<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
          											<li><a href="#"><i class="fa fa-heart-o" aria-hidden="true"></i></a></li>
          											'.(($comment->commentBy === $user_id) ?
          											'<li>
          												<a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
          												<ul>
          												  <li><label class="deleteComment" data-tweet="'.$tweet->tweetID.'" data-comment="'.$comment->commentID.'">Delete Tweet</label></li>
          												</ul>
          											</li>' : '').'
          										</ul>
          									</div>
          								</div>
          							</div>
          						</div>
          						<!--TWEET SHOW POPUP COMMENT inner END-->
          						</div>
          						';
        	 		}

              return $this->response->setBody($output); // Return the generated HTML
          }
      }
  }

  public function deleteComment()
  {
      if ($this->request->getMethod() !== 'POST')
      {
          return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
      }

      // Validate POST data
      $commentID = $this->request->getPost('deleteComment');
      $tweetID = $this->request->getPost('tweet_id');
      $user_id = $this->user_id; // Get the current user ID from session

      if (empty($commentID) || empty($tweetID)) {
          return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request parameters']);
      }

      // Ensure the comment to be deleted belongs to the current user
      $this->comments->deleteRecord('comments', array('commentBy' => $user_id, 'commentID' => $commentID));
      return $this->response->setJSON(['success' => 'Comment deleted successfully']);
  }

  public function deleteTweet()
  {

      if ($this->request->getMethod() === 'POST') {
          $showpopup = $this->request->getPost('showpopup');
          $deleteTweet = $this->request->getPost('deleteTweet'); // Get the tweet ID
          $user_id = $this->user_id; // Get the current user's ID from session

          if (isset($deleteTweet) && !empty($deleteTweet))
          {
              //get tweet data from tweet id
              $tweet_id = $deleteTweet;
              $tweet = $this->tweets->tweetPopup($tweet_id);

              //delete the tweet from database
              $this->tweets->deleteRecord('tweets', array(
                  'tweetID' => $tweet_id,
                  'tweetBy' => $user_id
              ));

              //check if tweet has image
              if(!empty($tweet->tweetImage))
              {
                  //create link for tweet image to delete from
                  $imageLink = FCPATH . $tweet->tweetImage;
                  //delete the file
                  unlink($imageLink);
              }
          }

          if (isset($showpopup) && !empty($showpopup))
          {
              $tweet_id  = $showpopup;
              $tweet = $this->tweets->tweetPopup($tweet_id);
              $output = '
              <div class="retweet-popup">
                <div class="wrap5">
                  <div class="retweet-popup-body-wrap">
                    <div class="retweet-popup-heading">
                      <h3>Are you sure you want to delete this Tweet?</h3>
                      <span><button class="close-retweet-popup"><i class="fa fa-times" aria-hidden="true"></i></button></span>
                    </div>
                     <div class="retweet-popup-inner-body">
                      <div class="retweet-popup-inner-body-inner">
                        <div class="retweet-popup-comment-wrap">
                           <div class="retweet-popup-comment-head">
                            <img src="'. site_url().$tweet->profileImage . '"/>
                           </div>
                           <div class="retweet-popup-comment-right-wrap">
                             <div class="retweet-popup-comment-headline">
                              <a>'. $tweet->screenName .'</a><span>‏@' .$tweet->username . ' ' . $tweet->created_at .'</span>
                             </div>
                             <div class="retweet-popup-comment-body">
                               '. $tweet->status . ' ' .$tweet->tweetImage . '
                             </div>
                           </div>
                        </div>
                       </div>
                    </div>
                    <div class="retweet-popup-footer">
                      <div class="retweet-popup-footer-right">
                        <button class="cancel-it f-btn">Cancel</button><button class="delete-it" data-tweet="' . $tweet->tweetID . '" type="submit">Delete</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>';

              return $this->response->setBody($output); // Return the generated HTML
          }
      } else {
          return $this->response->setStatusCode(405)->setJSON(['error' => $method]);
      }
  }

  public function fetchPosts()
  {
      if ($this->request->getMethod() === 'POST')
      {
          $user_id = $this->user_id;
          $fetchPost = $this->request->getPost('fetchPost');
          if(isset($fetchPost) && !empty($fetchPost))
          {
              $limit   = (int) trim($fetchPost);
		          $output = returntweets($user_id, $limit);
              return $this->response->setBody($output);
          }
      } else {
          return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
      }
  }

  public function follow()
  {
    if ($this->request->getMethod() === 'POST')
    {
        $unfollow = $this->request->getPost('unfollow');
        $follow = $this->request->getPost('follow');
        $profile = $this->request->getPost('profile');
        $user_id = $this->user_id;
        if(isset($unfollow) && !empty($unfollow))
        {
            $followID = $unfollow;
            $profileID = $profile;
            $unfollow = $this->follow->unfollow($followID, $user_id, $profileID);
            return $this->response->setJSON($unfollow);
        }

        if(isset($follow) && !empty($follow))
        {
            $followID = $follow;
            $profileID = $profile;
            $follow = $this->follow->follow($followID, $user_id, $profileID);
            return $this->response->setJSON($follow);
        }

    } else {
        return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function getHashtag()
  {
      if ($this->request->getMethod() === 'POST')
      {
        $hashtag = $this->request->getPost('hashtag');
          if(isset($hashtag)){
            if(!empty($hashtag)){
              $mension = $this->request->getPost('mension');

              if(substr($hashtag, 0,1) === '#'){
                $trend   = str_replace('#', '', $hashtag);
                $trend   = $this->tweets->getTrendByHash($trend);

                $output = '';
                foreach ($trend as $hashtag) {
                  $output .= '<li><a href="#"><span class="getValue">#'.$hashtag->hashtag.'</span></a></li>';
                }
                return $this->response->setBody($output);
              }

              if(substr($mension, 0,1) === '@'){
                $mension = str_replace('@', '', $mension);
                $mensions = $this->users->getMention($mension);
                $output = '';
                foreach ($mensions as $mension) {
                  $output .= '<li><div class="nav-right-down-inner">
                    <div class="nav-right-down-left">
                      <span><img src="'.site_url().$mension->profileImage.'"></span>
                    </div>
                    <div class="nav-right-down-right">
                      <div class="nav-right-down-right-headline">
                        <a>'.$mension->screenName.'</a><span class="getValue">@'.$mension->username.'</span>
                      </div>
                    </div>
                  </div><!--nav-right-down-inner end-here-->
                  </li>';
                }
                return $this->response->setBody($output);
              }
            }
         }
      } else {
          return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
      }
  }

  public function imagePopup()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $showImage = $this->request->getPost('showImage');
      if(isset($showImage) && !empty($showImage)){
    		$tweet_id   = $showImage;
     		$user_id    = $this->user_id;
    		$tweet      = $this->tweets->tweetPopup($tweet_id);
    		$likes      = likes($user_id,$tweet_id);
    		$retweet    = checkRetweet($tweet_id,$user_id);
    	}

      $output = '
      <div class="img-popup">
        <div class="wrap6">
        <span class="colose">
        	<button class="close-imagePopup"><i class="fa fa-times" aria-hidden="true"></i></button>
        </span>
        <div class="img-popup-wrap">
        	<div class="img-popup-body">
        		<img src="'.site_url().$tweet->tweetImage.'"/>
        	</div>
        	<div class="img-popup-footer">
        		<div class="img-popup-tweet-wrap">
        			<div class="img-popup-tweet-wrap-inner">
        				<div class="img-popup-tweet-left">
        					<img src="'. site_url().$tweet->profileImage .'"/>
        				</div>
        				<div class="img-popup-tweet-right">
        					<div class="img-popup-tweet-right-headline">
        						<a href="'. site_url().$tweet->username .'">'.$tweet->screenName.'</a><span>@'. $tweet->username . ' - ' .timeAgo($tweet->created_at) .'</span>
        					</div>
        					<div class="img-popup-tweet-right-body">
        						'.getTweetLinks($tweet->status).'
        					</div>
        				</div>
        			</div>
        		</div>
        		<div class="img-popup-tweet-menu">
        			<div class="img-popup-tweet-menu-inner">
        				<ul>
        						'.((loggedIn()) ?   '
        								<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
        								<li>'.(($tweet->tweetID === isset($retweet['retweetID']) OR $user_id === isset($retweet['retweetBy'])) ? '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>' : '<button class="retweet" data-tweet="'
                        .$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>').'</li>
        								<li>'.((isset($likes['likeOn']) == $tweet->tweetID) ? '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '').'</span></button>' : '<button class="like-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="likesCounter">'.
                        (($tweet->likesCount > 0) ? $tweet->likesCount : '').'</span></button>').'</li>
        								'.(($tweet->tweetBy === $user_id) ? '
        								<li>
        									<a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
        									<ul>
        									  <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>
        									</ul>
        								</li>' : '').'
        							' : '
        								<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
        								<li><button><i class="fa fa-retweet" aria-hidden="true"></i></button></li>
        								<li><button><i class="fa fa-heart-o" aria-hidden="true"></i></button></li>
        							').'
        						</ul>
        			</div>
        		</div>
        	</div>
        </div>
        </div>
        </div><!-- Image PopUp ends--> ';

      return $this->response->setBody($output);
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function like()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $like = $this->request->getPost('like');
      $user_id = $this->user_id;
      $get_id = $this->request->getPost('user_id');
      $unlike = $this->request->getPost('unlike');


      if(isset($like) && !empty($like)){
      	$tweet_id = $like;
      	$this->tweets->addLike($user_id, $tweet_id, $get_id);
      }

      if(isset($unlike) && !empty($unlike)){
      	$tweet_id = $unlike;
      	$this->tweets->unLike($user_id, $tweet_id, $get_id);
      }

      $file = $this->request->getFile('file');

      if(isset($file)){

        if ($file && $file->isValid() && !$file->hasMoved()) {
          if ($file->getSize() < $maxFileSize) {
            // Check file extension
            $fileExtension = $file->getExtension();
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {

              // Upload the file to a specific path
              $newName = $file->getRandomName();
              $fileRoot = $file->move(FCPATH . 'uploads/tweets', $newName);

              if ($fileRoot) {
                 // If the file is successfully uploaded, update the database
                 $this->users->update($user_id, ['profileImage' => 'uploads/profile/' . $newName]);
              }

            } else {
              // Return an error or handle as needed
              $result['imgError'] = "Invalid file type. Allowed extensions are jpg, jpeg, png.";
            }
          } else {
            // Return an error or handle as needed
            $result['imgError'] = "File is too large. Maximum allowed size is 2 MB.";
          }
        }

        return $this->response->setJSON($result);
      }

    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function messages()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $user_id = $this->user_id;
      $deleteMsg = $this->request->getPost('deleteMsg');
      $sendMessage = $this->request->getPost('sendMessage');
      $showChatMessage = $this->request->getPost('showChatMessage');
      $showMessage = $this->request->getPost('showMessage');
      $showChatPopup = $this->request->getPost('showChatPopup');

      if(isset($deleteMsg) && !empty($deleteMsg))
      {
    		$messageID = $deleteMsg;
    		$this->messages->deleteMsg($messageID, $user_id);
    	}

      if(isset($sendMessage) && !empty($sendMessage))
      {
     		$message  = $this->users->checkInput($sendMessage);
     		$get_id   = $this->request->getPost('get_id');

     		if(!empty($message)){
     			$date = date('Y-m-d H:i:s');
     			$this->messages->createRecord('messages', array('messageTo' => $get_id, 'messageFrom' => $user_id, 'message' => $message, 'created_at' => $date));
     		}
     	}

      if(isset($showChatMessage) && !empty($showChatMessage))
      {
    		$messageFrom = $showChatMessage;
    		$message = getMessages($messageFrom, $user_id);
        return $this->response->setBody($message);
    	}

    	if(isset($showMessage) && !empty($showMessage)){
    		$messages = $this->messages->recentMessages($user_id);
    		$this->messages->messagesViewed($user_id);
        $output = '
        <div class="popup-message-wrap">
  			<input id="popup-message-tweet" type="checkbox" checked="unchecked"/>
  			<div class="wrap2">
  			<div class="message-send">
  				<div class="message-header">
  					<div class="message-h-left">
  						<label for="mass"><i class="fa fa-angle-left" aria-hidden="true"></i></label>
  					</div>
  					<div class="message-h-cen">
  						<h4>New message</h4>
  					</div>
  					<div class="message-h-right">
  						<label for="popup-message-tweet" ><i class="fa fa-times" aria-hidden="true"></i></label>
  					</div>
  				</div>
  				<div class="form-group p-2">
  					<h4>Send message to:</h4>
  				  	<input type="text" placeholder="Search people" class="search-user form-control"/>
  					<ul class="search-result down">

  					</ul>
  				</div>
  				<div class="message-body">
  					<h4>Recent</h4>
  					<div class="message-recent">';

  					foreach($messages as $message)
            {
  						$output .='<!--Direct Messages-->
  						<div class="people-message" data-user="'.$message->user_id.'">
  							<div class="people-inner">
  								<div class="people-img">
  									<img src="'.site_url().$message->profileImage.'"/>
  								</div>
  								<div class="name-right2">
  									<span><a href="#">'.$message->screenName.'</a></span><span>@'.$message->username.'</span>
  								</div>

  								<span>
  									'.timeAgo($message->created_at).'
  								</span>
  							</div>
  						</div>
  						<!--Direct Messages-->';
  					}

  					$output .= '</div>
  				</div>
  				<!--message FOOTER-->
  <!--
  				<div class="message-footer">
  					<div class="ms-fo-right">
  						<label>Next</label>
  					</div>
  				</div> message FOOTER END
  -->
  			</div><!-- MESSGAE send ENDS-->
  			<script type="text/javascript" src="'.site_url().'/assets/js/search.js"></script>

  				<input id="mass" type="checkbox" checked="unchecked" />
  				<div class="back">
  					<div class="back-header">
  						<div class="back-left">
  							Direct message
  						</div>
  						<div class="back-right">
  							<label for="mass"  class="new-message-btn">New messages</label>
  							<label for="popup-message-tweet"><i class="fa fa-times" aria-hidden="true"></i></label>
  						</div>
  					</div>
  					<div class="back-inner">
  						<div class="back-body">';
  						foreach($messages as $message)
              {
				        $output .= '<!--Direct Messages-->
  							<div class="people-message" data-user="'.$message->user_id.'">
  								<div class="people-inner">
  									<div class="people-img">
  										<img src="'.site_url().$message->profileImage.'"/>
  									</div>
  									<div class="name-right2">
  										<span><a href="#">'.$message->screenName.'</a></span><span>@'.$message->username.'</span>
  									</div>
  									<div class="msg-box">
  										'.$message->message.'
  									</div>

  									<span>
  										'.timeAgo($message->created_at).'
  									</span>
  								</div>
  							</div>
  							<!--Direct Messages-->';
  						}

  						$output .= '</div>
  					</div>
  					<div class="back-footer">

  					</div>
  				</div>
  			</div>
  			</div>';

        return $this->response->setBody($output);

      }

      if(isset($showChatPopup) && !empty($showChatPopup)){
    		$messageFrom = $showChatPopup;
    		$user = $this->users->userData($messageFrom);
        $output = '
        <div class="popup-message-body-wrap">
    			<input id="popup-message-tweet" type="checkbox" checked="unchecked"/>
    			<input id="message-body" type="checkbox" checked="unchecked"/>
    			<div class="wrap3">
    			<div class="message-send2">
    				<div class="message-header2">
    					<div class="message-h-left">
    						<label class="back-messages" for="mass"><i class="fa fa-angle-left" aria-hidden="true"></i></label>
    					</div>
    					<div class="message-h-cen">
    						<div class="message-head-img">
    						  <img src="'.site_url().$user->profileImage.'"/><h4>Messages</h4>
    						</div>
    					</div>
    					<div class="message-h-right">
    					  <label class="close-msgPopup" for="message-body" ><i class="fa fa-times" aria-hidden="true"></i></label>
    					</div>
    				</div>
    				<div class="message-del">
    					<div class="message-del-inner">
    						<h4>Are you sure you want to delete this message? </h4>
    						<div class="message-del-box">
    							<span>
    								<button class="cancel mb-2" value="Cancel">Cancel</button>
    							</span>
    							<span>
    								<button class="delete mb-2" value="Delete">Delete</button>
    							</span>
    						</div>
    					</div>
    				</div>
    				<div class="main-msg-wrap">
    			      <div id="chat" class="main-msg-inner">

    			 	  </div>
    				</div>
    				<div class="main-msg-footer">
    					<div class="main-msg-footer-inner form-group">
    						<ul>
    							<li><textarea class="form-control" id="msg" name="msg" cols="100%" placeholder="Write some thing!"></textarea></li>
    							<li><input id="msg-upload" type="file" value="upload"/><label for="msg-upload"><i class="fa fa-camera mt-2" aria-hidden="true"></i></label></li>
    							<li><input class="mt-2"id="send" data-user="'.$messageFrom.'" type="submit" value="Send"/></li>
    						</ul>
    					</div>
    				</div>
     			 </div> <!--MASSGAE send ENDS-->
    			</div> <!--wrap 3 end-->
    			</div><!--POP UP message WRAP END-->';

          return $this->response->setBody($output);
    	}

    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function notification()
  {
    if ($this->request->getMethod() === 'GET')
    {
      $showNotification = $this->request->getGet('showNotification');
      if (isset($_GET['showNotification']) && !empty($_GET['showNotification']))
      {
        $user_id = $this->user_id;
        $data = $this->messages->getNotificationCount($user_id);
        $result = array(
          'notification' => $data->totalN,
          'messages' => $data->totalM
        );
        return $this->response->setJSON($result);
      }
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function popupTweets()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $showpopup = $this->request->getPost('showpopup');
      $user_id = $this->user_id;
      if(isset($showpopup) && !empty($showpopup)){
    		$tweetID = $showpopup;
    		$tweet   = $this->tweets->tweetPopup($tweetID);
    		$likes   = likes($user_id, $tweetID);
        $user    = $this->data['user'];
    		$retweet = checkRetweet($tweetID,$user_id);
    		$comments = $this->comments->comments($tweetID);

        $output = '
        <div class="tweet-show-popup-wrap">
        <input type="checkbox" id="tweet-show-popup-wrap">
        <div class="wrap4">
        	<label for="tweet-show-popup-wrap">
        		<div class="tweet-show-popup-box-cut">
        			<i class="fa fa-times" aria-hidden="true"></i>
        		</div>
        	</label>
        	<div class="tweet-show-popup-box">
        	<div class="tweet-show-popup-inner">
        		<div class="tweet-show-popup-head">
        			<div class="tweet-show-popup-head-left">
        				<div class="tweet-show-popup-img">
        					<img src="'.site_url().$tweet->profileImage.'"/>
        				</div>
        				<div class="tweet-show-popup-name">
        					<div class="t-s-p-n">
        						<a href="'.site_url().$tweet->username.'">
        							'.$tweet->screenName.'
        						</a>
        					</div>
        					<div class="t-s-p-n-b">
        						<a href="'.site_url().$tweet->username.'">
        							@'.$tweet->username.'
        						</a>
        					</div>
        				</div>
        			</div>
        			<div class="tweet-show-popup-head-right">
        			'.followBtn($tweet->tweetBy, $user_id, $user_id).'
         			</div>
        		</div>
        		<div class="tweet-show-popup-tweet-wrap">
        			<div class="tweet-show-popup-tweet">
        				'.getTweetLinks($tweet->status).'
        			</div>
        			<div class="tweet-show-popup-tweet-ifram mb-0">';

        			if(!empty($tweet->tweetImage)){
      			    	$output .= '<img src="'.site_url().$tweet->tweetImage.'"/>';
      				}

        			$output .= '</div>
        		</div>
        		<div class="tweet-show-popup-footer-wrap">
        			<div class="tweet-show-popup-retweet-like">
        				<div class="tweet-show-popup-retweet-left">
        					<div class="tweet-retweet-count-wrap">
        						<div class="tweet-retweet-count-head">
        							RETWEET
        						</div>
        						<div class="tweet-retweet-count-body">
        							'.$tweet->retweetCount.'
        						</div>
        					</div>
        					<div class="tweet-like-count-wrap">
        						<div class="tweet-like-count-head">
        							LIKES
        						</div>
        						<div class="tweet-like-count-body">
        							'.$tweet->likesCount.'
        						</div>
        					</div>
        				</div>
        				<div class="tweet-show-popup-retweet-right">

        				</div>
        			</div>
        <!--
        			<div class="tweet-show-popup-time">
        				<span>'.timeAgo($tweet->created_at).'</span>
        			</div>
        -->
        			<div class="tweet-show-popup-footer-menu mb-0">';
        				$output .= '<ul>
        						'.((loggedIn()) ?   '
        							<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
        							<li>'.(((isset($retweet->retweetID)) ? $tweet->tweetID === $retweet->retweetID OR $user_id === $retweet->retweetBy : '') ? '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '')
                      .'</span></button>' : '<button class="retweet" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>').'</li>
        							<li>'.(((isset($likes->likeOn)) ? $likes->likeOn == $tweet->tweetID : '') ? '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '' ).'</span></button>' : '<button class="like-btn" data-tweet="'
                      .$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '').'</span></button>').'</li>
        							'.(($tweet->tweetBy === $user_id) ? '
        							<li>
        								<a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
        								<ul>
        								  <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>
        								</ul>
        							</li>' : '').'
        						' : '
        							<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
        							<li><button><i class="fa fa-retweet" aria-hidden="true"></i></button></li>
        							<li><button><i class="fa fa-heart-o" aria-hidden="true"></i></button></li>
        						').'
        						</ul>
        			</div>
        		</div>
        	</div><!--tweet-show-popup-inner end-->';
        	if(loggedIn() === true)
          {
           	$output .= '<div class="tweet-show-popup-footer-input-wrap">
          		<div class="tweet-show-popup-footer-input-inner">
          			<div class="tweet-show-popup-footer-input-left">
          				<img src="'.site_url().$user->profileImage.'"/>
          			</div>
          			<div class="tweet-show-popup-footer-input-right">
          				<input id="commentField" type="text" name="comment"  data-tweet="'.$tweet->tweetID.'" placeholder="Reply to @'.$tweet->username.'">
          			</div>
          		</div>
          		<div class="tweet-footer">
          		 	<div class="t-fo-left">
          		 		<ul>
          		 			<li>
          		 				<!-- <label for="t-show-file"><i class="fa fa-camera" aria-hidden="true"></i></label>
          		 				<input type="file" id="t-show-file"> -->
          		 			</li>
          		 		</ul>
          		 	</div>
          		 	<div class="t-fo-right">
           		 		<input type="submit" id="postComment" value="Tweet">
           		 		<script type="text/javascript" src="'.site_url().'assets/js/comment.js"></script>
           		 		<script type="text/javascript" src="'.site_url().'assets/js/follow.js"></script>
            		 	</div>
          		 </div>
          	</div><!--tweet-show-popup-footer-input-wrap end-->';
          }

          $output .= '<div class="tweet-show-popup-comment-wrap">
        	<div id="comments">';

      	 		foreach ($comments as $comment) {
      	 			$output .= '<div class="tweet-show-popup-comment-box">
      						<div class="tweet-show-popup-comment-inner">
      							<div class="tweet-show-popup-comment-head">
      								<div class="tweet-show-popup-comment-head-left">
      									 <div class="tweet-show-popup-comment-img">
      									 	<img src="'.site_url().$comment->profileImage.'">
      									 </div>
      								</div>
      								<div class="tweet-show-popup-comment-head-right">
      									  <div class="tweet-show-popup-comment-name-box">
      									 	<div class="tweet-show-popup-comment-name-box-name">
      									 		<a href="'.site_url().$comment->username.'">'.$comment->screenName.'</a>
      									 	</div>
      									 	<div class="tweet-show-popup-comment-name-box-tname">
      									 		<a href="'.site_url().$comment->username.'">@'.$comment->username.'</a>
      									 	</div>
      									 </div>
      									 <div class="tweet-show-popup-comment-right-tweet">
      									 		<p><a href="'.site_url().$tweet->username.'">@'.$tweet->username.'</a> '.$comment->comment.'</p>
      									 </div>
      								 	<div class="tweet-show-popup-footer-menu">
      										<ul>
      											<li><button><i class="fa fa-share" aria-hidden="true"></i></button></li>
      											<li><button><i class="fa fa-heart-o" aria-hidden="true"></i></button></li>
      											'.(($comment->commentBy === $user_id) ?
      											'<li>
      												<a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true"></i></a>
      												<ul>
      												  <li><label class="deleteComment" data-tweet="'.$tweet->tweetID.'" data-comment="'.$comment->commentID.'">Delete Tweet</label></li>
      												</ul>
      											</li>' : '').'
      										</ul>
      									</div>
      								</div>
      							</div>
      						</div>
      						<!--TWEET SHOW POPUP COMMENT inner END-->
      						</div>
      						';
      	 		}
        	$output .= '</div>

        </div>
        <!--tweet-show-popup-box ends-->
        </div>
        </div>';

        return $this->response->setBody($output);

     	}
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function retweet()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $retweet = $this->request->getPost('retweet');
      $showPopup = $this->request->getPost('showPopup');
      $user_id = $this->user_id;

      if(isset($retweet) && !empty($retweet))
      {
    		$tweet_id  = $retweet;
    		$get_id    = $this->request->getPost('user_id');
    		$comment   = $this->users->checkInput($this->request->getPost('user_id'));
    		$this->tweets->retweet($tweet_id, $user_id, $get_id, $comment);
    	}

      if(isset($showPopup) && !empty($showPopup))
      {
    		$tweet_id = $showPopup;
    		$user = $this->data['user'];
    		$tweet = $this->tweets->getPopupTweet($tweet_id);

        $output = '
        <div class="retweet-popup">
          <div class="wrap5">
          	<div class="retweet-popup-body-wrap">
          		<div class="retweet-popup-heading">
          			<h3>Retweet this to followers?</h3>
          			<span><button class="close-retweet-popup"><i class="fa fa-times" aria-hidden="true" style="outline:none;"></i></button></span>
          		</div>
          		<div class="retweet-popup-input">
          			<div class="retweet-popup-input-inner">
          				<input class="retweetMsg" type="text" placeholder="Add a comment.."/>
          			</div>
          		</div>
          		<div class="retweet-popup-inner-body">
          			<div class="retweet-popup-inner-body-inner">
          				<div class="retweet-popup-comment-wrap">
          					 <div class="retweet-popup-comment-head">
          					 	<img src="'.site_url().$tweet->profileImage.'"/>
          					 </div>
          					 <div class="retweet-popup-comment-right-wrap">
          						 <div class="retweet-popup-comment-headline">
          						 	<a>'.$tweet->screenName.'</a><span>@'.$tweet->username.' '.$tweet->created_at .'</span>
          						 </div>
          						 <div class="retweet-popup-comment-body">
          						 	'.$tweet->status.'  | '.$tweet->tweetImage.'
          						 </div>
          					 </div>
          				</div>
          			</div>
          		</div>
          		<div class="retweet-popup-footer">
          			<div class="retweet-popup-footer-right">
          				<button class="retweet-it new-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->user_id.'" type="submit"><i class="fa fa-retweet mr-2" aria-hidden="true"></i>Retweet</button>
          			</div>
          		</div>
          	</div>
          </div>
        </div><!-- Retweet PopUp ends-->';
        return $this->response->setBody($output);
      }
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function search()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $search = $this->request->getPost('search');

      if(isset($search) && !empty($search)){
    		$search = $this->users->checkInput($search);
    		$result = $this->users->search($search);
        $output = '';
    		if(!empty($result)){
      		$output .= ' <div class="nav-right-down-wrap"><ul> ';
      		foreach ($result as $user) {
      			$output .= '
  					<li>
  				  		<div class="nav-right-down-inner trend">
  							<div class="nav-right-down-left">
  								<a href="'.site_url().$user->username.'"><img src="'.site_url().$user->profileImage.'"></a>
  							</div>
  							<div class="nav-right-down-right">
  								<div class="nav-right-down-right-headline">
  									<a href="'.site_url().$user->username.'"><b>'.$user->screenName.'</b></a><br><span class="text-muted">@'.$user->username.'</span>
  								</div>
  								<div class="nav-right-down-right-body">

  							    </div>
  							</div>
  						</div>
  					 </li> ';
      		}
            $output .= '</ul></div>';
            return $this->response->setBody($output);
        }
      }
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function searchUserinMsg()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $search = $this->request->getPost();
      $user_id = $this->user_id;
      if(isset($search) && !empty($search))
      {
        $search  = $this->users->checkInput($_POST['search']);
    		$result  = $this->users->search($search);
        $output = '';
    		$output .= '<h4>People</h4><div class="message-recent"> ';
    		foreach ($result as $user) {
    			if($user->user_id != $user_id){
  			     $output .= '<div class="people-message" data-user="'.$user->user_id.'">
  						<div class="people-inner">
  							<div class="people-img">
  								<img src="'.site_url().$user->profileImage.'"/>
  							</div>
  							<div class="name-right">
  								<span><a>'.$user->screenName.'</a></span><span>@'.$user->username.'</span>
  							</div>
  						</div>
  					 </div>';
    			}
    		}
    		$output .= '</div>';
        return $this->response->setBody($output);
    	}
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

  public function tweetForm()
  {
    if ($this->request->getMethod() === 'POST')
    {
      $user = $this->data['user'];
      $output = '';
      $output .= '
      <!-- POPUP TWEET-FORM WRAP -->
      <div class="popup-tweet-wrap">
      		<div class="wrap">

      		<div class="popwrap-inner">
      			<div class="popwrap-header">
      				<div class="popwrap-h-left">
      					<h4></h4>
      				</div>
      				<span class="popwrap-h-right">
      					<label class="closeTweetPopup" for="pop-up-tweet" ><i class="fa fa-times" aria-hidden="true" style="outline:none;"></i></label>
      				</span>
      			</div>
      			<div class="popwrap-full tweet_body">
      <!--
      			<div class="left-tweet">
                                   PROFILE-IMAGE
                                  <img class="ml-3" src="'.site_url().$user->profileImage.'" style="width: 53px;height:53px;border-radius:50%;" />
                              </div>
      -->
      			 <form id="popupForm" method="post" enctype="multipart/form-data">
                                      <textarea class="status" maxlength="141" name="status" placeholder="What\'s happening?" rows="3" cols="100%"style="font-size:17px;"></textarea>
                                      <div class="hash-box">
                                          <ul>
                                          </ul>
                                      </div>

                                      <div class="tweet_icons-wrapper">
                                          <div class="t-fo-left tweet_icons-add">
                                              <ul>
                                                  <input type="file" name="file" id="file" />
                                                  <li><label for="file"><i class="fa fa-image" aria-hidden="true"></i></label>
                                                      <i class="fa fa-bar-chart"></i>
                                                      <i class="fa fa-smile-o"></i>
                                                      <i class="fa fa-calendar-o"></i>
                                                  </li>
                                                  <span class="tweet-error">';
                                                  if ( isset( $error ) ) {
                                                      $output .= $error;
                                                  } else if ( isset( $imgError ) ) {
                                                      $output .= "<br>" . $imgError;
                                                  }

                                                  $output .= '</span>
                                                  <!--<i class="fa fa-image"></i>-->

                                              </ul>
                                          </div>
                                          <div class="t-fo-right">
                                              <!--<span id="count">140</span>-->
                                              <!--<input type="submit" name="tweet" value="tweet" />-->

                                              <button class="button_tweet" type="submit" name="tweet" style="outline:none;">Tweet</button>

                                          </div>
                                  </form>
      				</div>
      			</div>
      		</div>
      	</div>
      </div>
      <!-- POPUP TWEET-FORM END -->';
      return $this->response->setBody($output);
    } else {
      return $this->response->setStatusCode(405)->setJSON(['error' => 'Method not allowed']);
    }
  }

}
