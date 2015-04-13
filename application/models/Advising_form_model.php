<?php

/**
 * Advising_form_model short summary.
 *
 * A model used to describe an Advising form saved in the database
 */
class Advising_form_model extends CI_Model
{
	// Member variables, use getter / setter functions for access
	private $advisingFormID = null;
	private $studentUserID = null;
	private $academicQuarterID = null;
	
	// Constants to represent the various course section state types as reflected in the CSC Web App database
    // If the table `AdvisingFormCourseSectionStates` or any of its rows are ever modified, reflect those changes in these constants
	const COURSE_SECTION_STATE_PREFERRED = 1;
	const COURSE_SECTION_STATE_ALTERNATE = 2;
	
	/**
     * Main constructor for Advising_form_model
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	/**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a course section model's data from the database into this object using a CourseSectionID as a primary key lookup
     * 
     * @param int $advisingFormID The primary key (AdvisingFormID) to lookup advising form properties in the database with
     * @return boolean True if an advising form model's properties were successfully loaded from database, false otherwise
     */
	public function loadPropertiesFromPrimaryKey($advisingFormID)
	{
		if($advisingFormID != null && filter_var($advisingFormID, FILTER_VALIDATE_INT))
		{
			$results = $this->db->get_where('AdvisingForms', array('AdvisingFormID' => $advisingFormID), 1);
			
			if($results->num_rows() > 0)
			{
				$row = $results->row_array();
				
				$this->advisingFormID = row['AdvisingFormID'];
				$this->studentUserID = row['StudentUserID'];
				$this->academicQuarterID = row['AcademicQuarterID'];
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
     * Summary of getAdvisingFormID
     * 
     * @return integer The primary key (AdvisingFormID) for this model
     */
	public function getAdvisingFormID()
	{
		return $this->advisingFormID;
	}
	
	/**
     * Summary of getStudentUserID
     * 
     * @return integer The student user id for this model
     */
	public function getStudentUserID()
	{
		return $this->studentUserID;
	}

	/**
     * Summary of getAcademicQuarterID
     * 
     * @return integer The academic quarter id for this model
     */
	public function getAcademicQuarterID()
	{
		return $this->academicQuarterID;
	}
	
	/**
     * Summary of setStudentUserID
     * Set the student user id for this advising form
     * 
     * @param integer $studentUserID The student user id to associate with this model
     */
	public function setStudentUserID($studentUserID)
	{
		$this->studentUserID = filter_var($studentUserID, FILTER_SANITIZE_NUMBER_INT);
	}
	
	/**
     * Summary of setAcademicQuarterID
     * Set the academic quarter id for this advising form
     * 
     * @param integer $academicQuarterID The academic quarter id to associate with this model 
     */
	public function setAcademicQuarterID($academicQuarterID)
	{
		$this->academicQuarterID = filter_var($academicQuarterID, FILTER_SANITIZE_NUMBER_INT);
	}
	
	/**
     * Summary of getPrefferedCourseSections
     * Get all the preferred course sections for this advising form
     * 
     * @return Array An array containing all course sections that are preferred in this advising form
     */
	public function getPrefferedCourseSections()
	{
		$models = array();
		
		if($this->advisingFormID != null)
		{
			$this->db->select('CourseSectionID');
			$this->db->from('AdvisingFormCourseSections');
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->where('AdvisingFormCourseSectionState', self::COURSE_SECTION_STATE_PREFERRED);
			
			$results = $this->db->get();
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_section_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CourseSectionID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
     * Summary of getAlternateCourseSections
     * Get all the alternate course sections for this advising form
     * 
     * @return Array An array containing all course sections that are alternate in this advising form
     */
	public function getAlternateCourseSections()
	{
		$models = array();
		
		if($this->advisingFormID != null)
		{
			$this->db->select('CourseSectionID');
			$this->db->from('AdvisingFormCourseSections');
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->where('AdvisingFormCourseSectionState', self::COURSE_SECTION_STATE_ALTERNATE);
			
			$results = $this->db->get();
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_section_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CourseSectionID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
     * Summary of addCourseSection
     * Add a course section to this advising form and set its state
     * 
     * @param Course_section_model $courseSection The course section to add to this advising form
	 * @param integer $courseSectionState The state of the course section added (see COURSE_SECTION_STATE constants)
	 * @return boolean True if the course section was successfully added, false otherwise
     */
	public function addCourseSection($courseSection, $courseSectionState)
	{
		if($this->advisingFormID != null && $courseSection->getCourseSectionID() != null)
		{
			$data = array(
				'AdvisingFormID' => $this->advisingFormID,
				'CourseSectionID' => $courseSection->getCourseSectionID(),
				'AdvisingFormCourseSectionStateID' => $courseSectionState
			);
			
			$this->db->insert('AdvisingFormCourseSections', $data);
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	/**
     * Summary of removeCourseSection
     * Remove a course section from this advising form
     * 
     * @param Course_section_model $courseSection The course section to remove from this advising form
     */
	public function removeCourseSection($courseSection)
	{
		if($this->advisingFormID != null && $courseSection->getCourseSectionID() != null)
		{
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->where('CourseSectionID', $courseSection->getCourseSectionID());
			$this->db->delete('AdvisingFormCourseSections');
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	/**
     * Summary of create
     * Save a new advising form model into the AdvisingForms table in the database
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
	public function create()
	{
		if($this->studentUserID != null && filter_var($this->studentUserID, FILTER_VALIDATE_INT) && $this->academicQuarterID != null && filter_var($this->academicQuarterID, FILTER_VALIDATE_INT))
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'AcademicQuarterID' => $this->academicQuarterID
			);
			
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->update('AdvisingForms', $data);
			
			if($this->db->affected_rows() > 0)
			{
				$this->advisingFormID = $this->db->insert_id();
				
				return true;
			}
		}
		
		return false;
	}
	
	/**
     * Summary of update
     * Update existing rows in the database associated with this advising form model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
	public function update()
	{
		if($this->advisingFormID != null && $this->studentUserID != null && filter_var($this->studentUserID, FILTER_VALIDATE_INT) && $this->academicQuarterID != null && filter_var($this->academicQuarterID, FILTER_VALIDATE_INT))
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'AcademicQuarterID' => $this->academicQuarterID
			);
			
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->update('AdvisingForms', $data);
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	/**
     * Summary of delete
     * Delete this advising form from the database and all associated models for this course
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
	public function delete()
	{
		if($this->advisingFormID != null)
		{
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->delete('AdvisingFormCourseSections');
			
			$this->db->where('AdvisingFormID', $this->advisingFormID);
			$this->db->delete('AdvisingForms');
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	/**
     * Summary of getAllAdvisingFormsByStudentID
     * Loads a course section model's data from the database into this object using a CourseSectionID as a primary key lookup
     * 
     * @param int $studentUserID The student user id to lookup advising form models in the database with
     * @return Array An array containing all advising form models in the database associated with that student user
     */
	public static function getAllAdvisingFormsByStudentID($studentUserID)
	{
		$db = get_instance()->db;
		
		$models = array();
		
		if($studentUserID != null && filter_var($studentUserID, FILTER_VALIDATE_INT))
		{
			$db->select('AdvisingFormID');
			$db->from('AdvisingForms');
			$db->where('StudentUserID', $studentUserID);
			$results = $db->get();
			
			foreach($results->result_array() as $row)
			{
				$model = new Advising_form_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['AdvisingFormID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
}

?>