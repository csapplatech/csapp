<?php

/**
 * course_model short summary.
 *
 * A model used to describe a single Course offered at the university
 */
class Course_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $courseID = null;
    private $courseName = null;
    private $courseNumber = null;
	private $courseTitle = null;
    private $courseDescription = null;
	private $courseCategoryName = null;
	private $courseTypeID = null;
    
	// Constant values defined by the CourseRequisiteTypes table, must reflect content in that table
	const COURSE_REQUISITE_PREREQUISITE = 1;
	const COURSE_REQUISITE_COREQUISITE = 2;
	const COURSE_REQUISITE_JUNIOR_STANDING = 3;
	const COURSE_REQUISITE_SENIOR_STANDING = 4;
	
	// Constant values defined by the CourseTypes table, must reflect content in that table
	const COURSE_TYPE_UNDERGRADUATE = 1;
	const COURSE_TYPE_GRADUATE = 2;
	
    /**
     * Main constructor for Course_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a course model's data from the database into this object using a CourseID as a primary key lookup
     * 
     * @param int $courseID The primary key (CourseID) to lookup course properties in the database with
     * @return boolean True if a course model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($courseID)
    {
        if($courseID != null && filter_var($courseID, FILTER_VALIDATE_INT))
        {
            $results = $this->db->get_where('Courses', array('CourseID' => $courseID), 1);
            
            if($results->num_rows() > 0)
            {
                $row = $results->row_array();
                
                $this->courseID = $row['CourseID'];
                $this->courseName = $row['CourseName'];
				$this->courseCategoryName = self::getCategoryName($this->courseName);
                $this->courseNumber = $row['CourseNumber'];
				$this->courseTitle = $row['CourseTitle'];
                $this->courseDescription = $row['CourseDescription'];
				$this->courseTypeID = $row['CourseTypeID'];
                
                return true;
            }
            
        }
        
        return false;
    }
	
    /**
     * Summary of getCourseID
     * Get the course id primary key of this model
     * 
     * @return int The course id associated with this course model
     */
    public function getCourseID()
    {
        return $this->courseID;
    }
    
    /**
     * Summary of getCourseName
     * Get the course name of this model
     * 
     * @return string The course name associated with this course model
     */
    public function getCourseName()
    {
        return $this->courseName;
    }
    
    /**
     * Summary of getCourseNumber
     * Get the course number of this model
     * 
     * @return int The course number associated with this course model
     */
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }
    
	/**
     * Summary of getCourseTitle
     * Get the course title of this model
     * 
     * @return string The course title associated with this course model
     */
	public function getCourseTitle()
	{
		return $this->courseTitle;
	}
	
	/**
     * Summary of getCourseType
     * Get the course type of this model
     * 
     * @return integer The course type associated with this course model
     */
	public function getCourseType()
	{
		return $this->courseTypeID;
	}
	
    /**
     * Summary of getCourseDescription
     * Get the course description of this model
     * 
     * @return string The course description of this course model or null if no description is set
     */
    public function getCourseDescription()
    {
        return $this->courseDescription;
    }
    
	/**
     * Summary of getCourseCategoryName
     * Get the course category name of this model
     * 
     * @return string The course category name of this course model or null if no category is set
     */
	public function getCourseCategoryName()
	{
		return $this->courseCategoryName;
	}
	
	/**
	 * Summary of isUndergraduateCourse
	 * Check whether or not this course model type id is that of an undergraduate course (see COURSE_TYPE constants)
	 *
	 * @return boolean True if the course type for this model is COURSE_TYPE_UNDERGRADUATE, false otherwise
	 */
	public function isUndergraduateCourse()
	{
		return $this->courseTypeID == self::COURSE_TYPE_UNDERGRADUATE;
	}
	
	/**
	 * Summary of isGraduateCourse
	 * Check whether or not this course model type id is that of a graduate course (see COURSE_TYPE constants)
	 *
	 * @return boolean True if the course type for this model is COURSE_TYPE_GRADUATE, false otherwise
	 */
	public function isGraduateCourse()
	{
		return $this->courseTypeID == self::COURSE_TYPE_GRADUATE;
	}
	
    /**
     * Summary of setCourseName
     * Set the course name for this model
     * 
     * @param string $courseName The course name to be associated with this model
     */
    public function setCourseName($courseName)
    {
        $this->courseName = filter_var($courseName, FILTER_SANITIZE_MAGIC_QUOTES);
		
		$this->courseCategoryName = self::getCategoryName($this->courseName);
    }
	
    /**
     * Summary of setCourseNumber
     * Set the course number for this model
     * 
     * @param int $courseNumber The course number to be associated with this model
     */
    public function setCourseNumber($courseNumber)
    {
        $this->courseNumber = filter_var($courseNumber, FILTER_SANITIZE_NUMBER_INT);
    }
    
	/**
     * Summary of setCourseTitle
     * Set the course title for this model
     * 
     * @param string $courseTitle The course title to be associated with this model
     */
	public function setCourseTitle($courseTitle)
	{
		$this->courseTitle = filter_var($courseTitle, FILTER_SANITIZE_MAGIC_QUOTES);
	}
	
    /**
     * Summary of setCourseDescription
     * Set the course description for this model
     * 
     * @param string $courseDescription The course description to be associated with this model or null to set no description
     */
    public function setCourseDescription($courseDescription)
    {
        $this->courseDescription = filter_var($courseDescription, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
	/**
	 * Summary of setCourseType
	 * Set the type of course that this course model is
	 *
	 * @param integer $courseTypeID The type of course this course model is (see COURSE_TYPE constants)
	 */
	public function setCourseType($courseTypeID)
	{
		$this->courseTypeID = filter_var($courseTypeID, FILTER_SANITIZE_NUMBER_INT);
	}
	
	/**
	 * Summary of getCoursesPrerequisiteTo
	 * Get all of the courses that this course is a prerequisite for
	 *
	 * @return Array An array containing all the courses that this course is a prerequisite for
	 */
	public function getCoursesPrerequisiteTo()
	{
		$models = array();
		
		if($this->courseID != null)
		{
			$this->db->select('CourseID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_PREREQUISITE);
			$this->db->where('RequisiteCourseID', $this->courseID);
			
			$results = $this->db->get('CourseRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CourseID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getPrerequisiteCourses
	 * Get all of the prerequisite courses for this course
	 *
	 * @return Array An array containing all the courses that are prerequisites to this course
	 */
	public function getPrerequisiteCourses()
	{
		$models = array();
		
		if($this->courseID != null)
		{
			$this->db->select('RequisiteCourseID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_PREREQUISITE);
			$this->db->where('CourseID', $this->courseID);
			
			$results = $this->db->get('CourseRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['RequisiteCourseID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getCorequisiteCourses
	 * Get all of the co-requisite courses for this course
	 *
	 * @return Array An array containing all the courses that are co-requisites to this course
	 */
	public function getCorequisiteCourses()
	{
		$models = array();
		
		if($this->courseID != null)
		{
			$this->db->select('RequisiteCourseID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_COREQUISITE);
			$this->db->where('CourseID', $this->courseID);
			
			$results = $this->db->get('CourseRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['RequisiteCourseID']))
				{
					array_push($models, $model);
				}
			}
			
			$this->db->select('CourseID');
			$this->db->where('CourseRequisiteTypeID', self::COURSE_REQUISITE_COREQUISITE);
			$this->db->where('RequisiteCourseID', $this->courseID);
			
			$results = $this->db->get('CourseRequisites');
			
			foreach($results->result_array() as $row)
			{
				$model = new Course_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CourseID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getNonCourseRequisites
	 * Get all of the non course requisites for this course
	 *
	 * @return Array An array containing all the non course requisites for this course
	 */
	public function getNonCourseRequisites()
	{
		$rows = array();
		
		if($this->courseID != null)
		{
			$this->db->select('CourseRequisiteTypeID');
			$this->db->from('CourseRequisites');
			$this->db->where('CourseID', $this->courseID);
			$this->db->where('CourseRequisiteTypeID >', self::COURSE_REQUISITE_COREQUISITE);
			
			$results = $this->db->get();
			
			foreach($results->result_array() as $row)
			{
				array_push($rows, $row['CourseRequisiteTypeID']);
			}
		}
		
		return $rows;
	}
	
	/**
	 * Summary of addCoursePrerequisite
	 * Add a prerequisite course to this model
	 *
	 * @param Course_model $course The course model that is the prerequisite to this model
	 * @return boolean Whether or not the prerequisite relationship was created in the database
	 */
	public function addCoursePrerequisite($course)
	{
		$data = array(
			'CourseID' => $this->courseID,
			'RequisiteCourseID' => $course->courseID,
			'CourseRequisiteTypeID' => self::COURSE_REQUISITE_PREREQUISITE
		);
		
		$this->db->insert('CourseRequisites', $data);
		
		return $this->db->affected_rows() > 0;
	}
	
	/**
	 * Summary of addCourseCorequisite
	 * Add a corequisite course to this model
	 *
	 * @param Course_model $course The course model that is the corequisite to this model
	 * @return boolean Whether or not the corequisite relationship was created in the database
	 */
	public function addCourseCorequisite($course)
	{
		$data = array(
			'CourseID' => $this->courseID,
			'RequisiteCourseID' => $course->courseID,
			'CourseRequisiteTypeID' => self::COURSE_REQUISITE_COREQUISITE
		);
		
		$this->db->insert('CourseRequisites', $data);
		
		return $this->db->affected_rows() > 0;
	}
	
	/**
	 * Summary of removeCourseRequisite
	 * Remove all requisite relationships between this model and another course model
	 *
	 * @param Course_model $course The course model to remove relationships with
	 * @return boolean True if any requisite relationships were deleted from the database, false otherwise
	 */
	public function removeCourseRequisite($course)
	{
		$this->db->where('CourseID', $this->courseID);
		$this->db->where('RequisiteCourseID', $course->courseID);
		$this->db->delete('CourseRequisites');
		
		$num = $this->db->affected_rows();
		
		$this->db->where('CourseID', $course->courseID);
		$this->db->where('RequisiteCourseID', $this->courseID);
		$this->db->delete('CourseRequisites');
		
		return ($num + $this->db->affected_rows()) > 0;
	}
	
	/**
	 * Summary of addNonCourseRequisite
	 * Add a non course based curriculum course slot to this model
	 *
	 * @param int $courseRequisiteType The type of requisite to bind to the course
	 * @return boolean Whether or not the requisite was created in the database
	 */
	public function addNonCourseRequisite($courseRequisiteType)
	{
		if($courseRequisiteType == self::COURSE_REQUISITE_JUNIOR_STANDING || $courseRequisiteType == self::COURSE_REQUISITE_SENIOR_STANDING)
		{
			$data = array(
				'CourseID' => $this->courseID,
				'RequisiteCourseID' => $this->courseID,
				'CourseRequisiteTypeID' => $courseRequisiteType
			);
			
			$this->db->insert('CourseRequisites', $data);
			
			return $this->db->affected_rows() > 0;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Summary of removeNonCourseRequisite
	 * Remove a non course based curriculum course slot to this model
	 *
	 * @param int $courseRequisiteType The type of requisite to remove from the course
	 * @return boolean Whether or not the requisite was created in the database
	 */
	public function removeNonCourseRequisite($courseRequisiteType)
	{
		if($courseRequisiteType == self::COURSE_REQUISITE_JUNIOR_STANDING || $courseRequisiteType == self::COURSE_REQUISITE_SENIOR_STANDING)
		{
			$data = array(
				'CourseID' => $this->courseID,
				'RequisiteCourseID' => $this->courseID,
				'CourseRequisiteTypeID' => $courseRequisiteType
			);
			$this->db->where('CourseID', $this->CourseID);
			$this->db->where('CourseRequisiteTypeID', $courseRequisiteType);
			$this->db->delete('CourseRequisites');
			
			return $this->db->affected_rows() > 0;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Summary of getAllCurriculumCourseSlots
	 * Get all of the curriculum course slots that this course is compatible with
	 *
	 * @return Array An array containing curriculum course slot models that are compatible with this course
	 */
	public function getAllCurriculumCourseSlots()
	{
		$models = array();
		
		if($this->courseID != null)
		{
			$this->db->where("CourseID", $this->courseID);
			$this->db->select("CurriculumCourseSlotID");
			
			$results = $this->db->get("CurriculumSlotValidCourses");
			
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
     * Summary of update
     * Update existing rows in the database associated with this course model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->courseID != null && filter_var($this->courseID, FILTER_VALIDATE_INT) && $this->courseName != null && $this->courseNumber != null && filter_var($this->courseNumber, FILTER_VALIDATE_INT) && $this->courseTypeID != null && filter_var($this->courseTypeID, FILTER_VALIDATE_INT))
        {
            $data = array(
				'CourseTypeID' => $this->courseTypeID,
				'CourseName' => $this->courseName, 
				'CourseNumber' => $this->courseNumber, 
				'CourseTitle' => $this->courseTitle,
				'CourseDescription' => $this->courseDescription
			);
            
            $this->db->where('CourseID', $this->courseID);
            $this->db->update('Courses', $data);
            
            if($this->db->affected_rows() > 0)
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Summary of create
     * Save a new course model into the Courses table in the database
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {
        if($this->courseName != null && $this->courseNumber != null && filter_var($this->courseNumber, FILTER_VALIDATE_INT) && $this->courseTypeID != null && filter_var($this->courseTypeID, FILTER_VALIDATE_INT))
        {
            $data = array(
				'CourseTypeID' => $this->courseTypeID,
				'CourseName' => $this->courseName, 
				'CourseNumber' => $this->courseNumber, 
				'CourseTitle' => $this->courseTitle,
				'CourseDescription' => $this->courseDescription
			);
            
            $this->db->insert('Courses', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->courseID = $this->db->insert_id();
                
                return true;
            }
        }
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this course from the database and all associated models for this course
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->courseID != null)
        {
			$this->db->select('CourseSectionID');
			$this->db->from('CourseSections');
			$this->db->where('CourseID', $this->courseID);
			
			$results = $this->db->get();
			
			$ids = array();
			
			foreach($results->result_array() as $row)
			{
				array_push($ids, $row['CourseSectionID']);
			}
			
			if(count($ids) > 0)
			{
				$this->db->where_in('CourseSectionID', $ids);
				$this->db->delete('CourseSectionTimes');
				
				$this->db->where_in('CourseSectionID', $ids);
				$this->db->delete('StudentCourseSections');
				
				$this->db->where('CourseID', $this->courseID);
				$this->db->delete('CourseSections');
			}
			
			$this->db->where('CourseID', $this->courseID);
			$this->db->delete('CurriculumSlotValidCourses');
			
			$this->db->where('EquivilentCourseID', $this->courseID);
			$this->db->delete('StudentTransferCourseEquivilentCourses');
            
            $this->db->where('CourseID', $this->courseID);
            $this->db->or_where('RequisiteCourseID', $this->courseID);
            $this->db->delete('CourseRequisites');
            
            $this->db->where('CourseID', $this->courseID);
            $this->db->delete('Courses');
            
            return $this->db->affected_rows() > 0;
        }
        else
        {
            return false; 
        }
    }
    
	/**
	 * Summary of getCategoryName
	 * Get the category name of the course
	 * 
	 * @param string $courseName The name of the course to find a category for
	 * @return string The category name for the course or the course name if no category is found
	 */
	public static function getCategoryName($courseName)
	{
		$db = get_instance()->db;
		
		$db->select('CategoryName');
		$db->where('CourseName', $courseName);
		$results = $db->get('CourseCategories');
		
		if($results->num_rows() > 0)
		{
			$row = $results->row_array();
			
			return $row['CategoryName'];
		}
		else
		{
			return $courseName;
		}
	}
	
	/**
     * Summary of getAllCourseCategories
     * Get all of the course categories saved in the database
     * 
     * @return An array of all the course categories represented in the database
     */
	public static function getAllCourseCategories()
	{
		$db = get_instance()->db;
		
		$db->order_by('CategoryName', 'asc');
		
		$results = $db->get('CourseCategories');
		
		if($results->num_rows() > 0)
		{
			return $results->result_array();
		}
		
		return array();
	}
	
	/**
     * Summary of getAllCourseTypes
     * Get all of the course types saved in the database
     * 
     * @return An array of all the course types represented in the database
     */
	public static function getAllCourseTypes()
	{
		$db = get_instance()->db;
		
		$results = $db->get('CourseTypes');
		
		if($results->num_rows() > 0)
		{
			return $results->result_array();
		}
		
		return array();
	}
	
    /**
     * Summary of getAllCourses
     * Get all of the courses saved in the database
     * 
	 * @param string $courseName A filter to only select courses with the specified course name, leave null for no filter
     * @return An array of all the course models represented in the database
     */
    public static function getAllCourses($courseName = null)
    {
        $db = get_instance()->db;
        
		$db->select('CourseID');
		
		if($courseName != null)
		{
			$db->where('CourseName', $courseName);
		}
		
		$db->order_by('CourseName, CourseNumber', 'asc');
		
        $results = $db->get('Courses');
        
        $data_arr = array();
        
        foreach($results->result_array() as $row)
        {
            $course = new Course_model;
            
            if($course->loadPropertiesFromPrimaryKey($row['CourseID']))
				array_push($data_arr, $course);
        }
        
        return $data_arr;
    }
}
