<?php
use Config\Services;


if (!function_exists('loggedIn')) {
  function loggedIn()
  {
    $session = \Config\Services::session();
		return ($session->has('user_id')) ? true : false;
	}
}

if (!function_exists('uploadImage')) {
  function uploadImage($file){
   		 	$filename   = $file['name'];
  			$fileTmp    = $file['tmp_name'];
  			$fileSize   = $file['size'];
  			$errors     = $file['error'];

   			$ext = explode('.', $filename);
  			$ext = strtolower(end($ext));

   			$allowed_extensions  = array('jpg','png','jpeg');

  			if(in_array($ext, $allowed_extensions)){

  				if($errors ===0){

  					if($fileSize <= 2097152){

  		 				$root = 'users/' . $filename;
  					  	 move_uploaded_file($fileTmp,$_SERVER['DOCUMENT_ROOT'].'/twitter/'.$root);
  						 return $root;

  					}else{
  							$GLOBALS['imgError'] = "File Size is too large";
  					    }
  			    }
  			  }else{
  						$GLOBALS['imgError'] = "Only allowed JPG, PNG JPEG extensions";
  		  	       }
   		}
}
