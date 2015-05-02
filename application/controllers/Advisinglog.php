<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advisinglog extends CI_Controller
{	
	public function index($advisorUserID = "all", $studentUserID = "all", $advisingLogEntryType = "all")
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair() && !$user->isAdvisor())
			redirect('Login/logout');
		
		if($advisingLogEntryType === "all")
			$advisingLogEntryType = null;
		
		if($advisorUserID === "all")
			$advisorUserID = null;
		
		if($user->isAdvisor() && !$user->isProgramChair() && ($advisorUserID == null || $advisorUserID != $user->getUserID()))
			$advisorUserID = $user->getUserID();
		
		if($studentUserID === "all")
			$studentUserID = null;
		
		$advisors = ($user->isProgramChair()) ? User_model::getAllAdvisors() : array( $user );
		
		$students = ($user->isProgramChair()) ? array() : $user->getAdvisees();
		
		$types = Advising_log_entry_model::getAllAdvisingLogEntryTypes();
		
		$data = array(
			'user' => $user,
			'logEntries' => Advising_log_entry_model::getAllAdvisingLogEntries($advisorUserID, $studentUserID, $advisingLogEntryType),
			'advisors' => $advisors,
			'students' => $students,
			'types' => $types,
			'advisorUserID' => ($advisorUserID == null) ? "all" : $advisorUserID,
			'studentUserID' => ($studentUserID == null) ? "all" : $studentUserID,
			'advisingLogEntryType' => ($advisingLogEntryType == null) ? "all" : $advisingLogEntryType
		);
		
		$this->load->view('advisinglog_index_view', $data);
	}
}