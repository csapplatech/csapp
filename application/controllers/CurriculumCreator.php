<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CurriculumCreator extends CI_Controller {
	
	public function index()
	{
		//load models
		$this->load->model('Curriculum_model', 'Curriculum_course_slot_model', 'Course_model');
		$curriculum = new Curriculum_Model(); 
		
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
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		
		//load curriculum
		$_SESSION['curriculum'] = new Curriculum_Model();
	    $_SESSION['curriculum']->loadPropertiesFromPrimaryKey($curriculumID);
		$_SESSION['courseSlot'] = $_SESSION['curriculum']->getAllCurriculumCourseSlots();
		$_SESSION['curriculumCreationMethod'] = "clone";
		$data = array(
			"name" => $_SESSION['curriculum']->getName(),
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $slot->getName(),
				1 => $slot->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data));
	}
	
	//edit a current curriculum
	public function editCurriculum($curriculumID = NULL) //post: curriculum
	{
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
			
		//load curriculum
		$_SESSION['curriculum'] = new Curriculum_Model();
	    $_SESSION['curriculum']->loadPropertiesFromPrimaryKey($curriculumID);
		$courseSlots = $_SESSION['curriculum']->getAllCurriculumCourseSlots();
		$_SESSION['curriculumCreationMethod'] = "edit";
		$data = array(
			"name" => $_SESSION['curriculum']->getName(),
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $slot->getName(),
				1 => $slot->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data));    
	}
	
	//creating a new curriculum
	public function newCurriculum()
	{
		$_SESSION['curriculum'] = new Curriculum_model(); 
		$_SESSION['curriculumCreationMethod'] = "new";
		$data = array(
			"name" => "New Curriculum",
		);
		$this->load->view('curriculum_edit');	
	}
	
	//deletes a selected curriculum
	public function deleteCurriculum($curriculumID = NULL) //post: curriculum
	{
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		
		$_SESSION['curriculum']->loadPropertiesFromPrimaryKey($curriculumID);
		$_SESSION['curriculum']->delete();
	}
	
	//saves a curriculum to the database
	public function setCurriculum($name = NULL, $type = NULL) //post: name, type; type being whether the curriculum is a degree, minor, or concentration
	{
		//get arguments
		if ($name == NULL)
			$name = $this->input->post('name');
			
		if ($type == NULL)
			$type = $this->input->post('type');
		
		//set curriculum name
		$_SESSION['curriculum']->setName($name);
		
		//set curriculum type
		if ($type == "Degree")
			$_SESSION['curriculum']->setCurriculumType(1);
		else if ($type == "Minor")
			$_SESSION['curriculum']->setCurriculumType(2);
		else if ($type == "Concentration")
			$_SESSION['curriculum']->setCurriculumType(3);
		
		//save
		if ($_SESSION['curriculumCreationMethod'] == "edit")
			$_SESSION['curriculum']->update(); //update current curriculum for edit
		else
			$_SESSION['curriculum']->create(); //create a new entry for clone/new	
			
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		
		$this->index();
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculumEdit()
	{
		$_SESSION['curriculum'] = new Curriculum_Model(); 
		$_SESSION['courseSlot'] = new Curriculum_course_slot_model();
		$_SESSION['curriculumCreationMethod'] = NULL;
		$_SESSION['curriculumCourseSlotMethod'] = NULL;
		
		//call and pass data to initial curriculum view
		$curriculums = $_SESSION['curriculum']->getAllCurriculums();
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
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
			
		$_SESSION['courseSlot'] = new Curriculum_course_slot_model();
		$_SESSION['courseSlot']->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $_SESSION['courseSlot']->getValidCourseIDs();
		$_SESSION['curriculumCourseSlotMethod'] = "clone";
		$data = array(
			"name" => $_SESSION['courseSlot']->getName(),
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
		
		$this->load->view('course_slot_edit', array('data'=>$data));
	}
	
	//clone and edit a curriculum course slot
    public function editCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
    {
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
		
		$_SESSION['courseSlot'] = new Curriculum_course_slot_model();
		$_SESSION['courseSlot']->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$validCourses = $_SESSION['courseSlot']->getValidCourseIDs();
		$_SESSION['curriculumCourseSlotMethod'] = "edit";
		$data = array(
			"name" => $_SESSION['courseSlot']->getName(),
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
		
		$this->load->view('course_slot_edit', array('data'=>$data));
	}

	//create a new curriculum course slot
	public function newCurriculumCourseSlot()
	{
		$_SESSION['courseSlot'] = new Curriculum_course_slot_model(); 
		$_SESSION['courseSlot']->setCurriculum($_SESSION['curriculum']);
		$_SESSION['curriculumCourseSlotMethod'] = "new";
		$data = array(
			"name" => 'New Curriculum Course Slot',
		);
		
		$this->load->view('course_slot_edit', array('data'=>$data));
	}
	
	//delete a curriculum course slot
	public function deleteCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
	{
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
			
		$_SESSION['courseSlot']->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		$_SESSION['curriculum']->removeCurriculumCourseSlot($_SESSION['courseSlot']);
		$_SESSION['courseSlot']->delete(); 
	}
	
	//cancel a curriculum course slot editing
	public function cancelCurriculumCourseSlotEdit()
	{
		$courseSlots = $_SESSION['curriculum']->getAllCurriculumCourseSlots();
		$data = array(
			"name" => $_SESSION['curriculum']->getName(),
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $slot->getName(),
				1 => $slot->getCurriculumCourseSlotID()
			];
			
			array_push($data, $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data));   
	}
	
	//save a curriculum course slot
	public function setCurriculumCourseSlot($validCourseIDs = NULL, $name = NULL, $minimumGrade = NULL) //post: courseSlot; validCourseIDs(int array); name(string); minimumGrade(string)
	{
		//get arguments
		if ($validCourseIDs == NULL)
			$arr = $this->input->post('courseSlot');
			
		//add logic to grab arguments	
			
		$_SESSION['courseSlot']->setMinimumGrade($minimumGrade);
		$_SESSION['courseSlot']->setName($name);
		
		//populate course slot with the valid course ids
		foreach ($validCourseIDs as $validCourse)
			$_SESSION['courseSlot']->addValidCourseID($validCourse);
			
		$_SESSION['curriculum']->addCurriculumCourseSlot($_SESSION['courseSlot']);
		$this->load->view('curriculum_edit', array('data'=>$data));  
	}
}
