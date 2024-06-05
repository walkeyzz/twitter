<?php
use Config\Database;
use Config\Services;


if (!function_exists('checkFollow')) {
  function checkFollow($followerID, $user_id)
  {
      // Check if a user is following another user
      $db = Database::connect();
      $builder = $db->table('follow');
      $builder->where('sender', $user_id);
      $builder->where('receiver', $followerID);
      $query = $builder->get();

      return $query->getRow(); // Return single row
  }
}

if (!function_exists('followBtn')) {
  function followBtn($profileID, $user_id, $followID){
  		$data = checkFollow($profileID, $user_id);

      $session = Services::session();
      if($session->has('user_id')){
  			if($profileID != $user_id){
  				if(isset($data->receiver) && $data->receiver === $profileID){
  					//Following btn
  					return "<button class='f-btn following-btn follow-btn' data-follow='".$profileID."' data-profile='".$followID."' style='outline:none;'>Following</button>";
  				}else{
  					//Follow button
  					return "<button class='f-btn follow-btn' data-follow='".$profileID."' data-profile='".$followID."' style='outline:none;'><i class='fa fa-user-plus'></i>Follow</button>";
  				}
  			}else{
  				//edit button
  				return "<button class='new-btn' onclick=location.href='".site_url()."profileedit' style='outline:none;'>Edit Profile</button>";
  			}
  		}else{
  			return "<button style='outline:none;' class='f-btn' onclick=location.href='".site_url()."'><i class='fa fa-user-plus'></i>Follow</button>";
  		}
  	}
}

if (!function_exists('followingList')) {
  function followingList($profileID, $user_id, $followID)
  {
      // Check if a user is following another user
      $db = Database::connect();
      $builder = $db->table('follow');
      $builder->select('users.*');
      $builder->join('users', 'users.user_id = follow.receiver');
      $builder->where('follow.sender', $profileID);
      $query = $builder->get();

      $followings = $query->getResult();

      foreach ($followings as $following)
      {
  			echo '<div class="following-box">
                  <div class="follow-unfollow-box">
  					<div class="follow-unfollow-inner">

  						<div class="follow-person-button-img mt-2">
  							<div class="follow-person-img mr-4">
  							 	<img src="'.site_url().$following->profileImage.'"/>
  							</div>
                              <div class="follow-person-name mt-2">
  								<a href="'.site_url().$following->username.'">'.$following->screenName.'</a>
  							</div>
  							<div class="follow-person-tname">
  								<a href="'.site_url().$following->username.'">@'.$following->username.'</a>
  							</div>
  							<div class="follow-person-button">
  								 '.followBtn($following->user_id, $user_id, $followID).'
  						    </div>
  						</div>
  						<div class="follow-person-bio ml-5 mb-3">
  							<div class="follow-person-dis ml-4">
  								'.getTweetLinks($following->bio).'
  							</div>
  						</div>
  					</div>
  				</div></div>';
		}
    echo '<div class="space"style="height:10px; width:100%; background:rgba(230, 236, 240, 0.5);"></div>';
  }
}

if (!function_exists('followersList')) {
  function followersList($profileID, $user_id, $followID)
  {
      // Check if a user is following another user
      $db = Database::connect();
      $builder = $db->table('follow');
      $builder->select('users.*');
      $builder->join('users', 'users.user_id = follow.sender');
      $builder->where('follow.receiver', $profileID);
      $query = $builder->get();

      $followings = $query->getResult();
      foreach ($followings as $following)
      {
  			echo '<div class="following-box">
                  <div class="follow-unfollow-box">
  					<div class="follow-unfollow-inner">
  						<div class="follow-person-button-img mt-2">
  							<div class="follow-person-img mr-4">
  							 	<img src="'.site_url().$following->profileImage.'"/>
  							</div>
                              <div class="follow-person-name mt-2">
  								<a href="'.site_url().$following->username.'">'.$following->screenName.'</a>
  							</div>
  							<div class="follow-person-tname">
  								<a href="'.site_url().$following->username.'">@'.$following->username.'</a>
  							</div>
  							<div class="follow-person-button">
  								 '.followBtn($following->user_id, $user_id, $followID).'
  						    </div>
  						</div>
  						<div class="follow-person-bio ml-5 mb-3">
  							<div class="follow-person-dis ml-4">
  								'.getTweetLinks($following->bio).'
  							</div>
  						</div>
  					</div>
  				</div></div>';
  		}
      echo '<div class="space"style="height:10px; width:100%; background:rgba(230, 236, 240, 0.5);"></div>';
  }
}
