<?php

/**
 * Summary of Student_transfer_course_model
 * 
 * A model used to describe a single transfer course in the CSC Web App project
 */
class Student_transfer_course_model extends CI_Model
{
	// Member variables, use getter / setter functions for access
	private $studentTransferCourseID = null;
	private $studentUserID = null;
	private $courseName = null;
	private $universityID = null;
	private $hours = null;
	
	/**
     * Main Constructor 
     */
    function __construct()
    {
        parent::__construct();
    }
	
	/**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a student transfer course model's data from the database into this object using a StudentTransferCourseID as a primary key lookup
     * 
     * @param int $studentTransferCourseID The primary key to lookup model properties in the database with
     * @return boolean True if a model's properties were successfully loaded from database, false otherwise
     */
	function loadPropertiesFromPrimaryKey($studentTransferCourseID)
	{
		if($studentTransferCourseID != null && filter_var($studentTransferCourseID, FILTER_VALIDATE_INT))
		{
			$result = $this->db-<get_where('StudentTransferCourses', array('StudentTransferCourseID' => $studentTransferCourseID), 1);
			
			if($result->num_rows() > 0)
			{
				$row = $result->row_array();
				
				$this->studentTransferCourseID = $row['StudentTransferCourseID'];
				$this->studentUserID = $row['StudentUserID'];
				$this->courseName = $row['CourseName'];
				$this->universityID = $row['UniversityID'];
				$this->hours = $row['Hours'];
				
				return true;
			}
		}
		
		return false;
	}
	
	public function setStudentUser($studentUser)
	{
		$this->studentUserID = $studentUser->getUserID();
	}
	
	public function setCourseName($courseName)
	{
		$this->courseName = filter_var($courseName, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	
	public function setUniversityID($universityID)
	{
		$this->universityID = filter_var($universityID, FILTER_SANITIZE_NUMBER_INT);
	}
	
	public function setHours($hours)
	{
		$this->hours = filter_var($hours, FILTER_SANITIZE_NUMBER_INT);
	}
	
	public function getStudentTransferCourseID()
	{
		return $this->studentTransferCourseID;
	}
	
	public function getCourseName()
	{
		return $this->courseName;
	}
	
	public function getUniversityID()
	{
		return $this->universityID;
	}
	
	public function getHours()
	{
		return $this->hours;
	}
	
	public function addEquivilentCourse($course)
	{
		if($this->studentTransferCourseID != null)
		{
			$data = array(
				'StudentTransferCourseID' => $this->studentTransferCourseID,
				'EquivilentCourseID' => $course->getCourseID()
			);
			
			$this->db->insert('StudentTransferCourseEquivilentCourses', $data);
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	public function removeEquivilentCourse($course)
	{
		if($this->studentTransferCourseID != null)
		{
			$this->db->where('StudentTransferCourseID', $this->studentTransferCourseID);
			$this->db->where('EquivilentCourseID', $course->getCourseID());
			
			$this->db->delete('StudentTransferCourseEquivilentCourses');
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	public function getAllEquivilentCourses()
	{
		$models = array();
		
		if($this->studentTransferCourseID != null)
		{
			$this->db->select('EquivilentCourseID');
			$this->db->from('StudentTransferCourseEquivilentCourses');
			$this->db->where('StudentTransferCourseID', $this->studentTransferCourseID);
			
			$results = $this->db->get();
			
			if($results->num_rows() > 0)
			{
				foreach($results->result_array() as $row)
				{
					$model = new Course_model;
					
					if($model->loadPropertiesFromPrimaryKey($row['EquivilentCourseID']))
					{
						array_push($models, $model);
					}
				}
			}
		}
		
		return $models;
	}
	
	public function create()
	{
		if($this->courseName != null && $this->universityID != null && $this->studentUserID != null && $this->hours != null)
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'CourseName' => $this->courseName,
				'UniversityID' => $this->universityID,
				'Hours' => $this->hours
			);
			
			$this->db->insert('StudentTransferCourses', $data);
			
			if($this->db->affected_rows() > 0)
			{
				$this->studentTransferCourseID = $this->db->insert_id();
				
				return true;
			}
		}
		
		return false;
	}
	
	public function update()
	{
		if($this->studentTransferCourseID != null && $this->courseName != null && $this->universityID != null && $this->studentUserID != null && $this->hours != null)
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'CourseName' => $this->courseName,
				'UniversityID' => $this->universityID,
				'Hours' => $this->hours
			);
			
			$this->db->where('StudentTransferCourseID', $this->studentTransferCourseID);
			$this->db->update('StudentTransferCourses', $data);
			
			if($this->db->affected_rows() > 0)
			{
				$this->studentTransferCourseID = $this->db->insert_id();
				
				return true;
			}
		}
		
		return false;
	}
	
	public function delete()
	{
		if($this->studentTransferCourseID != null)
		{
			$this->db->where('StudentTransferCourseID', $this->studentTransferCourseID);
			$this->db->delete('StudentTransferCourseEquivilentCourses');
			
			$this->db->where('StudentTransferCourseID', $this->studentTransferCourseID);
			$this->db->delete('StudentTransferCourses');
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
}