<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ManagePC extends CI_Controller
{
	
	public function index()
	{
		$user = new User_model; 
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
			redirect('Login/logout');
		
		$this->load->view('managepc_index_view', array('user'=>$user));
	}
	
	public function getLists()
	{
		$user = new User_model; 
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']) || !$user->isAdmin())
        {
			header("Content-type: text/plain", true, 403);
			echo "Invalid session user credentials";
			return;
		}
		
		$advisors = User_model::getAllAdvisors();
		
		$programChairs = User_model::getAllProgramChairs();
		
		foreach($advisors as $key => $advisor)
		{
			foreach($programChairs as $programChair)
			{
				if($advisor->getUserID() == $programChair->getUserID())
				{
					unset($advisors[$key]);
					break;
				}
			}
		}
		
		$response = array(
			"advisors" => array(),
			"programChairs" => array()
		);
		
		foreach($advisors as $advisor)
		{
			array_push($response['advisors'], array('userid' => $advisor->getUserID(), 'name' => $advisor->getName()));
		}
		
		foreach($programChairs as $programChair)
		{
			array_push($response['programChairs'], array('userid' => $programChair->getUserID(), 'name' => $programChair->getName()));
		}
		
		header("Content-type: application/json", true, 200);
		echo json_encode($response);
	}
	
	public function addProgramChair()
	{
		$user = new User_model; 
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']) || !$user->isAdmin())
        {
			header("Content-type: text/plain", true, 403);
			echo "Invalid session user credentials";
			return;
		}
		
		if(!isset($_POST['userid']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Missing User ID";
			return;
		}
		
		$m_user = new User_model;
		
		if(!$m_user->loadPropertiesFromPrimaryKey($_POST['userid']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Invalid User ID";
			return;
		}
		
		if(!$m_user->isProgramChair())
			$m_user->addRole(User_model::ROLE_PROGRAM_CHAIR);
		
		header("Content-type: text/plain", true, 200);
		echo " ";
	}
	
	public function removeProgramChair()
	{
		$user = new User_model; 
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']) || !$user->isAdmin())
        {
			header("Content-type: text/plain", true, 403);
			echo "Invalid session user credentials";
			return;
		}
		
		if(!isset($_POST['userid']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Missing User ID";
			return;
		}
		
		$m_user = new User_model;
		
		if(!$m_user->loadPropertiesFromPrimaryKey($_POST['userid']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Invalid User ID";
			return;
		}
		
		if($m_user->isProgramChair())
			$m_user->removeRole(User_model::ROLE_PROGRAM_CHAIR);
		
		header("Content-type: text/plain", true, 200);
		echo " ";
	}
}