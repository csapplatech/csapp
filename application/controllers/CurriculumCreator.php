<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurriculumCreator extends CI_Controller {

	private $curriculum = NULL; //will hold the current curriculum
	private $courseSlot = NULL; //will hold the current course slot 
	private $curriculumType = NULL; //(string): clone, edit, new
	private $courseSlotType = NULL; //(string): clone, edit, new
	
	public function index()
	{
		//grab global
		global $curriculum, $courseSlot;
		
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
    public function cloneCurriculum($curriculumID = NULL) //post: curriculum
    {
		//grab global
		global $curriculum, $curriculumType;
		
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		
		//load curriculum
		$curriculum = new Curriculum_Model();
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$curriculumType = "clone";
		$data = array(
			"name" => $curriculum->getName(),
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
	public function editCurriculum($curriculumID = NULL) //post: curriculum
	{
		//grab global
		global $curriculum, $curriculumType;
		
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
			
		//load curriculum
		$curriculum = new Curriculum_Model();
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$curriculumType = "edit";
		$data = array(
			"name" => $curriculum->getName(),
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
		//grab global
		global $curriculum, $curriculumType;
		
		$curriculum = new Curriculum_model(); 
		$curriculumType = "new";
		$data = array(
			"name" => "New Curriculum",
		);
		$this->load->view('curriculum_edit');	
	}
	
	//deletes a selected curriculum
	public function deleteCurriculum($curriculumID = NULL) //post: curriculum
	{
		//grab global
		global $curriculum;
		
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$curriculum->delete();
	}
	
	//saves a curriculum to the database
	public function setCurriculum($name = NULL, $type = NULL) //post: name, type; type being whether the curriculum is a degree, minor, or concentration
	{
		//grab global
		global $curriculum, $curriculumType;
		
		//get arguments
		if ($name == NULL)
			$name = $this->input->post('name');
			
		if ($type == NULL)
			$type = $this->input->post('type');
		
		//set curriculum name
		$curriculum->setName($name);
		
		//set curriculum type
		if ($type == "Degree")
			$curriculum->setCurriculumType(1);
		else if ($type == "Minor")
			$curriculum->setCurriculumType(2);
		else if ($type == "Concentration")
			$curriculum->setCurriculumType(3);
		
		//save
		if ($curriculumType == "edit")
			$curriculum->update(); //update current curriculum for edit
		else
			$curriculum->create(); //create a new entry for clone/new	
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculumEdit()
	{
		//grab global
		global $curriculum, $curriculumType, $courseSlot, $courseSlotType;
		
		$curriculum = new Curriculum_Model(); 
		$courseSlot = new Curriculum_course_slot_model();
		$curriculumType = NULL;
		$courseSlotType = NULL;
		
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
		
	//clone and edit a curriculum course slot
    public function cloneCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
    {
		//grab global
		global $courseSlot, $courseSlotType;
		
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
			
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $courseSlot->getValidCourseIDs();
		$courseSlotType = "clone";
		$data = array(
			"name" => $courseSlot->getName(),
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
    public function editCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
    {
		//grab global
		global $courseSlot, $courseSlotType;
		
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
		
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $courseSlot->getValidCourseIDs();
		$courseSlotType = "edit";
		$data = array(
			"name" => $courseSlot->getName(),
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
		//grab global
		global $curriculum, $courseSlot, $courseSlotType;
		
		$courseSlot = new Curriculum_course_slot_model(); //make this public
		$courseSlot->setCurriculum($curriculum);
		$courseSlotType = "new";
		$data = array(
			"name" => 'New Curriculum Course Slot',
		);
		
		$this->load->view('course_slot_edit', $data);
	}
	
	//delete a curriculum course slot
	public function deleteCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
	{
		//grab global
		global $curriculum, $courseSlot;
		
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
			
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$curriculum->removeCurriculumCourseSlot($courseSlot);
		$courseSlot->delete(); //should we delete from db or will it be used by other curriculums?
	}
	
	//cancel a curriculum course slot editing
	public function cancelCurriculumCourseSlotEdit()
	{
		//grab global
		global $curriculum;
		
		$courseSlots = $curriculum->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $curriculum->getName(),
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
	public function setCurriculumCourseSlot($validCourseIDs = NULL, $name = NULL, $minimumGrade = NULL) //post: courseSlot; validCourseIDs(int array); name(string); minimumGrade(string)
	{
		//grab global
		global $curriculum, $courseSlot;
		
		//get arguments
		if ($validCourseIDs == NULL)
			$arr = $this->input->post('courseSlot');
			
		//add logic to grab arguments	
			
		$courseSlot->setMinimumGrade($minimumGrade);
		$courseSlot->setName($name);
		
		//populate course slot with the valid course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
			
		$curriculum->addCurriculumCourseSlot($courseSlot);
		$this->load->view('curriculum_edit', $data);  
	}
}
