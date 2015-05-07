<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Curriculumcreator extends CI_Controller {
	
	public function index() 
	{
		//load models
		$this->load->model('Curriculum_model', 'Curriculum_course_slot_model', 'Course_model', 'User_model');
		$this->load->helper('url');
		$user = new User_model();
		
		//~ //verify the user is valid and a program chair
		if (isset($_SESSION['UserID']))
		{	
			$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']);
			if (!($user->isProgramChair()))
				redirect('login');
		}
		else
			redirect('login');
		
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
		if (isset($_SESSION['curriculum']))
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
		if (isset($_SESSION['curriculumCreationMethod']))
		{
			if ($_SESSION['curriculumCreationMethod'] == "edit")
				$curriculum->update(); //update current curriculum for edit
			else
				$curriculum->create(); //create a new entry for clone/new
		}
					
		//find all current pre/coreqs and 			
					
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		
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
							
						//find and delete old prereqs
						if (!empty($previousPrereqSlots))// and !empty($reqs['prereqs']))
							foreach ($previousPrereqSlots as $previousSlot)
								$slot->removeCourseSlotRequisite($previousSlot);
						
						//delete any old corequisites		
						if (!empty($previousCoreqSlots))// and !empty($reqs['coreqs']))
							foreach ($previousCoreqSlots as $previousSlot)
								$slot->removeCourseSlotRequisite($previousSlot);
								
						//save new coreqs
						if (!empty($reqs['coreqs']))
						{
							$co = new Curriculum_course_slot_model();
							foreach ($reqs['coreqs'] as $r)
							{
								$co->fromSerializedString($r);
								$slot->addCourseSlotCorequisite($co);
							}
						}
						
						//save new prereqs
						//need to make sure you can't make one slot a co and pre
						if (!empty($reqs['prereqs']))
						{
							$pre = new Curriculum_course_slot_model();							
							foreach ($reqs['prereqs'] as $r)
							{
								$pre->fromSerializedString($r);
								$slot->addCourseSlotPrerequisite($pre);
							}
						}						
										
						break;
					}
				}	
			}
		} 
		
		$curriculum->update(); //update current curriculum for edit

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
		var_dump($_SESSION['curriculumCourseSlotMethod']);		
		echo "<br><br>";
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
				
		$courseSlots = $curriculum->getCurriculumCourseSlots();
		$prerequisites = array();
		$corequisites  = array();
		
		//Handle prereq and coreq duplicates and set to coreq
		if (isset($prereqIDs) and isset($coreqIDs))
			foreach ($prereqIDs as $pre)
				if(($key = array_search($pre, $coreqIDs)) !== false) 
					unset($prereqIDs[$key]);
		
		$largestIndex = 1;
		//Handle non-unique curriculum indexes
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
			
			//grab coreq course slots
			if (isset($coreqIDs))
				foreach ($coreqIDs as $c)
					if ($currentIndex == $c)
						array_push($corequisites, $slot);
		}
				
		if ($largestIndex > 1)
			$_SESSION['maxCurriculumIndex'] = $largestIndex;
		
		//remove previous valid courses
		$previousValidCourseIDs = $courseSlot->getValidCourseIDs();
		
		if (isset($previousValidCourseIDs))
			foreach ($previousValidCourseIDs as $prevID)
				$courseSlot->removeValidCourseID($prevID);
		
		//populate course slot with the new valid course ids
		if (isset($validCourseIDs))
			foreach ($validCourseIDs as $validCourse)
				$courseSlot->addValidCourseID($validCourse);
				
		//add course slot to the curriculum based on its creation method			
		if (strcmp($_SESSION['curriculumCourseSlotMethod'], "edit") == 0)
			$curriculum->updateCurriculumCourseSlot($courseSlot);
		else 
		{
			$courseSlot->setCurriculumIndex(++$_SESSION['maxCurriculumIndex']);
			$curriculum->addCurriculumCourseSlot($courseSlot);
		}		
		
		//add pro/coreqs to session 
		//be sure to delete old reqs that were altered this session
		if (!empty($_SESSION['reqs']))
		{
			$tempReqs = $_SESSION['reqs'];

			$currIndex = $courseSlot->getCurriculumIndex();
			foreach ($tempReqs as $reqs)
			{
				$reqsSlot = new Curriculum_course_slot_model();
				$reqsSlot->fromSerializedString($reqs['slot']);
				if ($reqsSlot->getCurriculumIndex() == $currIndex)
					if(($key = array_search($reqs, $tempReqs)) !== false) 
						unset($tempReqs[$key]);
			}
			
			$_SESSION['reqs'] = $tempReqs;
		}
				
		$arr = [
			'slot'    => $courseSlot->toSerializedString(),
			'prereqs' => array(),
			'coreqs'  => array() 
		];
		
		//add any new prereqs
		if (!empty($prerequisites))
			foreach ($prerequisites as $p)
				array_push($arr['prereqs'], $p->toSerializedString());
		
		//add any new coreqs		
		if (!empty($corequisites))
			foreach ($corequisites as $c)
				array_push($arr['coreqs'], $c->toSerializedString());
				
		//need to make sure reqs is set so the push doesn't error		
		if (!isset($_SESSION['reqs']))
			$_SESSION['reqs'] = array();
			
		array_push($_SESSION['reqs'], $arr);
		
		//set session variables after serializing
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
		
		//find selected course slot
		foreach ($courseSlots as $slot)
		{
			$index = $slot->getCurriculumIndex();
			if ($index == $courseSlotIndex)
			{	
				//set values manually for clone so it doesn't copy courseSlotID 
				if (strcmp($_SESSION['curriculumCourseSlotMethod'], "clone") == 0)
				{
					$courseSlot->setMinimumGrade($slot->getMinimumGrade());
					$courseSlot->setName($slot->getName());
					$courseSlot->setRecommendedQuarter($slot->getRecommendedQuarter());
					$courseSlot->setRecommendedYear($slot->getRecommendedYear());
					$courseSlot->setNotes($slot->getNotes());
					$courseSlot->setCurriculum($curriculum);
					$courseSlot->setCurriculumIndex($slot->getCurriculumIndex());
					
					$slotValids = $slot->getValidCourseIDs();
					foreach ($slotValids as $valid)
						$courseSlot->addValidCourseID($valid);
				}	
				else //copy entire slot for edit
					$courseSlot = $slot;
				break;
			}
		}
		
		//set the name to 
		if ($courseSlot->getName() == NULL)
			$courseSlot->setName("New Curriculum Course Slot");
		
		$courses = new Course_model();
		
		//set default values for data array if it's a new slot				
		if (strcmp($_SESSION['curriculumCourseSlotMethod'], "new") == 0)
		{
			$courseSlot = new Curriculum_course_slot_model();
			$data = array(
				'name'               => "New Curriculum Course Slot",
				'courses'            => array(),
				'recommendedQuarter' => "Fall",
				'recommendedYear'    => "Freshman",
				'minimumGrade'       => "F",
				'notes'              => " ",
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
						
		//find if the co or pre reqs have been edited in this session
		$prereqsEdited = FALSE;
		$coreqsEdited  = FALSE;
		
		if (!empty($_SESSION['reqs']))
		{
			foreach ($_SESSION['reqs'] as $reqs)
			{
				foreach ($courseSlots as $slot) 
				{
					$reqsSlot = new Curriculum_course_slot_model();
					$reqsSlot->fromSerializedString($reqs['slot']);
					//find the right course slot
					if ($slot->getCurriculumIndex() == $reqsSlot->getCurriculumIndex()) 
					{	
						$prereqsEdited = TRUE;
						$coreqsEdited = TRUE;
					}
				}
			}
		}
		
		//Pass possible and chosen prereq slots
		$currentIndex = $courseSlot->getCurriculumIndex();
		$courseSlotIndex = $courseSlot->getCurriculumIndex();
		$slotPrereqs = $courseSlot->getPrequisiteCourseSlots();

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
				if (!empty($slotPrereqs))
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
		$slotCoreqs = $courseSlot->getCorequisiteCourseSlots();	
		
		foreach ($courseSlots as $slot)
		{
			$arr = [ 
				'name'     => $slot->getName(),
				'id'       => $slot->getCurriculumCourseSlotID(),
				'index'    => $slot->getCurriculumIndex(),
				'selected' => FALSE
			];
			
			if (!$prereqsEdited)
			{	//normal coreq functionality
				if (isset($slotCoreqs))
					foreach ($slotCoreqs as $coreq)
						if ($coreq->getCurriculumIndex() == $arr['index'])
							$arr['selected'] = TRUE;
			}
			else
			{	//grabbing coreqs from session
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
				'name'    => $slot->getName(),
				'id'      => $slot->getCurriculumCourseSlotID(),
				'index'   => $slot->getCurriculumIndex(),
				'quarter' => $slot->getRecommendedQuarter(),
				'year'    => $slot->getRecommendedYear()
			];
			
			array_push($data['course'], $arr);
		}
		
		$this->load->view('curriculum_edit', array('data'=>$data)); 
	}
}
