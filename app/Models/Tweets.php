<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\BaseBuilder;
use App\Models\Messages;

class Tweets extends BaseModel
{
    protected $table = 'tweets';
    protected $primaryKey = 'tweetID';
    protected $allowedFields = [
      'tweetID',
      'status',
      'tweetBy',
      'retweetID',
      'retweetBy',
      'tweetImage',
      'likesCount',
      'retweetCount',
      'retweetMsg',
      'created_at'
    ];
    // protected $useTimestamps = true;
    // protected $createdField  = 'created_at';
    protected $returnType = 'object'; // Default return type for database queries

    public function __construct()
    {
        parent::__construct();
        $this->message = new Messages(); // Create an instance of the Message model
    }

    public function getUserTweets($user_id)
    {
        // Query to get tweets for a specific user
        $builder = $this->table('tweets');
        $builder->join('users', 'tweets.tweetBy = users.user_id');
        $builder->where('tweets.tweetBy', $user_id);
        $builder->orWhere('tweets.retweetBy', $user_id);
        $builder->orderBy('tweets.tweetID', 'DESC');
        $query = $builder->get();

        return $query->getResult();
    }

    public function addLike($user_id, $tweet_id, $get_id)
    {
        // Add a like to a tweet
        $builder = $this->table('tweets');
        $builder->set('likesCount', 'likesCount + 1', false);
        $builder->where('tweetID', $tweet_id);
        $builder->update();

        $this->createRecord('likes', array('likeBy' => $user_id, 'likeOn' => $tweet_id));

        // Send notification for like
        if ($get_id !== $user_id) {
            $this->sendNotification($get_id, $user_id, $tweet_id, 'like');
        }
    }

    public function unLike($user_id, $tweet_id)
    {
        // Decrement the like count in the tweets table
        $builder = $this->table('tweets');
        $builder->set('likesCount', 'likesCount-1', false); // false to indicate the value should not be escaped
        $builder->where('tweetID', $tweet_id);
        $builder->update();

        // Delete the like from the likes table
        $builder = $this->db->table('likes');
        $builder->where('likeBy', $user_id);
        $builder->where('likeOn', $tweet_id);
        $builder->delete();
    }

    public function removeLike($user_id, $tweet_id)
    {
        // Remove a like from a tweet
        $builder = $this->table('tweets');
        $builder->set('likesCount', 'likesCount - 1', false);
        $builder->where('tweetID', $tweet_id);
        $builder->update();

        $this->deleteLike($user_id, $tweet_id);
    }

    public function countTweets($user_id)
    {
        $builder = $this->table('tweets');
        $builder->select('COUNT(tweetID) as totalTweets');
        $builder->groupStart()
                  ->where('tweetBy', $user_id)
                  ->where('retweetID', 0)
                ->groupEnd()
                ->orGroupStart()
                  ->where('retweetBy', $user_id)
                ->groupEnd();

        $query = $builder->get();
        $result = $query->getRow();
        return $result->totalTweets;
    }

    public function getPopupTweet($tweet_id)
    {
        $builder = $this->table('tweets');
        $builder->select('tweets.*, users.*');
        $builder->join('users', 'tweets.tweetBy = users.user_id');
        $builder->where('tweets.tweetID', $tweet_id);

        $query = $builder->get();
        return $query->getRow(); // Returns a single row object
    }

    public function retweet($tweet_id, $user_id, $get_id, $comment)
    {
        // Increment the retweetCount in the original tweet
        $builder = $this->table('tweets');
        $builder->set('retweetCount', 'retweetCount+1', FALSE);
        $builder->where('tweetID', $tweet_id);
        $builder->where('tweetBy', $get_id);
        $builder->update();

        // Insert a new tweet as a retweet
        $db = \Config\Database::connect();
        $sql = "INSERT INTO `tweets` (`status`,`tweetBy`,`retweetID`,`retweetBy`,`tweetImage`,`created_at`,`likesCount`,`retweetCount`,`retweetMsg`) SELECT `status`,`tweetBy`,`tweetID`,?,`tweetImage`,`created_at`,`likesCount`,`retweetCount`,? FROM `tweets` WHERE `tweetID` = ?";
        $db->query($sql, [$user_id, $comment, $tweet_id]);

        $this->message->sendNotification($get_id, $user_id, $tweet_id, 'retweet');
    }

    public function tweetPopup($tweet_id)
    {
        $builder = $this->table('tweets');
        $builder->select('tweets.*, users.*'); // Adjust according to the actual columns you need
        $builder->join('users', 'users.user_id = tweets.tweetBy');
        $builder->where('tweets.tweetID', $tweet_id);

        $query = $builder->get();

        // Returns the first row object if available
        return $query->getRow();
    }

    public function getTweetsByHash($hashtag) {
        $builder = $this->table('tweets');
        $builder->select('tweets.*, users.username, users.profileImage'); // Assuming you want these fields from users
        $builder->join('users', 'tweets.tweetBy = users.user_id', 'left');
        $builder->like('tweets.status', '#' . $hashtag, 'both');  // Searches for hashtag in status
        $builder->orLike('tweets.retweetMsg', '#' . $hashtag, 'both');  // Searches for hashtag in retweet message
        $query = $builder->get();

        return $query->getResult();
    }

}
