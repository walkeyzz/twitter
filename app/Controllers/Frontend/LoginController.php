<?php namespace App\Controllers\Frontend;

class LoginController extends BaseController {

	public function index() {
		// Is user logged in? Redirect to main page
		if($this->data['logged_user'])
			return redirect()->to('/');

		return view('login/loginorsignup', $this->data);
	}

  public function signin() {
    $this->data['errorMsg'] = '';

    // if ($this->request->getMethod() === 'POST') {

      $email = $this->users->checkInput($this->request->getPost('email'));
      $password = $this->users->checkInput($this->request->getPost('password'));

      if (empty($email) || empty($password)) {
            $this->data['errorMsg'] = "Please enter your email and password!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->data['errorMsg'] = "Invalid email format!";
        } else {
            $user = $this->users->login($email, $password);
            if ($user === false) {
                $this->data['errorMsg'] = "The email or password is incorrect!";
            } else {
                return redirect()->to('/'); // Redirect to home page on successful login
            }
        }
      // }

      return view('login/loginorsignup', $this->data); // Return to login view with error message if any
  }

	public function logout() {
		// Unset session and redirect to the dashboard
		$this->session->remove('user_id');

		return redirect()->to('/login');
	}

  public function signup($signup_step = false) {
    $this->data['error'] = '';

    if ($this->request->getMethod() === 'POST') {
        $rules = [
            'screenName' => 'required|min_length[6]|max_length[20]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[5]',
        ];

        if ($this->validation->setRules($rules)) {

            $email = $this->request->getPost('email');

            if($this->users->checkEmail($email)){
      			     $this->data['error'] = 'Email is already in use';
      			}else {
              $newData = [
                  'screenName' => $this->request->getPost('screenName'),
                  'email' => $this->request->getPost('email'),
                  'password' => md5($this->request->getPost('password')), // Consider using password_hash in real applications
									'profileImage' => 'assets/images/defaultProfileImage.png',
			            'profileCover' => 'assets/images/defaultCoverImage.png',
              ];

              $this->users->save($newData);
              $this->session->set('user_id', $this->users->insertID());
              return redirect()->to('/signup/1'); // Adjust redirect to your application's route
            }
        } else {
            $this->data['error'] = $this->validation->getErrors();
        }
    }

    if ($signup_step != false){
      $this->data['step'] = $signup_step;
      // $user_id = $this->session->get('user_id');
      // $this->data['user'] = $this->users->UserData($user_id);
      // $this->data['notify'] = $this->messages->getNotificationCount($user_id);
      return view('login/signup', $this->data);
    }

    return view('login/loginorsignup', $this->data);
	}

  public function next(){
    // $user_id = $this->session->get('user_id');
    // $this->data['user'] = $this->users->UserData($user_id);
    // $this->data['notify'] = $this->messages->getNotificationCount($user_id);
    $this->data['step'] = '1';

    if ($this->request->getMethod() === 'POST') {
            $username = $this->request->getPost('username');

            if ($username) {
                if (strlen($username) > 20) {
                    $this->data['error'] = "Username must be between 6 and 20 characters";
                } elseif (!preg_match('/^[a-zA-Z0-9]{6,}$/', $username)) {
                    $this->data['error'] = 'Username must be longer than 6 alphanumeric characters without spaces';
                } elseif ($this->users->checkUsername($username)) {
                    $this->data['error'] = "Username is already taken!";
                } else {
                    $this->users->updateUsername($this->user_id, $username);
                    return redirect()->to('/signup/2'); // Redirect to step 2
                }
            } else {
                $this->data['error'] = "Please enter your username";
            }
        }

        return view('login/signup', $this->data); // Render view with error messages (if any)
  }

}
