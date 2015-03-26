<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index()
	{
            $this->load->view('login');
	}
        
        public function auth()
        {
            //Get the username and password from the field
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            //Create a new user object
            $user = new User_model;
            //If username exists
            if ($user->loadPropertiesFromPrimaryKey($username) || $user->loadPropertiesFromEmailAddress($username))
            {
                $user->create();
                //If password is correct
                if ($user->authenticate($password))
                {
                    //Activate the session
                    $_SESSION['UserID'] = $user->getUserID();
                    //Redirect to the mainpage controller
                    redirect('Mainpage');
                }
            }
            //Incorrect username or password, reload login and display an error
            $this->load->view('login', array("error"=>TRUE));
        }
        
        
        public function logout() 
        {
            //Unset session array
            $_SESSION = array();
            //Destroy Cookie
            if (ini_get("session.use_cookies")) 
            {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]);
            }
            //Destroy the session.
            session_destroy();
        }
}