<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurriculumCreator extends CI_Controller {

	//In case we can't pass objects we can try to generate tables in the php
	//https://ellislab.com/codeigniter/user-guide/libraries/table.html
	
	/**
	 * functions we will be needing
	 * 
	 * Curriculum_model: getName(), getCurriculumID(), getAllCurriculums(), 
	 * 		delete(), update(), create(), addCurriculumCourseSlot($curriculumCourseSlot),   
	 * 		removeCurriculumCourseSlot($curriculumCourseSlot), setCurriculumType($curriculumType), 
	 * 		setName($name), getCurriculumCourseSlots(), getDateCreated(), getCurriculumType(), 
	 * 
	 * Curriculum_course_slot_model: 
	 * 
	 * Course_model: getAllCourses()
	 * 
	*/

	private $curriculum = NULL; //will hold the current curriculum
	private $courseSlot = NULL; //will hold the current course slot 
	
	public function index()
	{
		//load models
		$this->load->model('Curriculum_model', 'Curriculum_course_slot_model', 'Course_model');
		$curriculum = new Curriculum_Model(); 
		$courseSlot = new Curriculum_course_slot_model();
		
		//call and pass data to initial curriculum view
		$curriculums = $curriculum->getAllCurriculums();
		$data = array();
		
		//creating easy to use array for table
		foreach ($curriculums as $curr) 
		{
			$arr = [
				0 => $curr->getName(),
				1 => $curr->getCurriculumID(),
				2 => $curr->getDateCreated(),
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_choice', $data);
	}
        
    //clone and edit a curriculum
    public function cloneCurriculum($curriculumID, $name) 
    {
		//load curriculum
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $name,
			"type" => "clone"
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $curr->getName(),
				1 => $curr->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', $data);
	}
	
	//edit a current curriculum
	public function editCurriculum($curriculumID)
	{
		//load curriculum
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $curriculum->getName(),
			"type" => "edit"
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $curr->getName(),
				1 => $curr->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', $data);    
	}
	
	//creating a new curriculum
	public function newCurriculum($name, $type)
	{
		$curriculum = new Curriculum_model(); //will we need this for only new or all?
		$curriculum->setName($name);
		$curriculum->setCurriculumType($type);
		$data = array(
			"name" => $name,
			"type" => "new"
		);
		$this->load->view('curriculum_edit', $name);	
	}
	
	//deletes a selected curriculum
	public function deleteCurriculum($curriculumID)
	{
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$curriculum->delete();
	}
	
	public function saveCurriculum($type = NULL)
	{
		if ($type == "edit")
			$curriculum->update();
		else
			$curriculum->create();			
	}
	
	//clone and edit a curriculum course slot
    public function cloneCurriculumCourseSlot($curriculumCourseSlotID, $name) 
    {
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $courseSlot->getValidCourseIDs();
		$data = array(
			"name" => $name,
			"type" => "clone"
		);
		
		//create easy to use array for table
		foreach ($validCourses as $course)
		{
			$arr = [
				0 => $course->getCourseName(),
				1 => $course->getCourseID(),
				2 => $course->getPrerequisiteCourses()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('course_slot_edit', $data);
	}
	
	//clone and edit a curriculum course slot
    public function editCurriculumCourseSlot($curriculumCourseSlotID) 
    {
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $courseSlot->getValidCourseIDs();
		$data = array(
			"name" => $name,
			"type" => "edit"
		);
		
		//create easy to use array for table
		foreach ($validCourses as $course)
		{
			$arr = [
				0 => $course->getCourseName(),
				1 => $course->getCourseID(),
				2 => $course->getPrerequisiteCourses()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('course_slot_edit', $data);
	}

	//create a new curriculum course slot
	public function newCurriculumCourseSlot($name, $minimumGrade)
	{
		$courseSlot = new Curriculum_course_slot_model(); //make this public
		$courseSlot->setCurriculum($curriculum);
		$courseSlot->setMinimumGrade($minimumGrade);
		$courseSlot->setName($name);
		
		$data = array(
			"name" => $name,
			"type" => "new"
		);
		
		$this->load->view('course_slot_edit', $data);
	}
	
	//delete a curriculum course slot
	public function deleteCurriculumCourseSlot($curriculumCourseSlotID)
	{
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$curriculum->removeCurriculumCourseSlot($courseSlot);
		$courseSlot->delete(); //should we delete from db or will it be used by other curriculums?
	}
	
	//cancel a curriculum course slot editing
	public function cancelCurriculumCourseSlotEdit()
	{
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $curriculum->getName(),
			"type" => "edit"
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $curr->getName(),
				1 => $curr->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', $data);   
	}
	
	//save a curriculum course slot
	public function setCurriculumCourseSlot($validCourseIDs, $type) 
	{
		//populate course slot with course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
		
		//do we want to update the database only when the whole curriculum is saved?
		/*
		if ($type == "edit")
			$courseSlot->update();
		else
			$courseSlot->create();
		*/
	}
}
