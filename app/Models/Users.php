<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\Messages;

class Users extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $allowedFields = [
      'user_id',
      'username',
      'email',
      'password',
      'screenName',
      'profileImage',
      'profileCover',
      'following',
      'followers',
      'bio',
      'country',
      'website'
    ];

    protected $returnType = 'object'; // Return data as objects

    public function __construct()
    {
        parent::__construct();
        $this->message = new Messages(); // Create an instance of the Message model
    }

    public function checkInput($data)
    {
        // Clean input data to prevent XSS
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    public function search($search)
    {
        // Search for users by username or screenName
        $builder = $this->table('users');
        $builder->like('username', $search);
        $builder->orLike('screenName', $search);
        $query = $builder->get();

        return $query->getResult(); // Return as an array of objects
    }

    public function login($email, $password)
    {
        // Validate user login
        $passwordHash = md5($password); // Note: MD5 is not recommended for password hashing
        $builder = $this->table('users');
        $builder->select('user_id');
        $builder->where('email', $email);
        $builder->where('password', $passwordHash);
        $query = $builder->get();

        $user = $query->getRow(); // Fetch a single row

        if ($user) {
            // Store user ID in session
            session()->set('user_id', $user->user_id);
            return true; // Successful login
        } else {
            return false; // Unsuccessful login
        }
    }

    public function register($email, $password, $screenName)
    {
        // Register a new user
        $passwordHash = md5($password); // Again, MD5 is not recommended for password hashing
        $data = [
            'email' => $email,
            'password' => $passwordHash,
            'screenName' => $screenName,
            'profileImage' => 'assets/images/defaultProfileImage.png',
            'profileCover' => 'assets/images/defaultCoverImage.png',
        ];

        $this->insert($data); // Insert new user into the database
        $user_id = $this->insertID(); // Get the last inserted ID

        session()->set('user_id', $user_id); // Store user ID in session
        return $user_id; // Return user ID after registration
    }

    public function userData($user_id)
    {
        // Retrieve user data by user_id
        $builder = $this->table('users');
        $builder->where('user_id', $user_id);
        $query = $builder->get();

        return $query->getRow(); // Return single user data
    }

    public function checkUsername($username)
    {
        // Check if a username exists in the "users" table
        $builder = $this->table('users');
        $builder->where('username', $username);
        $query = $builder->get();

        return ($query->getRow() !== null); // Return true if a record exists, false otherwise
    }

    public function checkPassword($username, $password) {
        $builder = $this->table('users');
        $builder->select('password');
        $builder->where('username', $username);  // Assuming you're identifying the user by username
        $query = $builder->get();

        if ($query->getNumRows() === 1) {
            $user = $query->getRow();
            $md5 = md5($password);
            if ($md5 == $user->password){
                return true;  // Password is correct
            }
        }
        return false;  // Password is incorrect or user does not exist
    }

    public function checkEmail($email)
    {
        // Check if an email exists in the "users" table
        $builder = $this->table('users');
        $builder->where('email', $email);
        $query = $builder->get();

        return ($query->getRow() !== null); // Return true if a record exists, false otherwise
    }

    public function uploadImage($file)
    {
        // Handle image upload
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName(); // Generate a unique name
            $file->move(WRITEPATH . 'uploads/profile_images', $newName); // Move file to the specified path
            return 'uploads/profile_images/' . $newName; // Return the relative path to the image
        }
        return null; // Return null if file is invalid
    }

    public function updateUsername($user_id, $username)
    {
        // Updates the username for a specific user
        return $this->update($user_id, ['username' => $username]);
    }

    public function updatePassword($user_id, $password)
    {
        // Updates the username for a specific user
        return $this->update($user_id, ['password' => $password]);
    }

    public function UserIdByUsername($username)
    {
        $builder = $this->db->table($this->table);
        $builder->select('user_id');
        $builder->where('username', $username);

        $query = $builder->get();

        $result = $query->getRow();

        return $result->user_id;
    }

    public function getMention($mention)
    {
        $builder = $this->table('users');
        $builder->select('user_id, username, screenName, profileImage');
        $builder->groupStart()
                  ->like('username', $mention, 'after')
                  ->orLike('screenName', $mention, 'after')
                ->groupEnd();
        $builder->limit(5);
        $query = $builder->get();

        return $query->getResult();
    }

    public function addMention($status, $user_id, $tweet_id)
    {
        if (preg_match_all("/@+([a-zA-Z0-9_]+)/i", $status, $matches)) {
            if($matches){
              $result = array_values($matches[1]);
            }

            $builder = $this->table('users');

            foreach ($result as $mention) {
                $builder->where('username', $mention);
                $query = $builder->get();
                $data = $query->getRow();

                if ($data && $data->user_id != $user_id) {
                    // Send a notification for the mention
                    $this->message->sendNotification($data->user_id, $user_id, $tweet_id, 'mention');
                }
            }
        }
    }

    public function getUsersByHash($hashtag)
    {
        $builder = $this->table('users');
        $builder->distinct();
        $builder->select('users.*'); // Specifying users.* since we're focusing on user details
        $builder->join('tweets', 'tweets.tweetBy = users.user_id', 'inner');
        $builder->groupStart();  // Group the like conditions
            $builder->like('tweets.status', '#' . $hashtag, 'both');
            $builder->orLike('tweets.retweetMsg', '#' . $hashtag, 'both');
        $builder->groupEnd();
        $builder->groupBy('users.user_id');  // Ensuring distinct users by grouping
        $query = $builder->get();

        return $query->getResult(); // Returns result set as objects
    }


}
