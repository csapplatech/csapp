<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BossImport extends CI_Controller
{
	const MAX_FILE_SIZE = 262144000;
	
	const UPLOAD_FILE_DIR = "./uploads";
	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
			redirect('Login/logout');
		
		$data = array('user' => $user);
		
		$this->load->view('bossimport_index_view', $data);
	}
	
	public function submit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			header("Content-type: text/plain", true, 401);
			echo "Unauthorized access";
			return;
		}
		
		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['boss_file']['error']) 
		{
			case UPLOAD_ERR_OK:
				break;
				
			case UPLOAD_ERR_NO_FILE:
				header("Content-type: text/plain", true, 400);
				echo "No file sent";
				return;
				
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				header("Content-type: text/plain", true, 400);
				echo "Exceeded file size limit";
				return;
				
			default:
				header("Content-type: text/plain", true, 500);
				echo "Unknown error occurred";
				return;
		}
		
		// You should also check filesize here. 
		if ($_FILES['boss_file']['size'] > self::MAX_FILE_SIZE) 
		{
			header("Content-type: text/plain", true, 400);
			echo "Exceeded file size limit";
			return;
		}
		
		$file_name = hash("md5", time() . $_FILES['boss_file']['tmp_name']);
		
		$file_path = self::UPLOAD_FILE_DIR . "/" . $file_name . ".txt";
		
		if (!move_uploaded_file($_FILES['boss_file']['tmp_name'], $file_path))
		{
			header("Content-type: text/plain", true, 500);
			echo "Failed to move uploaded file";
			return;
		}
		
		include_once('application/libraries/boss_import/ParserServerTest.php');
		
		$result = ParseFile($file_path);
		
		// In future, possibly check to make sure file was successfully deleted here
		unlink($file_path);
		
		if($result == null)
		{
			header("Content-type: text/plain", true, 200);
			echo "Success";
		}
		else
		{
			header("Content-type: text/plain", true, 400);
			echo $result;
		}
	}
}