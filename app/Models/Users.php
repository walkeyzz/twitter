<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
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

    protected $returnType = 'object';

    public function checkInput($input)
    {
        // Sanitize input
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function userData($user_id)
    {
        return $this->find($user_id);
    }

    public function updateUsername($user_id, $username)
    {
        // Updates the username for a specific user
        return $this->update($user_id, ['username' => $username]);
    }

    public function checkUsername($username)
    {
        // Checks if the username already exists in the users table
        return $this->where('username', $username)->countAllResults() > 0;
    }

    public function checkEmail($email)
    {
        // Checks if the email already exists in the users table
        return $this->where('email', $email)->countAllResults() > 0;
    }

    public function login($email, $password)
    {
        // Check if a user exists with the given email and password
        $user = $this->where('email', $email)->first();

        if ($user && $user->password === md5($password)) {
            return $user;
        }
        return false;
    }
}
