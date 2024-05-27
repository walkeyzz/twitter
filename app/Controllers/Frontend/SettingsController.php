<?php namespace App\Controllers\Frontend;

class SettingsController extends BaseController {

	public function index() {
		return view('account', $this->data);
	}

	public function submit() {
		$username = $this->request->getPost('username');
		$this->data['error'] = array();

		if ( !empty( $username )) {
        if ( preg_match( '/[^a-zA-Z0-9\!]/', $username ) ) {
            $this->data['error']['username']  = 'Only characters and numbers allowed';
        }
				if ( $this->data['user']->username != $username and $this->users->checkUsername( $username ) === true ) {
            $this->data['error']['username'] = 'Username is not available';
        } else {
          	$this->users->updateUsername($this->user_id, $username);
						return redirect()->to('settings/account');
    		}
		} else {
        $this->data['error']['fields']  = 'Please fill all the fields';
    }

		return view('account', $this->data);
	}

	public function password() {
		return view('password', $this->data);
	}

	public function passwordSubmit() {
		$currentPwd = $this->request->getPost('currentPwd');
		$newPassword = $this->request->getPost('newPassword');
		$rePassword = $this->request->getPost('rePassword');
		$this->data['error'] = array();

		if(!empty($currentPwd) && !empty($newPassword) && !empty($rePassword)){
			if($this->users->checkPassword($this->data['user']->username, $currentPwd) === true){
				if(strlen($newPassword) < 6){
					$this->data['error']['newPassword'] = "Password is too short";
				}else if($newPassword != $rePassword){
					$this->data['error']['rePassword'] = "Password does not match";
				}else{
					$this->users->updatePassword($this->user_id, md5($newPassword));
					return redirect()->to('settings/password');
				}
			}else{
				$this->data['error']['currentPwd'] = "Password does not match";
			}
		}else{
			$this->data['error']['fields']  = "Please fill all the fields";
		}

		return view('password', $this->data);
	}

}
