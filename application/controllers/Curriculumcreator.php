<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Curriculumcreator extends CI_Controller {
	
	public function index()
	{
		//load models
		$this->load->model('Curriculum_model', 'Curriculum_course_slot_model', 'Course_model');
		$curriculum = new Curriculum_Model(); 
		$_SESSION['maxCurriculumIndex'] = 1;
		$_SESSION['reqs'] = array();
		
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
		$curriculum->setName('New Curriculum');
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
		
		//save curriculum
		if ($_SESSION['curriculumCreationMethod'] == "edit")
			$curriculum->update(); //update current curriculum for edit
		else
			$curriculum->create(); //create a new entry for clone/new	
				
////////////////////////////////////////////////////////////////////////		
		
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		
		echo '<br>120<br>';
		var_dump($_SESSION['reqs']);
		
		//find and delete old reqs and save new ones
		if (isset($_SESSION['reqs']))
		{
			foreach ($_SESSION['reqs'] as $reqs)
			{
				$reqSlot = new Curriculum_course_slot_model();
				$reqSlot->fromSerializedString($reqs['slot']);
				$reqSlotIndex = $reqSlot->getCurriculumIndex();
				
				foreach ($courseSlots as $slot) 
				{
					//find the right course slot
					if ($slot->getCurriculumIndex() == $reqSlotIndex) 
					{			
						$previousPrereqSlots = $slot->getPrequisiteCourseSlots();
						$previousCoreqSlots  = $slot->getCorequisiteCourseSlots();	
							
						//find old reqs and delete any that should no longer exist
						if (isset($previousPrereqSlots) and isset($reqs['prereqs']))
							foreach ($previousPrereqSlots as $previousSlot)
								$courseSlot->removeCourseSlotRequisite($previousSlot);
								
						if (isset($previousCoreqSlots) and isset($reqs['coreqs']))
							foreach ($previousCoreqSlots as $previousSlot)
								$courseSlot->removeCourseSlotRequisite($previousSlot);
						
						//save new prereqs
						if (isset($reqs['prereqs'])) //will this save it correctly?
						{
							$pre = new Curriculum_course_slot_model();
							foreach ($reqs['prereqs'] as $r)
							{
								$pre->fromSerializedString($r);
								$slot->addCourseSlotPrerequisite($pre);
							}
						}
						
						//save new coreqs
						if (isset($reqs['coreqs'])) //will this save it correctly?
						{
							$co = new Curriculum_course_slot_model();
							foreach ($reqs['coreqs'] as $r)
							{
								$co->fromSerializedString($r);
								$slot->addCourseSlotPrerequisite($co);
							}
						}
											
						break;
					}
				}	
			}
		} 
	
////////////////////////////////////////////////////////////////////////		
	
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		unset($_SESSION['maxCurriculumIndex']);
		unset($_SESSION['reqs']);
				
		$this->index();
	}
	
	//cancelling an edit to a curriculum
	public function cancelCurriculum()
	{
		unset($_SESSION['curriculum']);
		unset($_SESSION['courseSlot']);
		unset($_SESSION['curriculumCreationMethod']);
		unset($_SESSION['curriculumCourseSlotMethod']);
		unset($_SESSION['maxCurriculumIndex']);
		unset($_SESSION['reqs']);
		
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
		$_SESSION['curriculumCourseSlotMethod'] = "new";
		$this->loadCurriculumCourseSlotEdit();
	}
	
	//delete a curriculum course slot
	public function deleteCurriculumCourseSlot($courseSlotIndex = NULL) 
	{
		//get arguments
		if ($courseSlotIndex == NULL)
			$courseSlotIndex = $this->input->post('courseSlot');
	
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
	public function setCurriculumCourseSlot($validCourseIDs = NULL, $name = NULL, $minimumGrade = NULL, $recommendedQuarter = NULL, $recommendedYear = NULL, $notes = NULL, $index = NULL, $prereqIDs = NULL, $coreqIDs = NULL) 
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
			
		if ($prereqIDs == NULL)
			$prereqIDs = $this->input->post('prereqIDs');
			
		if ($coreqIDs == NULL)
			$coreqIDs = $this->input->post('coreqIDs');
			
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
		
		$curriculum = new Curriculum_model();
		$curriculum->fromSerializedString($_SESSION['curriculum']);
		
////////////////////////////////////////////////////////////////////////
		
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		$prerequisites = array();
		$corequisites  = array();
		
		$largestIndex = 0;
		//Handle non-unique indeces
		foreach ($courseSlots as $slot)
		{
			$currentIndex = $slot->getCurriculumIndex();
			if ($currentIndex > $largestIndex)
				$largestIndex = $currentIndex;
				
			//grab prereq course slots 
			if (isset($prereqIDs))
				foreach ($prereqIDs as $p)
					if ($currentIndex == $p)
						array_push($prerequisites, $slot);
			
			if (isset($coreqIDs))
				foreach ($coreqIDs as $p)
					if ($currentIndex == $p)
						array_push($corequisites, $slot);
		}
				
		if ($largestIndex > 0)
			$_SESSION['maxCurriculumIndex'] = $largestIndex + 1;
		
		if (isset($prerequisites) or isset($corequisites))
		{
			//be sure to delete old reqs that were altered this session
			if (isset($_SESSION['reqs']))
			{
				$currIndex = $courseSlot->getCurriculumIndex();
				foreach ($_SESSION['reqs'] as $reqs)
				{
					$reqsSlot = new Curriculum_course_slot_model();
					$reqsSlot->fromSerializedString($reqs['slot']);
					if ($reqsSlot->getCurriculumIndex() == $currIndex)
						unset($reqs); //will this delete the sessoin array??
				}
			}
					
			$arr = [
				'slot'    => $courseSlot->toSerializedString(),
				'prereqs' => array(),
				'coreqs'  => array() 
			];
			
			if (isset($prerequisites))
				foreach ($prerequisites as $p)
					array_push($arr['prereqs'], $p->toSerializedString());
					
			if (isset($corequisites))
				foreach ($corequisites as $c)
					array_push($arr['coreqs'], $c->toSerializedString());
						
			array_push($_SESSION['reqs'], $arr);
		}
		
///////////////////////////////////////////////////////////////////////					
		
		//remove previous valid courses
		$previousValidCourseIDs = $courseSlot->getValidCourseIDs();
		if (isset($previousValidCourseIDs))
			foreach ($previousValidCourseIDs as $prevID)
				$courseSlot->removeValidCourseID($prevID);

		//populate course slot with the new valid course ids
		foreach ($validCourseIDs as $validCourse)
			$courseSlot->addValidCourseID($validCourse);
					
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
		else 
			$courseSlot->setCurriculumIndex($_SESSION['maxCurriculumIndex']++);

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
			$courseSlotIndex = $this->input->post('courseSlot'); 
		
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
		
		if ($courseSlot->getName() == NULL)
			$courseSlot->setName("New Curriculum Course Slot");
		
		$courses = new Course_model();
						
		if (strcmp($_SESSION['curriculumCourseSlotMethod'], "new") == 0)
		{
			$courseSlot = new Curriculum_course_slot_model();
			$data = array(
				'name'               => "New Curriculum Course Slot",
				'courses'            => array(),
				'recommendedQuarter' => $courseSlot->getRecommendedQuarter(),
				'recommendedYear'    => $courseSlot->getRecommendedYear(),
				'minimumGrade'       => $courseSlot->getMinimumGrade(),
				'notes'              => $courseSlot->getNotes(),
				'index'				 => $courseSlotIndex,
				'prereqs'            => array(),
				'coreqs'             => array(), 
			);
		}
		else
		{
			$data = array(
				'name'               => $courseSlot->getName(),
				'courses'            => array(),
				'recommendedQuarter' => $courseSlot->getRecommendedQuarter(),
				'recommendedYear'    => $courseSlot->getRecommendedYear(),
				'minimumGrade'       => $courseSlot->getMinimumGrade(),
				'notes'              => $courseSlot->getNotes(),
				'index'				 => $courseSlotIndex,
				'prereqs'            => array(),
				'coreqs'             => array(), 
			);
		}
				
////////////////////////////////////////////////////////////////////////		
		
		//find if the co or pre reqs have been edited in this session
		$prereqsEdited = FALSE;
		$coreqsEdited  = FALSE;
		
		foreach ($_SESSION['reqs'] as $reqs)
		{
			foreach ($courseSlots as $slot) 
			{
				$reqsSlot = new Curriculum_course_slot_model();
				$reqsSlot->fromSerializedString($reqs['slot']);
				//find the right course slot
				if ($slot->getCurriculumIndex() == $reqsSlot->getCurriculumIndex()) 
				{			
					if (isset($reqs['prereqs']))
						$prereqsEdited = TRUE;
					if (isset($reqs['coreqs']))
						$coreqsEdited = TRUE;
				}
			}
		}
		
		//Pass possible and chosen prereq slots
		$currentIndex = $courseSlot->getCurriculumIndex();
		
		foreach ($courseSlots as $slot)
		{
			$arr = [ 
				'name'     => $slot->getName(),
				'id'       => $slot->getCurriculumCourseSlotID(),
				'index'    => $slot->getCurriculumIndex(),
				'selected' => FALSE
			];
			
			if (!$prereqsEdited)
			{	//normal prereq functionality
				$slotPrereqs = $slot->getPrequisiteCourseSlots();
				if (isset($slotPrereqs))
					foreach ($slotPrereqs as $prereq)
						if ($prereq->getCurriculumIndex() == $arr['index'])
							$arr['selected'] = TRUE;
			}
			else
			{	//grabbing prereqs from session
				$currReq = new Curriculum_course_slot_model();
				foreach ($_SESSION['reqs'] as $reqs)
				{
					foreach ($reqs['prereqs'] as $preRekts)
					{
						$currReq->fromSerializedString($preRekts);
						if ($currReq->getCurriculumIndex() == $arr['index'])
							$arr['selected'] = TRUE;
					}
				}
			}
						
			if ($currentIndex != $arr['index'])
					array_push($data['prereqs'], $arr);
		}
				
		//Pass possible and chosen coreq slots
		foreach ($courseSlots as $slot)
		{
			$arr = [ 
				'name'     => $slot->getName(),
				'id'       => $slot->getCurriculumCourseSlotID(),
				'index'    => $slot->getCurriculumIndex(),
				'selected' => FALSE
			];
			
			if (!$prereqsEdited)
			{	//normal prereq functionality
				$slotCoreqs = $slot->getCorequisiteCourseSlots();
				if (isset($slotCoreqs))
					foreach ($slotCoreqs as $coreq)
						if ($coreq->getCurriculumIndex() == $arr['index'])
							$arr['selected'] = TRUE;
			}
			else
			{	//grabbing prereqs from session
				$currReq = new Curriculum_course_slot_model();
				foreach ($_SESSION['reqs'] as $reqs)
				{
					foreach ($reqs['coreqs'] as $coRekts)
					{
						$currReq->fromSerializedString($coRekts);
						if ($currReq->getCurriculumIndex() == $arr['index'])
							$arr['selected'] = TRUE;
					}
				}
			}
						
			if ($currentIndex != $arr['index'])
					array_push($data['coreqs'], $arr);
		}
				
////////////////////////////////////////////////////////////////////////		
		
		//get all available courses and pass to data
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
	
	//passes data to and loads curriculum edit view
	private function loadCurriculumEdit($curriculum)
	{
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		
		$type = $curriculum->getCurriculumType();
		$curriculumType = NULL;
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
				'name' => $slot->getName(),
				'id'   => $slot->getCurriculumCourseSlotID(),
				'index'=> $slot->getCurriculumIndex()
			];
			
			array_push($data['course'], $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data)); 
	}
}
