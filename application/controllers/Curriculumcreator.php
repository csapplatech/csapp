<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Curriculumcreator extends CI_Controller {
	
	public function index()
	{
		//load models
		$this->load->model('Curriculum_model', 'Curriculum_course_slot_model', 'Course_model');
		$curriculum = new Curriculum_Model(); 
		$_SESSION['MaxCurriculumIndex'] = 0;
		
		//call and pass data to initial curriculum view
		$curriculums = $curriculum->getAllCurriculums();
		$data = array();
		
		//creating easy to use array for table
		foreach ($curriculums as $curr) 
		{
			$arr = [ 
				'name' => $curr->getName(),
				'id'   => $curr->getCurriculumID(),
				'date' => $curr->getDateCreated(),
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
			'name'    => "New Curriculum",
			'courses' => array(),
			'type'    => ""
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
		unset($_SESSION['MaxCurriculumIndex']);
				
		$this->index();
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculumEdit()
	{
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		unset($_SESSION['MaxCurriculumIndex']);
		
		$this->index();
	}
		
	//clone and edit a curriculum course slot
    public function cloneCurriculumCourseSlot() 
    {
		$_SESSION['curriculumCourseSlotMethod'] = "clone";
		$this->loadCurriculumCourseSlotEdit();
	}
	
	//clone and edit a curriculum course slot
    public function editCurriculumCourseSlot() 
    {
		$_SESSION['curriculumCourseSlotMethod'] = "edit";
		$this->loadCurriculumCourseSlotEdit();
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
	public function setCurriculumCourseSlot($validCourseIDs = NULL, $name = NULL, $minimumGrade = NULL, $recommendedQuarter = NULL, $recommendedYear = NULL, $notes = NULL, $index = NULL) 
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
			
		if ($index == NULL)
			$index = $this->input->post('index');
			
		if (!isset($notes))
			$notes = " ";
			
		//add logic to grab arguments	
		$courseSlot = new Curriculum_course_slot_model();
		$courseSlot->fromSerializedString($_SESSION['courseSlot']);
		$courseSlot->setMinimumGrade($minimumGrade);
		$courseSlot->setName($name);
		$courseSlot->setRecommendedQuarter($recommendedQuarter);
		$courseSlot->setRecommendedYear($recommendedYear);
		$courseSlot->setNotes($notes);

		//populate course slot with the valid course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
					
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		
		$courseSlot->setCurriculumIndex($_SESSION['MaxCurriculumIndex']++);
		//~ $currIndex = $courseSlot->getCurriculumIndex();
		//~ 
		//Handle non-unique indeces
		//~ foreach ($courseSlots as $slot)
		//~ {
			//~ if ($slot->getCurriculumIndex() == $currIndex)
			//~ {
				//~ 
			//~ }
		//~ }


		if (strcmp($_SESSION['curriculumCourseSlotMethod'], 'edit') == 0)
		{
			$tempCourseSlot = new Curriculum_course_slot_model();
			$tempCourseSlot->fromSerializedString($_SESSION['courseSlot']);
			$tempCourseSlotIndex = $tempCourseSlot->getCurriculumIndex();			
			
			foreach ($courseSlots as $slot)
			{
				if ($tempCourseSlotIndex == $slot->getCurriculumIndex())
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

	//passes data to and loads curriculum course slot edit view
	private function loadCurriculumCourseSlotEdit($courseSlotIndex = NULL)
	{
		///get arguments
		if ($courseSlotIndex == NULL)
			$courseSlotIndex = $this->input->post('courseSlot'); //yoooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo
		
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		$courseSlot = new Curriculum_course_slot_model();
		
		//match indeces
		foreach ($courseSlots as $slot)
		{
			$index = $slot->getCurriculumIndex();
			if ($index == $courseSlotIndex)
			{	
				$courseSlot = $slot;
				break;
			}
		}
		
		$courses = new Course_model();
		
		$data = array(
			'name'               => $courseSlot->getName(),
			'courses'            => array(),
			'recommendedQuarter' => $courseSlot->getRecommendedQuarter(),
			'recommendedYear'    => $courseSlot->getRecommendedYear(),
			'minimumGrade'       => $courseSlot->getMinimumGrade(),
			'notes'              => $courseSlot->getNotes(),
			'index'				 => $courseSlotIndex
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
		
		//~ var_dump($data);
		$_SESSION['courseSlot'] = $courseSlot->toSerializedString();
		$this->load->view('course_slot_edit', array('data'=>$data));
	}
	
	//passes data to and loads curriculum edit view
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
		var_dump($data);
		
		//create easy to use array for table
		foreach ($courseSlots as $slot)
		{
			$arr = [ //yooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo changed indeces from 0-2 to strings
				'name' => $slot->getName(),
				'id'   => $slot->getCurriculumCourseSlotID(),
				'index'=> $slot->getCurriculumIndex()
			];
			
			array_push($data['course'], $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data)); 
	}
}
