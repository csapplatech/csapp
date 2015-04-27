<?php
/**
 * course_model short summary.
 *
 * A model used to describe a single Course Slot within a Curriculum
 */
class Curriculum_course_slot_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $curriculumCourseSlotID = null;
    private $curriculumID = null;
	private $curriculumIndex = null;
    private $name = null;
    private $minimumGrade = null;
	private $recommendedQuarter = null;
	private $recommendedYear = null;
	private $notes = null;
    private $validCourseIDs = array();
    
	// Constant values defined by the CourseRequisiteTypes table, must reflect content in that table
	const COURSE_REQUISITE_PREREQUISITE = 1;
	const COURSE_REQUISITE_COREQUISITE = 2;
	
	const YEAR_FRESHMAN = "Freshman";
	const YEAR_SOPHOMORE = "Sophomore";
	const YEAR_JUNIOR = "Junior";
	const YEAR_SENIOR = "Senior";
	
    /**
     * Main constructor for Curriculum_course_slot_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
	/**
	 * Summary of toSerializedString
	 * Serialization function for this model
	 */
	public function toSerializedString()
	{
		$arr = array();
		
		if($this->curriculumCourseSlotID != null)
		{
			$arr['curriculumCourseSlotID'] = $this->curriculumCourseSlotID;
		}
		
		if($this->curriculumID != null)
		{
			$arr['curriculumID'] = $this->curriculumID;
		}
		
		if($this->curriculumIndex != null)
		{
			$arr['curriculumIndex'] = $this->curriculumIndex;
		}
		
		if($this->name != null)
		{
			$arr['name'] = $this->name;
		}
		
		if($this->minimumGrade != null)
		{
			$arr['minimumGrade'] = $this->minimumGrade;
		}
		
		if($this->recommendedQuarter != null)
		{
			$arr['recommendedQuarter'] = $this->recommendedQuarter;
		}
		
		if($this->recommendedYear != null)
		{
			$arr['recommendedYear'] = $this->recommendedYear;
		}
		
		if($this->notes != null)
		{
			$arr['notes'] = $this->notes;
		}
		
		$arr['validCourseIDs'] = array();
		
		foreach($this->validCourseIDs as $validcourseID)
		{
			array_push($arr['validCourseIDs'], $validcourseID);
		}
		
		return json_encode($arr);
	}
	
	/**
	 * Summary of fromSerializedString
	 * Deserialization function for this model
	 */
	public function fromSerializedString($serializedString)
	{
		$arr = json_decode($serializedString);
		
		if(isset($arr->curriculumCourseSlotID))
		{
			$this->curriculumCourseSlotID = $arr->curriculumCourseSlotID;
		}
		
		if(isset($arr->curriculumID))
		{
			$this->curriculumID = $arr->curriculumID;
		}
		
		if(isset($arr->curriculumIndex))
		{
			$this->curriculumIndex = $arr->curriculumIndex;
		}
		
		if(isset($arr->name))
		{
			$this->name = $arr->name;
		}
		
		if(isset($arr->minimumGrade))
		{
			$this->minimumGrade = $arr->minimumGrade;
		}
		
		if(isset($arr->recommendedQuarter))
		{
			$this->recommendedQuarter = $arr->recommendedQuarter;
		}
		
		if(isset($arr->recommendedYear))
		{
			$this->recommendedYear = $arr->recommendedYear;
		}
		
		if(isset($arr->notes))
		{
			$this->notes = $arr->notes;
		}
		
		foreach($arr->validCourseIDs as $validcourseID)
		{
			array_push($this->validCourseIDs, $validcourseID);
		}
	}
	
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a curriculum course slot model's data and its associated models from the database into this object using a CurriculumCourseSlotID as a primary key lookup
     * 
     * @param int $curriculumCourseSlotID The primary key (Curriculum Course Slot ID) to lookup course properties in the database with
     * @return boolean True if a curriculum course slot model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($curriculumCourseSlotID)
    {
        if($curriculumCourseSlotID != null && filter_var($curriculumCourseSlotID, FILTER_VALIDATE_INT))
        {
            $results = $this->db->get_where('CurriculumCourseSlots', array('CurriculumCourseSlotID' => $curriculumCourseSlotID), 1);
            
            if($results->num_rows() > 0)
            {
                $row = $results->row_array();
                
                $this->curriculumCourseSlotID = $row['CurriculumCourseSlotID'];
                $this->curriculumID = $row['CurriculumID'];
				$this->curriculumIndex = $row['CurriculumIndex'];
                $this->name = $row['Name'];
                $this->minimumGrade = $row['MinimumGrade'];
                $this->recommendedQuarter = $row['RecommendedQuarter'];
				$this->recommendedYear = $row['RecommendedYear'];
				$this->notes = $row['Notes'];
				
                $this->db->select('CourseID');
                $this->db->from('CurriculumSlotValidCourses');
                $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
                
                $results = $this->db->get();
                
                if($results->num_rows() > 0)
                {
                    foreach($results->result_array() as $row)
                    {
                        $this->addValidCourseID($row['CourseID']);
                    }
                }
                
                return true;
            }
        }
        
        return false;
    }
    
	/**
     * Summary of getNotes
     * Get the notes associated with this model
     * 
     * @return string The notes associated with this curriculum course slot model
     */
	public function getNotes()
	{
		return $this->notes;
	}
	
	/**
     * Summary of getRecommendedQuarter
     * Get the recommended quarter associated with this model
     * 
     * @return string The recommended quarter associated with this curriculum course slot model
     */
	public function getRecommendedQuarter()
	{
		return $this->recommendedQuarter;
	}
	
	/**
     * Summary of getRecommendedYear
     * Get the recommended year associated with this model
     * 
     * @return string The recommended year associated with this curriculum course slot model
     */
	public function getRecommendedYear()
	{
		return $this->recommendedYear;
	}
	
    /**
     * Summary of getCurriculumCourseSlotID
     * Get the curriculum course slot model id (primary key)
     * 
     * @return int The id associated with this curriculum course slot model
     */
    public function getCurriculumCourseSlotID()
    {
        return $this->curriculumCourseSlotID;
    }
    
    /**
     * Summary of getCurriculumID
     * Get the curriculum id for the curriculum this model is associated with
     * 
     * @return int The id of the curriculum that this model is associated with
     */
    public function getCurriculumID()
    {
        return $this->curriculumID;
    }
    
	/**
     * Summary of getCurriculumIndex
     * Get the curriculum index for the curriculum this model is associated with
     * 
     * @return int The index of the curriculum that this model is associated with
     */
	public function getCurriculumIndex()
	{
		return $this->curriculumIndex;
	}
	
    /**
     * Summary of getName
     * Get the name of the curriculum course slot
     * 
     * @return string The name associated with this model
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Summary of getMinimumGrade
     * Get the minimum grade for this curriculum course slot
     * 
     * @return string The minimum grade for this curriculum course slot
     */
    public function getMinimumGrade()
    {
        return $this->minimumGrade;
    }
    
    /**
     * Summary of getValidCourseIDs
     * Get all of the course ids for courses that are valid to fill this curriculum course slot model
     * 
     * @return array An array containing all of the course IDs for courses that are valid to fill this slot
     */
    public function getValidCourseIDs()
    {
        return $this->validCourseIDs;
    }
    
    /**
     * Summary of setName
     * Set the name of this curriculum course slot id
     * 
     * @param string $name The name to be associated with this model
     */
    public function setName($name)
    {
        $this->name = filter_var($name, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
	/**
     * Summary of setCurriculumIndex
     * Set the curriculum index for this curriculum course slot model to be associated with
     * 
     * @param int $curriculum index The curriculum to associate with this model
     */
	public function setCurriculumIndex($curriculumIndex)
	{
		$this->curriculumIndex = filter_var($curriculumIndex, FILTER_SANITIZE_NUMBER_INT);
	}
	
	/**
     * Summary of setNotes
     * Set the notes associated with this model
     * 
     * @param string $notes The notes associated with this curriculum course slot model
     */
	public function setNotes($notes)
	{
		$this->notes = filter_var($notes, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	
	/**
     * Summary of setRecommendedQuarter
     * Set the recommended quarter associated with this model (SEE Academic Quarter Model QUARTER constants)
     * 
     * @return string $recommendedQuarter The recommended quarter associated with this curriculum course slot model
     */
	public function setRecommendedQuarter($recommendedQuarter)
	{
		$this->recommendedQuarter = filter_var($recommendedQuarter, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	
	/**
     * Summary of setRecommendedYear
     * Set the recommended year associated with this model (See YEAR constants)
     * 
     * @return string $recommendedYear The recommended year associated with this curriculum course slot model
     */
	public function setRecommendedYear($recommendedYear)
	{
		$this->recommendedYear = filter_var($recommendedYear, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	
    /**
     * Summary of setCurriculum
     * Set the curriculum for this curriculum course slot model to be associated with
     * 
     * @param Curriculum_model $curriculum The curriculum to associate with this model
     */
    public function setCurriculum($curriculum)
    {
        if($curriculum->getCurriculumID() != null && filter_var($curriculum->getCurriculumID(), FILTER_VALIDATE_INT))
        {
            $this->curriculumID = $curriculum->getCurriculumID();
        }
    }
    
    /**
     * Summary of setMinimumGrade
     * Set the minimum grade for this curriculum course slot model
     * 
     * @param string $minimumGrade The minimum grade to be associated with this curriculum course slot
     */
    public function setMinimumGrade($minimumGrade)
    {
        $this->minimumGrade = filter_var($minimumGrade, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    /**
     * Summary of addValidCourseID
     * Add a valid course id to the set of all valid courses to fill this curriculum course slot model
     * 
     * @param int $courseID An integer id of a valid course for this slot
     */
    public function addValidCourseID($courseID)
    {
        if(!in_array($courseID, $this->validCourseIDs))
        {
            array_push($this->validCourseIDs, $courseID);
        }
    }
    
    /**
     * Summary of removeValidCourseID
     * Remove a course id from the set of valid courses for this curriculum course slot model
     * 
     * @param int $courseID The course id to remove from the set of valid courses for this model
     */
    public function removeValidCourseID($courseID)
    {
        if(in_array($courseID, $this->validCourseIDs))
        {
            unset($this->validCourseIDs[array_search($courseID, $this->validCourseIDs)]);
        }
    }
    
	/**
	 * Summary of getCourseSlotsPrerequisiteTo
	 * Get all of the curriculum course slots that this curriculum course slot is a prerequisite for
	 *
	 * @return Array An array containing all the curriculum course slots that this curriculum course slot is a prerequisite for
	 */
	public function getCourseSlotsPrerequisiteTo()
	{
		$models = array();
		
		if($this->courseID != null)
		{
			$this->db->select('CurriculumCourseSlotID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_PREREQUISITE);
			$this->db->where('RequisiteCurriculumCourseSlotID', $this->courseID);

			
			$results = $this->db->get('CurriculumCourseSlotRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Curriculum_course_slot_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CurriculumCourseSlotID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getPrequisiteCourseSlots
	 * Get all of the prerequisite curriculum course slots for this curriculum course slot
	 *
	 * @return Array An array containing all the curriculum course slots that are prerequisites to this curriculum course slot
	 */
	public function getPrequisiteCourseSlots()
	{
		$models = array();
		
		if($this->curriculumCourseSlotID != null)
		{
			$this->db->select('RequisiteCurriculumCourseSlotID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_PREREQUISITE);
			$this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
			
			$results = $this->db->get('CurriculumCourseSlotRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Curriculum_course_slot_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['RequisiteCurriculumCourseSlotID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getCorequisiteCourseSlots
	 * Get all of the co-requisite curriculum course slots for this course
	 *
	 * @return Array An array containing all the curriculum course slots that are co-requisites to this curriculum course slot
	 */
	public function getCorequisiteCourseSlots()
	{
		$models = array();
		
		if($this->curriculumCourseSlotID != null)
		{
			$this->db->select('RequisiteCurriculumCourseSlotID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_COREQUISITE);
			$this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
			
			$results = $this->db->get('CurriculumCourseSlotRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Curriculum_course_slot_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['RequisiteCurriculumCourseSlotID']))
				{
					array_push($models, $model);
				}
			}
			
			$this->db->select('CurriculumCourseSlotID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_COREQUISITE);
			$this->db->where('RequisiteCurriculumCourseSlotID', $this->courseID);
			
			$results = $this->db->get('CurriculumCourseSlotRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Curriculum_course_slot_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CurriculumCourseSlotID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of addCourseSlotPrerequisite
	 * Add a prerequisite curriculum course slot to this model
	 *
	 * @param Curriculum_course_slot_model $curriculumCourseSlot The curriculum course slot model that is the prerequisite to this model
	 * @return boolean Whether or not the prerequisite relationship was created in the database
	 */
	public function addCourseSlotPrerequisite($curriculumCourseSlot)
	{
		$data = array(
			'CurriculumCourseSlotID' => $this->curriculumCourseSlotID,
			'RequisiteCurriculumCourseSlotID' => intval($curriculumCourseSlot->curriculumCourseSlotID),
			'CourseRequisiteTypeID' => self::COURSE_REQUISITE_PREREQUISITE
		);
		
		$this->db->insert('CurriculumCourseSlotRequisites', $data);
		
		return $this->db->affected_rows() > 0;
	}
	
	/**
	 * Summary of addCourseSlotCorequisite
	 * Add a corequisite curriculum course slot to this model
	 *
	 * @param Curriculum_course_slot_model $curriculumCourseSlot The curriculum course slot model that is the corequisite to this model
	 * @return boolean Whether or not the corequisite relationship was created in the database
	 */
	public function addCourseSlotCorequisite($curriculumCourseSlot)
	{
		$data = array(
			'CurriculumCourseSlotID' => $this->curriculumCourseSlotID,
			'RequisiteCurriculumCourseSlotID' => $curriculumCourseSlot->curriculumCourseSlotID,

			'CourseRequisiteTypeID' => self::COURSE_REQUISITE_COREQUISITE
		);
		
		$this->db->insert('CurriculumCourseSlotRequisites', $data);
		
		return $this->db->affected_rows() > 0;
	}
	
	/**
	 * Summary of removeCourseSlotRequisite
	 * Remove all requisite relationships between this model and another curriculum course slot model
	 *
	 * @param Curriculum_course_slot_model $curriculumCourseSlot The curriculum course slot model to remove relationships with
	 * @return boolean True if any requisite relationships were deleted from the database, false otherwise
	 */
	public function removeCourseSlotRequisite($curriculumCourseSlot)
	{
		$this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);

		$this->db->where('RequisiteCurriculumCourseSlotID', $curriculumCourseSlot->curriculumCourseSlotID);

		$this->db->delete('CurriculumCourseSlotRequisites');
		
		$num = $this->db->affected_rows();
		
		$this->db->where('CurriculumCourseSlotID', $curriculumCourseSlot->curriculumCourseSlotID);

		$this->db->where('RequisiteCurriculumCourseSlotID', $this->curriculumCourseSlotID);

		$this->db->delete('CurriculumCourseSlotRequisites');
		
		return ($num + $this->db->affected_rows()) > 0;
	}
	
    /**
     * Summary of create
     * Save a new curriculum course slot model into the CurriculumCourseSlots table and save all associated models
     * for storing valid courses to fill this slot
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {		
        if($this->curriculumID != null && filter_var($this->curriculumID, FILTER_VALIDATE_INT) && $this->curriculumIndex != null && filter_var($this->curriculumIndex, FILTER_VALIDATE_INT) && $this->name != null && $this->minimumGrade != null && $this->recommendedQuarter != null && $this->recommendedYear != null)
        {
            $data = array(
				'CurriculumID' => $this->curriculumID,
				'CurriculumIndex' => $this->curriculumIndex,
				'Name' => $this->name,
				'MinimumGrade' => $this->minimumGrade,
				'RecommendedQuarter' => $this->recommendedQuarter,
				'RecommendedYear' => $this->recommendedYear,
				'Notes' => $this->notes
			);
            
            $this->db->insert('CurriculumCourseSlots', $data);
			
            if($this->db->affected_rows() > 0)
            {
                $this->curriculumCourseSlotID = $this->db->insert_id();
                
				if(count($this->validCourseIDs) > 0)
				{
					$data_arr = array();
                
					foreach($this->validCourseIDs as $courseID)
					{
						array_push($data_arr, array('CurriculumCourseSlotID' => $this->curriculumCourseSlotID, 'CourseID' => $courseID));
					}
					
					$this->db->insert_batch('CurriculumSlotValidCourses', $data_arr);
				}
				
                return true;
            }
        }
		
        return false;
    }
    
    /**
     * Summary of update
     * Update existing rows in the database associated with this curriculum course slot model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->curriculumCourseSlotID != null && $this->curriculumIndex != null && filter_var($this->curriculumIndex, FILTER_VALIDATE_INT) && $this->minimumGrade != null && $this->recommendedQuarter != null && $this->recommendedYear != null)
        {
            $data = array(
				'CurriculumID' => $this->curriculumID,
				'CurriculumIndex' => $this->curriculumIndex,
				'Name' => $this->name,
				'MinimumGrade' => $this->minimumGrade,
				'RecommendedQuarter' => $this->recommendedQuarter,
				'RecommendedYear' => $this->recommendedYear,
				'Notes' => $this->notes
			);
            
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->update('CurriculumCourseSlots', $data);
            
            $sum = $this->db->affected_rows();
            
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->delete('CurriculumSlotValidCourses');
            
            if(count($this->validCourseIDs) > 0)
			{
				$data_arr = array();
			
				foreach($this->validCourseIDs as $courseID)
				{
					array_push($data_arr, array('CurriculumCourseSlotID' => $this->curriculumCourseSlotID, 'CourseID' => $courseID));
				}
				
				$this->db->insert_batch('CurriculumSlotValidCourses', $data_arr);
			}
            
            return $sum > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this curriculum course slot from the database and all associated models for this curriculum course slot
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->curriculumCourseSlotID != null)
        {
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->delete('CurriculumSlotValidCourses');
            
			$this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
			$this->db->or_where('RequisiteCurriculumCourseSlotID', $this->curriculumCourseSlotID);
			$this->db->delete('CurriculumCourseSlotRequisites');
			
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->delete('CurriculumCourseSlots');
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of toString
     * Create a summerizing text string of this model
     * 
     * @return string A string summerizing all of this model's properties for debugging
     */
    public function toString()
    {
        $str = "Curriculum_course_slot_model: curriculumCourseSlotID=" . $this->curriculumCourseSlotID . "|curriculumID=" . $this->curriculumID . "|validCourseIDs=";
        
        foreach($this->validCourseIDs as $id)
            $str = $str . $id . ",";
        
        return $str;
    }
}
