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
		$this->load->view('curriculum_choice', array('data'=>$data));
	}
        
    //clone and edit a curriculum
    public function cloneCurriculum($curriculumID = NULL) //post: curriculum
    {
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		$curriculum = new Curriculum_Model();
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		
		$_SESSION['curriculumCreationMethod'] = "clone";
		$_SESSION['curriculum'] = $curriculum->toSerializedString();
		
		//load curriculum
		$this->loadCurriculumEdit($curriculum);
	}
	
	//edit a current curriculum
	public function editCurriculum($curriculumID = NULL) //post: curriculum
	{
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		$curriculum = new Curriculum_Model();
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		
		$_SESSION['curriculumCreationMethod'] = "edit";
		$_SESSION['curriculum'] = $curriculum->toSerializedString();
			
		//load curriculum
		$this->loadCurriculumEdit($curriculum);
	}
	
	//creating a new curriculum
	public function newCurriculum()
	{
		$curriculum = new Curriculum_model(); 
		$_SESSION['curriculumCreationMethod'] = "new";
		$_SESSION['curriculum'] = $curriculum->toSerializedString();
		$data = array(
			'name' => "New Curriculum"
		);
		$this->load->view('curriculum_edit', array('data'=>$data));	
	}
	
	//deletes a selected curriculum
	public function deleteCurriculum($curriculumID = NULL) //post: curriculum
	{
		//get arguments
		if ($curriculumID == NULL)
			$curriculumID = $this->input->post('curriculum');
		
		$curriculum = new Curriculum_model();
		$curriculum->loadPropertiesFromPrimaryKey($curriculumID);
		$curriculum->delete();
		
		$this->index();
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
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$curriculum->setName($name);
		
		//set curriculum type
		if ($type == "Degree")
			$curriculum->setCurriculumType(1);
		else if ($type == "Minor")
			$curriculum->setCurriculumType(2);
		else if ($type == "Concentration")
			$curriculum->setCurriculumType(3);
		
		//save
		if ($_SESSION['curriculumCreationMethod'] == "edit")
			$curriculum->update(); //update current curriculum for edit
		else
			$curriculum->create(); //create a new entry for clone/new	
			
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		
		$this->index();
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculumEdit()
	{
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		
		$this->index();
	}
		
	//clone and edit a curriculum course slot
    public function cloneCurriculumCourseSlot($courseSlotName = NULL) //post: courseSlot
    {
		//get arguments
		if ($courseSlotName == NULL)
			$courseSlotName = $this->input->post('courseSlot');
		
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		$courseSlot = new Curriculum_course_slot_model();
		
		//match names
		foreach ($courseSlots as $slot)
		{
			$name = $slot->getName();
			
			if (strcmp($name,$courseSlotName) == 0)
			{	
				$courseSlot = $slot;
				break;
			}
		}
			
		$_SESSION['curriculumCourseSlotMethod'] = "clone";
		$courses = new Course_model();
		
		$data = array(
			'name'              => $courseSlot->getName(),
			'courses'            => array(),
			'recommendedQuarter' => $courseSlot->getRecommendedQuarter(),
			'recommendedYear'    => $courseSlot->getRecommendedYear(),
			'minimumGrade'       => $courseSlot->getMinimumGrade(),
			'notes'              => $courseSlot->getNotes()
		);
		
		$availableCourses = $courses->getAllCourses();
		$validCourse = $courseSlot->getValidCourseIDs();
		
		foreach ($availableCourses as $course)
		{
			$arr = [
				'name'    => $course->getCourseName(),
				'id'      => $course->getCourseID(),
				'prereqs' => $course->getPrerequisiteCourses(),
				'number'  => $course->getCourseNumber(),
				'selected'=> FALSE
			];
			
			foreach ($validCourse as $valid)
				if (strcmp($valid, $course->getCourseID()) == 0)
					$arr['selected'] = TRUE;
			
			array_push($data['courses'], $arr);
		}
		
		$_SESSION['courseSlot'] = $courseSlot->toSerializedString();
		$this->load->view('course_slot_edit', array('data'=>$data));
	}
	
	//clone and edit a curriculum course slot
    public function editCurriculumCourseSlot($courseSlotName = NULL) //post: courseSlot
    {
		///get arguments
		if ($courseSlotName == NULL)
			$courseSlotName = $this->input->post('name');
		
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		$courseSlot = new Curriculum_course_slot_model();
		
		//~var_dump($courseSlotName);
		//match names
		foreach ($courseSlots as $slot)
		{
			$name = $slot->getName();
			//~var_dump($name);
			if (strcmp($name,$courseSlotName) == 0)
			{	
				$courseSlot = $slot;
				break;
			}
		}
		
		$_SESSION['curriculumCourseSlotMethod'] = "edit";
		$courses = new Course_model();
		
		$data = array(
			'name'              => $courseSlot->getName(),
			'courses'            => array(),
			'recommendedQuarter' => $courseSlot->getRecommendedQuarter(),
			'recommendedYear'    => $courseSlot->getRecommendedYear(),
			'minimumGrade'       => $courseSlot->getMinimumGrade(),
			'notes'              => $courseSlot->getNotes()
		);
		
		$availableCourses = $courses->getAllCourses();
		$validCourse = $courseSlot->getValidCourseIDs();
		
		foreach ($availableCourses as $course)
		{
			$arr = [
				'name'    => $course->getCourseName(),
				'id'      => $course->getCourseID(),
				'prereqs' => $course->getPrerequisiteCourses(),
				'number'  => $course->getCourseNumber(),
				'selected'=> FALSE
			];
			
			foreach ($validCourse as $valid)
				if (strcmp($valid, $course->getCourseID()) == 0)
					$arr['selected'] = TRUE;
			
			array_push($data['courses'], $arr);
		}
		
		$_SESSION['courseSlot'] = $courseSlot->toSerializedString();
		//~ var_dump($courseSlot);
		//~ var_dump($_SESSION['courseSlot']);
		$this->load->view('course_slot_edit', array('data'=>$data));
	}

	//create a new curriculum course slot
	public function newCurriculumCourseSlot()
	{
		$courseSlot = new Curriculum_course_slot_model();
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$courseSlot->setCurriculum($curriculum);
		$_SESSION['curriculumCourseSlotMethod'] = "new";
		$courses = new Course_model();
		
		$data = array(
			'name'    => 'New Curriculum Course Slot',
			'courses' =>  array()
		);
		
		$availableCourses = $courses->getAllCourses();
		
		foreach ($availableCourses as $course)
		{
			$arr = [
				'name'    => $course->getCourseName(),
				'id'      => $course->getCourseID(),
				'prereqs' => $course->getPrerequisiteCourses(),
				'number'  => $course->getCourseNumber()
			];
			
			array_push($data['courses'], $arr);
		}
		
		$_SESSION['courseSlot'] = $courseSlot->toSerializedString();
		$this->load->view('course_slot_edit', array('data'=>$data));
	}
	
	//delete a curriculum course slot
	public function deleteCurriculumCourseSlot($curriculumCourseSlotID = NULL) //post: courseSlot
	{
		//get arguments
		if ($curriculumCourseSlotID == NULL)
			$curriculumCourseSlotID = $this->input->post('courseSlot');
	
		$courseSlot = new Curriculum_course_slot_model();	
		$courseSlot->loadPropertiesFromPrimaryKey($curriculumCourseSlotID);
		
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$curriculum->removeCurriculumCourseSlot($courseSlot);
		$_SESSION['curriculum'] = $curriculum->toSerializedString();
		
		$courseSlot->delete();
		
		//load curriculum
		$this->loadCurriculumEdit($curriculum);
	}
	
	//cancel a curriculum course slot editing
	public function cancelCurriculumCourseSlot()
	{
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		
		$this->loadCurriculumEdit($curriculum); 
	}
	
	//save a curriculum course slot
	//validCourseIDs(int array); name(string); minimumGrade(string); 
	public function setCurriculumCourseSlot($validCourseIDs = NULL, $name = NULL, $minimumGrade = NULL, $recommendedQuarter = NULL, $recommendedYear = NULL, $notes = NULL) 
	{
		//get arguments
		if ($validCourseIDs == NULL)
			$validCourseIDs = $this->input->post('validCourseIDs');
			
		if ($name == NULL)
			$name = $this->input->post('name');
			
		if ($minimumGrade == NULL)
			$minimumGrade = $this->input->post('minimumGrade');
			
		if ($recommendedQuarter == NULL)
			$recommendedQuarter = $this->input->post('recommendedQuarter');
			
		if ($recommendedYear == NULL)
			$recommendedYear = $this->input->post('recommendedYear');
		
		if ($notes == NULL)
			$notes = $this->input->post('notes');
			
		if (!isset($notes))
			$notes = " ";
			
		//add logic to grab arguments	
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->fromSerializedString($_SESSION['courseSlot']);
		//~ var_dump($courseSlot);
		$courseSlot->setMinimumGrade($minimumGrade);
		$courseSlot->setName($name);
		$courseSlot->setRecommendedQuarter($recommendedQuarter);
		$courseSlot->setRecommendedYear($recommendedYear);
		$courseSlot->setNotes($notes);
		//~ var_dump($courseSlot);

		//populate course slot with the valid course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
					
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		
		$courseSlots = $curriculum->getCurriculumCourseSlots();

		$tempCourseSlot = new Curriculum_course_slot_model();
		$tempCourseSlot->fromSerializedString($_SESSION['courseSlot']);
		//var_dump($tempCourseSlot);
		
		if (strcmp($_SESSION['curriculumCourseSlotMethod'], 'edit') == 0)
		{
			$tempCourseSlot = new Curriculum_course_slot_model();
			$tempCourseSlot->fromSerializedString($_SESSION['courseSlot']);
			foreach ($courseSlots as $slot)
			{
				if (strcmp($tempCourseSlot->getName(), $slot->getName()) == 0)
				{
					$curriculum->removeCurriculumCourseSlot($tempCourseSlot);
					break;
				}
			}
		}

		$curriculum->addCurriculumCourseSlot($courseSlot);
		
		$_SESSION['courseSlot'] = $courseSlot->toSerializedString();
		$_SESSION['curriculum'] = $curriculum->toSerializedString();
		
		$this->loadCurriculumEdit($curriculum);   
	}

	private function loadCurriculumEdit($curriculum)
	{
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		
		$type = $curriculum->getCurriculumType();
		if ($type == 1)
			$curriculumType = 'Degree';
		else if ($type == 2)
			$curriculumType = 'Minor';
		else if ($type == 3) 
			$curriculumType = 'Concentration';
		
		$data = array(
			'name'   => $curriculum->getName(),
			'course' => array(),
			'type'   => $curriculumType
		);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [
				0 => $slot->getName(),
				1 => $slot->getCurriculumCourseSlotID()
			];
			
			array_push($data['course'], $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data)); 
	}
}
