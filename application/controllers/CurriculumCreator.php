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
	 * 	const CURRICULUM_TYPE_DEGREE = 1;
		const CURRICULUM_TYPE_MINOR = 2;
		const CURRICULUM_TYPE_CONCENTRATION = 3;
	*/
	
	//need setters for curriculum/courseslot names and curriculum type (major, minor, concentration)

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
		array_push($data, 'test');
		$this->load->view('curriculum_choice', array('data'=>$data));
	}
        
    //clone and edit a curriculum
    public function cloneCurriculum($curriculumID) 
    {
		//load curriculum
		$curriculum = new Curriculum_Model();
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $curriculum->getName(),
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
		$curriculum = new Curriculum_Model();
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
	public function newCurriculum()
	{
		$curriculum = new Curriculum_model(); //will we need this for only new or all?
		$data = array(
			"name" => "New Curriculum",
			"type" => "new"
		);
		$this->load->view('curriculum_edit');	
	}
	
	//deletes a selected curriculum
	public function deleteCurriculum($curriculumID)
	{
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$curriculum->delete();
	}
	
	//saves a curriculum to the database
	public function setCurriculum($type = NULL) //type being whether the curriculum is new, cloned, or edited
	{
		if ($type == "edit")
			$curriculum->update(); //update current curriculum for edit
		else
			$curriculum->create(); //create a new entry for clone/new	
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculumEdit()
	{
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
		array_push($data, 'test');
		$this->load->view('curriculum_choice', array('data'=>$data));
	}
	
	//set the name for a curriculum
	public function setCurriculumName($name)
	{
		$curriculum->setName($name);
	}
	
	//set the curriculum type
	public function setCurriculumType($type)  //type(int): 1 - Degree, 2 - Minor, 3 - Concentration
	{
		$curriculum->setCurriculumType($type);
	}
		
	//clone and edit a curriculum course slot
    public function cloneCurriculumCourseSlot($curriculumCourseSlotID) 
    {
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $courseSlot->getValidCourseIDs();
		$data = array(
			"name" => $courseSlot->getName(),
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
			"name" => $courseSlot->getName(),
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
	public function newCurriculumCourseSlot()
	{
		$courseSlot = new Curriculum_course_slot_model(); //make this public
		$courseSlot->setCurriculum($curriculum);
		
		$data = array(
			"name" => 'New Curriculum Course Slot',
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
	public function setCurriculumCourseSlot($validCourseIDs, $name, $minimumGrade) //validCourseIDs(int array); name(string); minimumGrade(string)
	{
		$courseSlot->setMinimumGrade($minimumGrade);
		$courseSlot->setName($name);
		
		//populate course slot with the valid course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
			
		$curriculum->addCurriculumCourseSlot($courseSlot);
		$this->load->view('curriculum_edit', $data);  
	}
}
