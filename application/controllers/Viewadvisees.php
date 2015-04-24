<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ViewAdvisees extends CI_Controller
{
	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdvisor())
			redirect('Login/logout');
		
		$data = array ("user" => $user);
		
		$this->load->view('view_advisees', $data);
	}
}