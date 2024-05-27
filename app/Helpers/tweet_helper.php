<?php
use Config\Database;
use CodeIgniter\Database\BaseBuilder;

// Ensure the helper is not already loaded
if (!function_exists('checkRetweet')) {
    function checkRetweet($tweet_id, $user_id)
    {
        // Get database instance
        $db = Database::connect();
        $builder = $db->table('tweets');

        // Check if the tweet is retweeted by the user
        $builder->where('retweetID', $tweet_id);
        $builder->where('retweetBy', $user_id);
        $query = $builder->get();

        // Return true if the tweet is retweeted, false otherwise
        return $query->getRow();
    }
}

if (!function_exists('likes')) {
  function likes($user_id, $tweet_id) {
      $db = Database::connect();
      $builder = $db->table('likes');
      $builder->where('likeBy', $user_id);
      $builder->where('likeOn', $tweet_id);
      $query = $builder->get();

      return $query->getRowArray(); // Fetches result as an associative array
  }
}

if (!function_exists('userData')) {
  function userData($user_id)
  {
      $db = Database::connect();
      // Retrieve user data by user_id
      $builder = $db->table('users');
      $builder->where('user_id', $user_id);
      $query = $builder->get();

      return $query->getRow(); // Return single user data
  }
}

if (!function_exists('tweets')) {
function tweets($user_id, $num)
  {
      $db = Database::connect();
      $builder = $db->table('tweets');
      $builder->select('tweets.*, users.screenName, users.username, users.profileImage');
      $builder->join('users', 'tweets.tweetBy = users.user_id');

      // Define the subquery for whereIn
      $subquery = function(BaseBuilder $subBuilder) use ($user_id) {
          $subBuilder->select('receiver')->from('follow')->where('sender', $user_id);
      };

      // Using complex WHERE conditions
      $builder->groupStart()
                ->where('tweets.tweetBy', $user_id)
                ->where('tweets.retweetID', 0)
              ->groupEnd()
              ->orGroupStart() // Start an OR condition group
                ->where('tweets.tweetBy', $user_id)
                ->where('tweets.retweetBy !=', $user_id)
                // Subquery in WHERE
                ->whereIn('tweets.tweetBy', $subquery)
                // ->where("tweets.tweetBy IN (SELECT `receiver` FROM `follow` WHERE `sender` = $user_id)", NULL, FALSE)
              ->groupEnd(); // Close the OR group

      $builder->orderBy('tweets.tweetID', 'DESC');
      $builder->limit($num);
      $query = $builder->get();

      $tweets = $query->getResult();
      foreach ($tweets as $tweet) {
        $likes = likes($user_id, $tweet->tweetID);
        $retweet = checkRetweet($tweet->tweetID, $user_id);
        $user = userData($tweet->retweetBy);
        echo '<div class="all-tweet">
            <div class="t-show-wrap">
             <div class="t-show-inner">
             '.((isset($retweet->retweetID) ? $retweet->retweetID === $tweet->retweetID OR $tweet->retweetID > 0 : '') ? '
              <div class="t-show-banner">
                <div class="t-show-banner-inner">
                  <span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>'.$user->screenName.' Retweeted</span>
                </div>
              </div>'
              : '').'

              '.((!empty($tweet->retweetMsg) && $tweet->tweetID === $retweet->tweetID or $tweet->retweetID > 0) ? '<div class="t-show-head">
              <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                <div class="t-show-img">
                  <img src="'.site_url().$user->profileImage.'"/>
                </div>
                <div class="t-s-head-content">
                  <div class="t-h-c-name">
                    <span><a href="'.site_url().$user->username.'">'.$user->screenName.'</a></span>
                    <span>@'.$user->username.'</span>
                    <span>'.timeAgo($tweet->created_at).'</span>

                  </div>
                  <div class="t-h-c-dis">
                    '.getTweetLinks($tweet->retweetMsg).'
                  </div>
                </div>
              </div>
              <div class="t-s-b-inner">
                <div class="t-s-b-inner-in">
                  <div class="retweet-t-s-b-inner">
                  '.((!empty($tweet->tweetImage)) ? '
                    <div class="retweet-t-s-b-inner-left">
                      <img src="'.site_url().$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>
                    </div>' : '').'
                    <div>
                      <div class="t-h-c-name">
                        <span><a href="'.site_url().$tweet->username.'">'.$tweet->screenName.'</a></span>
                        <span>@'.$tweet->username.'</span>
                        <span>'.timeAgo($tweet->created_at).'</span>
                      </div>
                      <div class="retweet-t-s-b-inner-right-text">
                        '.getTweetLinks($tweet->status).'
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              </div>' : '

              <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                <div class="t-show-head">
                  <div class="t-show-img">
                    <img src="'.site_url().$tweet->profileImage.'"/>
                  </div>
                  <div class="t-s-head-content ">
                    <div class="t-h-c-name media-body">
                      <span><a href="'.$tweet->username.'">'.$tweet->screenName.'</a></span>
                      <span>@'.$tweet->username.'</span>
                      <span>'.timeAgo($tweet->created_at).'</span>
                    </div>
                    <div class="t-h-c-dis">
                      '.getTweetLinks($tweet->status).'
                    </div>
                  </div>
                </div>'.
                ((!empty($tweet->tweetImage)) ?
                 '<!--tweet show head end-->
                      <div class="t-show-body">
                        <div class="t-s-b-inner">
                         <div class="t-s-b-inner-in">
                           <img src="'.site_url().$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>
                         </div>
                        </div>
                      </div>
                      <!--tweet show body end-->
                ' : '').'

              </div>').'
              <div class="t-show-footer">
                <div class="t-s-f-right">
                  <ul>
                    <li><button style="outline:none;"><i class="fa fa-comment" aria-hidden="true"></i></button></li>
                    <li>'.((isset($retweet->retweetID) ? $tweet->tweetID === $retweet->retweetID : '') ?
                      '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-retweet" aria-hidden="true" style="outline:none;"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>' :
                      '<button class="retweet" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>').'
                    </li>
                    <li>'.((isset($likes->likeOn) ? $likes->likeOn === $tweet->tweetID : '') ?
                      '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '' ).'</span></button>' :
                      '<button class="like-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '' ).'</span></button>').'
                    </li>

                      '.(($tweet->tweetBy === $user_id) ? '
                          <li>
                      <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true" style="outline:none;"></i></a>
                      <ul>
                        <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>
                      </ul>
                    </li>' : '').'

                  </ul>
                </div>
              </div>
            </div>
            </div>
            </div>';
          }
  }
}

if (!function_exists('returntweets')) {
function returntweets($user_id, $num)
  {
      $db = Database::connect();
      $builder = $db->table('tweets');
      $builder->select('tweets.*, users.screenName, users.username, users.profileImage');
      $builder->join('users', 'tweets.tweetBy = users.user_id');

      // Define the subquery for whereIn
      $subquery = function(BaseBuilder $subBuilder) use ($user_id) {
          $subBuilder->select('receiver')->from('follow')->where('sender', $user_id);
      };

      // Using complex WHERE conditions
      $builder->groupStart()
                ->where('tweets.tweetBy', $user_id)
                ->where('tweets.retweetID', 0)
              ->groupEnd()
              ->orGroupStart() // Start an OR condition group
                ->where('tweets.tweetBy', $user_id)
                ->where('tweets.retweetBy !=', $user_id)
                // Subquery in WHERE
                ->whereIn('tweets.tweetBy', $subquery)
                // ->where("tweets.tweetBy IN (SELECT `receiver` FROM `follow` WHERE `sender` = $user_id)", NULL, FALSE)
              ->groupEnd(); // Close the OR group

      $builder->orderBy('tweets.tweetID', 'DESC');
      $builder->limit($num);
      $query = $builder->get();

      $tweets = $query->getResult();
      $output = '';
      foreach ($tweets as $tweet) {
        $likes = likes($user_id, $tweet->tweetID);
        $retweet = checkRetweet($tweet->tweetID, $user_id);
        $user = userData($tweet->retweetBy);
        $output .= '<div class="all-tweet">
            <div class="t-show-wrap">
             <div class="t-show-inner">
             '.((isset($retweet->retweetID) ? $retweet->retweetID === $tweet->retweetID OR $tweet->retweetID > 0 : '') ? '
              <div class="t-show-banner">
                <div class="t-show-banner-inner">
                  <span><i class="fa fa-retweet" aria-hidden="true"></i></span><span>'.$user->screenName.' Retweeted</span>
                </div>
              </div>'
              : '').'

              '.((!empty($tweet->retweetMsg) && $tweet->tweetID === $retweet->tweetID or $tweet->retweetID > 0) ? '<div class="t-show-head">
              <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                <div class="t-show-img">
                  <img src="'.site_url().$user->profileImage.'"/>
                </div>
                <div class="t-s-head-content">
                  <div class="t-h-c-name">
                    <span><a href="'.site_url().$user->username.'">'.$user->screenName.'</a></span>
                    <span>@'.$user->username.'</span>
                    <span>'.timeAgo($tweet->created_at).'</span>

                  </div>
                  <div class="t-h-c-dis">
                    '.getTweetLinks($tweet->retweetMsg).'
                  </div>
                </div>
              </div>
              <div class="t-s-b-inner">
                <div class="t-s-b-inner-in">
                  <div class="retweet-t-s-b-inner">
                  '.((!empty($tweet->tweetImage)) ? '
                    <div class="retweet-t-s-b-inner-left">
                      <img src="'.site_url().$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>
                    </div>' : '').'
                    <div>
                      <div class="t-h-c-name">
                        <span><a href="'.site_url().$tweet->username.'">'.$tweet->screenName.'</a></span>
                        <span>@'.$tweet->username.'</span>
                        <span>'.timeAgo($tweet->created_at).'</span>
                      </div>
                      <div class="retweet-t-s-b-inner-right-text">
                        '.getTweetLinks($tweet->status).'
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              </div>' : '

              <div class="t-show-popup" data-tweet="'.$tweet->tweetID.'">
                <div class="t-show-head">
                  <div class="t-show-img">
                    <img src="'.site_url().$tweet->profileImage.'"/>
                  </div>
                  <div class="t-s-head-content ">
                    <div class="t-h-c-name media-body">
                      <span><a href="'.$tweet->username.'">'.$tweet->screenName.'</a></span>
                      <span>@'.$tweet->username.'</span>
                      <span>'.timeAgo($tweet->created_at).'</span>
                    </div>
                    <div class="t-h-c-dis">
                      '.getTweetLinks($tweet->status).'
                    </div>
                  </div>
                </div>'.
                ((!empty($tweet->tweetImage)) ?
                 '<!--tweet show head end-->
                      <div class="t-show-body">
                        <div class="t-s-b-inner">
                         <div class="t-s-b-inner-in">
                           <img src="'.site_url().$tweet->tweetImage.'" class="imagePopup" data-tweet="'.$tweet->tweetID.'"/>
                         </div>
                        </div>
                      </div>
                      <!--tweet show body end-->
                ' : '').'

              </div>').'
              <div class="t-show-footer">
                <div class="t-s-f-right">
                  <ul>
                    <li><button style="outline:none;"><i class="fa fa-comment" aria-hidden="true"></i></button></li>
                    <li>'.((isset($retweet->retweetID) ? $tweet->tweetID === $retweet->retweetID : '') ?
                      '<button class="retweeted" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-retweet" aria-hidden="true" style="outline:none;"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>' :
                      '<button class="retweet" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-retweet" aria-hidden="true"></i><span class="retweetsCount">'.(($tweet->retweetCount > 0) ? $tweet->retweetCount : '').'</span></button>').'
                    </li>
                    <li>'.((isset($likes->likeOn) ? $likes->likeOn === $tweet->tweetID : '') ?
                      '<button class="unlike-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-heart" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '' ).'</span></button>' :
                      '<button class="like-btn" data-tweet="'.$tweet->tweetID.'" data-user="'.$tweet->tweetBy.'" style="outline:none;"><i class="fa fa-heart-o" aria-hidden="true"></i><span class="likesCounter">'.(($tweet->likesCount > 0) ? $tweet->likesCount : '' ).'</span></button>').'
                    </li>

                      '.(($tweet->tweetBy === $user_id) ? '
                          <li>
                      <a href="#" class="more"><i class="fa fa-ellipsis-h" aria-hidden="true" style="outline:none;"></i></a>
                      <ul>
                        <li><label class="deleteTweet" data-tweet="'.$tweet->tweetID.'">Delete Tweet</label></li>
                      </ul>
                    </li>' : '').'

                  </ul>
                </div>
              </div>
            </div>
            </div>
            </div>';
      }
      return $output;
  }
}

if (!function_exists('timeAgo')) {
  function timeAgo($datetime)
  {
      // Calculate the time since a given datetime
      $time = strtotime($datetime);
      $now = time();
      $diff = $now - $time;

      if ($diff < 60) {
          return 'Just now';
      } elseif ($diff < 3600) {
          return round($diff / 60) . ' minutes ago';
      } elseif ($diff < 86400) {
          return round($diff / 3600) . ' hours ago';
      } elseif ($diff < 604800) {
          return round($diff / 86400) . ' days ago';
      } else {
          return date('M d, Y', $time); // Return formatted date for older timestamps
      }
  }
}

if (!function_exists('getTweetLinks')) {
function getTweetLinks($tweet){
		$tweet = preg_replace("/(https?:\/\/)([\w]+.)([\w\.]+)/", "<a href='$0' target='_blink'>$0</a>", $tweet);

        //$tweet = preg_replace("/#([\w]+)/", "<a href='http://localhost/twitter/hashtag/$1'>$0</a>", $tweet);

		$tweet = preg_replace("/#([\w]+)/", "<a href='http://localhost/twitter/$1'>$0</a>", $tweet);

		$tweet = preg_replace("/@([\w]+)/", "<a href='http://localhost/twitter/$1'>$0</a>", $tweet);
		return $tweet;
	}
}

if (!function_exists('getTrends')) {
  function getTrends() {
    $db = Database::connect();
    $builder = $db->table('trends');
    $builder->select('trends.*, COUNT(tweets.tweetID) AS tweetsCount');
    $builder->join('tweets', 'tweets.status LIKE CONCAT("%#", trends.hashtag, "%") OR tweets.retweetMsg LIKE CONCAT("%#", trends.hashtag, "%")', 'inner');
    $builder->groupBy('trends.hashtag');
    $builder->orderBy('tweets.tweetID');
    $builder->limit(2);
    $query = $builder->get();

    $trends = $query->getResult();

    echo '<div class="trends_container"><div class="trends_box"><div class="trends_header"><p>Trends for you</p></div><!-- trend title end-->';
    foreach ($trends as $trend)
    {
			echo '<div class="trends_body">
					<div class="trend">
                    <span>Trending</span>
						<p>
							<a style="color: #000;">#'.$trend->hashtag.'</a>
						</p>
						<div class="trend-tweets">

						</div>
					</div>
                </div>
                <div>
				</div>';
		}
		echo '<div class="trends_show-more">
                    <a href="">Show more</a>
                </div></div></div>';
  }
}

?>
