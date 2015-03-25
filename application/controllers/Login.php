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
            
            if ($this->User_model->loadPropertiesFromPrimaryKey($username) || $this->User_model->loadPropertiesFromEmailAddress($username))
            {
                if ($this->User_model->authenticate($password))
                {
                    $this->load->view('mainpage');
                }
            }
        }
}