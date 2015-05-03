<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Selectcurriculum extends CI_Controller
{	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isStudent())
			redirect('Login/logout');
		
		$selectedCurriculums = $user->getCurriculums();
		
		$unselectedCurriculums = Curriculum_model::getAllCurriculums();
		
		foreach($unselectedCurriculums as $key => $unselectedCurriculum)
		{
			foreach($selectedCurriculums as $selectedCurriculum)
			{
				if($selectedCurriculum->getCurriculumID() == $unselectedCurriculum->getCurriculumID())
				{
					unset($unselectedCurriculums[$key]);
					break;
				}
			}
		}
		
		$data = array(
			'user' => $user,
			'selectedCurriculums' => $selectedCurriculums,
			'unselectedCurriculums' => $unselectedCurriculums
		);
		
		$this->load->view('curriculumselect_index_view', $data);
	}
	
	public function add($curriculumID = null)
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isStudent())
			redirect('Login/logout');
		
		$curriculum = new Curriculum_model;
		
		if($curriculum->loadPropertiesFromPrimaryKey($curriculumID))
		{
			$user->addCurriculum($curriculum);
		}
		
		redirect('Selectcurriculum/index');
	}
	
	public function remove($curriculumID = null)
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isStudent())
			redirect('Login/logout');
		
		$curriculum = new Curriculum_model;
		
		if($curriculum->loadPropertiesFromPrimaryKey($curriculumID))
		{
			$user->removeCurriculum($curriculum);
		}
		
		redirect('Selectcurriculum/index');
	}
}