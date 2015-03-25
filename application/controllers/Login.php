<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function index()
	{
		$this->load->view('login');
	}
        
        public function auth()
        {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            
            $user = new User_model;
            
            if ($user->loadPropertiesFromPrimaryKey($username) || $user->loadPropertiesFromEmailAddress($username))
            {
                echo "username exists";
                $user->create();
                if ($user->authenticate($password))
                {
                    echo "password correct";
                    $this->load->view('mainpage');
                }
            }
        }
}