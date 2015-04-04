<?php

/**
 * Course_section_model short summary.
 *
 * A model used to describe a single course section (A instance of a course being offered during an academic quarter)
 */
class Course_section_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $courseSectionID = null;
    private $courseSectionName = null;
    private $course = null;
	private $hours = null;
	private $callNumber = null;
    private $academicQuarter = null;
    private $courseSectionTimes = array();
    
    /**
     * Main constructor for Course_section_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of toString
     * Get a string representation of this course section model
     * 
     * @return string A string representation of this course section model
     */
    public function toString()
    {
        return $this->course->getCourseName() . $this->course->getCourseNumber() . $this->courseSectionName . $this->hours . $this->callNumber . $this->academicQuarter->getName() . $this->academicQuarter->getYear();
    }
    
	/**
	 * Summary of getCourseSectionTimesAsString
	 * Get the course sections times for this course section as a string similar to how they are represented on BOSS
	 *
	 * @return string A string representing all the course section times like on BOSS
	 */
	public function getCourseSectionTimesAsString()
	{
		$outputString = "";
		
		$temp = array();
		
		foreach($this->courseSectionTimes as $courseSectionTime)
		{
			$startTime = militaryToStandardTime($courseSectionTime->getStartTime());
			$endTime = militaryToStandardTime($courseSectionTime->getEndTime());
			
			$index = $startTime . " - " . $endTime;
			
			if(!isset($temp[$index]))
			{
				$temp[$index] = array();
			}
			
			$temp[$index][$courseSectionTime->getDayOfWeek()] = $courseSectionTime->getDayOfWeekLetter();
		}
		
		foreach($temp as $t)
		{
			$tStr = "";
			
			if(isset($t[Course_section_time_model::DAY_MONDAY]))
			{
				$tStr = $tStr . $t[Course_section_time_model::DAY_MONDAY];
			}
			
			if(isset($t[Course_section_time_model::DAY_TUESDAY]))
			{
				$tStr = $tStr . $t[Course_section_time_model::DAY_TUESDAY];
			}
			
			if(isset($t[Course_section_time_model::DAY_WEDNESDAY]))
			{
				$tStr = $tStr . $t[Course_section_time_model::DAY_WEDNESDAY];
			}
			
			if(isset($t[Course_section_time_model::DAY_THURSDAY]))
			{
				$tStr = $tStr . $t[Course_section_time_model::DAY_THURSDAY];
			}
			
			if(isset($t[Course_section_time_model::DAY_FRIDAY]));
			{
				$tStr = $tStr . $t[Course_section_time_model::DAY_FRIDAY];
			}
			$outputString = $outputString . $tStr . " " . key($temp) . ";";
		}
		
		return $outputString;
	}
	
	/**
	 * Summary of militaryToStandardTime
	 * Convert the database stored military time to a standard time notation
	 *
	 *	@param integer $time The time expressed in military time
	 *	@return string The time converted to standard time notation
	 */
	private static function militaryToStandardTime($time)
	{
		$hour = intval($time / 100);
		
		if($hour > 12)
		{
			$hour = $hour - 12;
		}
		
		$minute = intval($time % 100);
		
		return $hour . ":" . $minute;
	}
	
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a course section model's data from the database into this object using a CourseSectionID as a primary key lookup
     * 
     * @param int $courseSectionID The primary key (CourseSectionID) to lookup course section properties in the database with
     * @return boolean True if a course section model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($courseSectionID)
    {
        if($courseSectionID != null && filter_var($courseSectionID, FILTER_VALIDATE_INT))
        {
            $results = $this->db->get_where('CourseSections', array('CourseSectionID' => $courseSectionID), 1);
            
            $row = $results->row_array();
            
            $this->courseSectionID = $row['CourseSectionID'];
            $this->sectionName = $row['SectionName'];
			//  $this->callNumber = $row['CallNumber'];
			//$this->hours = $row['Hours'];
			
            $this->course = new Course_model;
            
            if($this->course->loadPropertiesFromPrimaryKey($row['CourseID']))
            {
                $this->academicQuarter = new Academic_quarter_model;
                
                if($this->academicQuarter->loadPropertiesFromPrimaryKey($row['AcademicQuarterID']))
                {
                    $courseSectionTimes = Course_section_time_model::getAllCourseSectionTimes($this->courseSectionID);
                    
                    foreach($courseSectionTimes as $courseSectionTime)
                    {
                        $searchstr = $courseSectionTime->getDayOfWeek() . $courseSectionTime->getStartTime() . $courseSectionTime->getEndTime();
                        
                        $this->courseSectionTimes[$searchstr] = $courseSectionTime;
                    }
                    
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Summary of getCourseSectionID
     * Get the course section id primary key of this model
     * 
     * @return int The course section id associated with this course model
     */
    public function getCourseSectionID()
    {
        return $this->courseSectionID;
    }
    
    /**
     * Summary of getCourse
     * Get the course that this model is a course section of
     * 
     * @return Course_model The course model associated with this model
     */
    public function getCourse()
    {
        return $this->course;
    }
    
    /**
     * Summary of getSectionName
     * 
     * @return string The section name of this course section model
     */
    public function getSectionName()
    {
        return $this->sectionName;
    }
    
	/**
     * Summary of getHours
     * 
     * @return integer The course credit hours of this course section model
     */
	public function getHours()
	{
		return $this->hours;
	}
	
	/**
     * Summary of getCallNumber
     * 
     * @return integer The call number of this course section model
     */
	public function getCallNumber()
	{
		return $this->callNumber;
	}
	
    /**
     * Summary of getAcademicQuarter
     * Get the academic quarter this course section model exists in
     * 
     * @return Academic_quarter_model The academic quarter model associated with this model
     */
    public function getAcademicQuarter()
    {
        return $this->academicQuarter;
    }
    
    /**
     * Summary of getCourseSectionTimes
     * Get the course section times for this course section model
     * 
     * @return Array An array containing course section time models associated with this model
     */
    public function getCourseSectionTimes()
    {
        return $this->courseSectionTimes;
    }
    
    /**
     * Summary of setSectionName
     * Set the course section name for this course section
     * 
     * @param string $sectionName The section name to be associated with this course model
     */
    public function setSectionName($sectionName)
    {
        $this->sectionName = filter_var($sectionName, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
	/**
     * Summary of setCallNumber
     * Set the call number for this course section
     * 
     * @param integer $callNumber The call number to be associated with this course model
     */
	public function setCallNumber($callNumber)
	{
		$this->callNumber = filter_var($callNumber, FILTER_SANITIZE_NUMBER_INT);
	}
	
	/**
     * Summary of setHours
     * Set the course credit hours for this course section
     * 
     * @param integer $hours The credit hours for this course model
     */
	public function setHours($hours)
	{
		$this->hours = filter_var($hours, FILTER_SANITIZE_NUMBER_INT);
	}
	
    /**
     * Summary of setCourseFromID
     * Look up a course model by its course id and associate it with this course section model
     * 
     * @param int $courseID The course id to find the associated course model by
     * @return boolean True if a course model was successfully associated with this model, false otherwise
     */
    public function setCourseFromID($courseID)
    {
        $this->course = new Course_model;
        
        if($this->course->loadPropertiesFromPrimaryKey($courseID))
        {
            return true;
        }
        
        $this->course = null;
        return false;
    }
    
    /**
     * Summary of setAcademicQuarterFromID
     * Look up an academic quarter modlel by its id and associate it with this course section model
     * 
     * @param int $academicQuarterID The academic quarter id to find the associated academic quarter model by
     * @return boolean True if an academic quarter model was successfully associated with this model, false otherwise
     */
    public function setAcademicQuarterFromID($academicQuarterID)
    {
        $this->academicQuarter = new Academic_quarter_model;
        
        if($this->academicQuarter->loadPropertiesFromPrimaryKey($academicQuarterID))
        {
            return true;
        }
        
        $this->academicQuarter = null;
        return false;
    }
    
    /**
     * Summary of addCourseSectionTime
     * Add a course section time model to this course section model
     * 
     * @param Course_section_time_model $courseSectionTime The course section time model to associate with this model
     * @return boolean True if the course section time model was added successfully, false otherwise
     */
    public function addCourseSectionTime($courseSectionTime)
    {
        $searchstr = $courseSectionTime->getDayOfWeek() . $courseSectionTime->getStartTime() . $courseSectionTime->getEndTime();
        
        if(!isset($this->courseSectionTimes[$searchstr]))
        {
            $this->courseSectionTimes[$searchstr] = $courseSectionTime;
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of removeCourseSectionTime
     * Remove a course section time model form this course section model
     * 
     * @param Course_section_time_model $courseSectionTime The course section time model to associate with this model
     * @return boolean True if the course section time model was removed successfully, false otherwise
     */
    public function removeCourseSectionTime($courseSectionTime)
    {
        $searchstr = $courseSectionTime->getDayOfWeek() . $courseSectionTime->getStartTime() . $courseSectionTime->getEndTime();
        
        if(isset($this->courseSectionTimes[$searchstr]))
        {
            unset($this->courseSectionTimes[$searchstr]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of create
     * Save a new course section model into the CourseSections table in the database along with its associated Course section time models
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {   
        if($this->academicQuarter != null && $this->sectionName != null && $this->course != null && filter_var($this->callNumber, FILTER_VALIDATE_INT) && filter_var($this->hours, FILTER_VALIDATE_INT))
        {
            $data = array(
				'CourseID' => $this->course->getCourseID(), 
				'SectionName' => $this->sectionName, 
				'Hours' => $this->hours,
				'CallNumber' => $this->callNumber,
				'AcademicQuarterID' => $this->academicQuarter->getAcademicQuarterID()
			);
            
            $this->db->insert('CourseSections', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->courseSectionID = $this->db->insert_id();
                
                foreach($this->courseSectionTimes as $courseSectionTime)
                {
                    $courseSectionTime->setCourseSectionID($this->courseSectionID);
                    $courseSectionTime->create();
                }
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Summary of update
     * Update existing rows in the database associated with this course section model with newly modified information
     * along with any changes to the associated course section time models
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->courseSectionID != null && filter_var($this->courseSectionID, FILTER_VALIDATE_INT) && $this->academicQuarter != null && $this->sectionName != null && $this->course != null && filter_var($this->callNumber, FILTER_VALIDATE_INT) && filter_var($this->hours, FILTER_VALIDATE_INT))
        {
            $data = array(
				'CourseID' => $this->course->getCourseID(), 
				'SectionName' => $this->sectionName, 
				'Hours' => $this->hours,
				'CallNumber' => $this->callNumber,
				'AcademicQuarterID' => $this->academicQuarter->getAcademicQuarterID()
			);
            
            $this->db->where('CourseSectionID', $this->courseSectionID);
            $this->db->update('CourseSections');
            
            $sum = $this->db->affected_rows() > 0;
            
            $this->db->where('CourseSectionID', $this->courseSectionID);
            $this->db->delete('CourseSectionTimes');
            
            foreach($this->courseSectionTimes as $courseSectionTime)
            {
                $courseSectionTime->create();
            }
            
            return $sum > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this course section from the database and all associated models for this course
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->courseSectionID != null && filter_var($this->courseSectionID, FILTER_VALIDATE_INT))
        {
            $this->db->where('CourseSectionID', $this->courseSectionID);
            $this->db->delete('CourseSectionTimes');
            
            $this->db->where('CourseSectionID', $this->courseSectionID);
            $this->db->delete('CourseSections');
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of getAllCourseSections
     * Get all of the course section models in the database
     * 
     * @return Array An array containing all of the course section models stored in the database
     */
    public static function getAllCourseSections()
    {
        $db = get_instance()->db;
        
        $models = array();
        
        $db->select('CourseSectionID');
        $db->from('CourseSections');
        
        $results = $db->get();
        
        foreach($results->result_array() as $row)
        {
            $model = new Course_section_model;
            
            if($model->loadPropertiesFromPrimaryKey($row['CourseSectionID']))
            {
                array_push($models, $model);
            }
        }
        
        return $models;
    }
}