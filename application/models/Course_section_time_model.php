<?php

/**
 * Course_section_time_model short summary.
 *
 * A model that describes a single amount of time in a day that a course section is offered during the week
 */
class Course_section_time_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $courseSectionID;
    private $dayOfWeek;
    private $startTime;
    private $endTime;
    
    // Constants to represent the days of the week 
    const DAY_MONDAY = "Monday";
    const DAY_TUESDAY = "Tuesday";
    const DAY_WEDNESDAY = "Wednesday";
    const DAY_THURSDAY = "Thursday";
    const DAY_FRIDAY = "Friday";
    const DAY_SATURDAY = "Saturday";
    const DAY_SUNDAY = "Sunday";
    
    /**
     * Main constructor for Course_section_time_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of getCourseSectionID
     * Get the course section id associated with this model
     * 
     * @return int The course section id of this model
     */
    public function getCourseSectionID()
    {
        return $this->courseSectionID;
    }
    
    /**
     * Summary of getDayOfWeek
     * Get the day of the week for this particular course section time model (see the DAY constants)
     * 
     * @return string The day of the week for this course section time model (see the DAY constants)
     */
    public function getDayOfWeek()
    {
        return $this->dayOfWeek;
    }
    
	/**
     * Summary of getDayOfWeek
     * Get the BOSS letter for the day of the week for this particular course section time model
     * 
     * @return string A letter representing the day of the week for this course section time model
     */
    public function getDayOfWeekLetter()
    {
        if($this->dayOfWeek == self::DAY_THURSDAY)
		{
			return "R";
		}
		else
		{
			return substr($this->dayOfWeek[0], 0, 1);
		}
    }
	
    /**
     * Summary of getStartTime
     * Get the starting time of this course section time model in military time 
     * 
     * @return int The starting time of this course section time model (represented in military time)
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
    
    /**
     * Summary of getEndTime
     * Get the ending time of this course section time model in military time 
     * 
     * @return int The ending time of this course section time model (represented in military time)
     */
    public function getEndTime()
    {
        return $this->endTime;
    }
    
    /**
     * Summary of setCourseSectionID
     * Set the course section id to be associated with this time
     * 
     * @param int $courseSectionID The course section id to be associated with this model
     */
    public function setCourseSectionID($courseSectionID)
    {
        $this->courseSectionID = filter_var($courseSectionID, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Summary of setDayOfWeek
     * Set the day of the week for this course section time model
     * 
     * @param string $dayOfWeek The day of the week to associated with this model (see the DAY constants)
     */
    public function setDayOfWeek($dayOfWeek)
    {
        $this->dayOfWeek = filter_var($dayOfWeek, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    /**
     * Summary of setStartTime
     * Set the start time (in military time) to be associated with this model
     * 
     * @param int $startTime The start time (expressed in military time) to be associated with this model
     */
    public function setStartTime($startTime)
    {
        $this->startTime = filter_var($startTime, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Summary of setEndTime
     * Set the end time (in military time) to be associated with this model
     * 
     * @param int $endTime The end time (expressed in military time) to be associated with this model
     */
    public function setEndTime($endTime)
    {
        $this->endTime = filter_var($endTime, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Summary of create
     * Save a new course section time model into the CourseSectionTimes table in the database
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {
        if($this->courseSectionID != null && $this->dayOfWeek != null && $this->startTime != null && $this->endTime != null)
        {
            $data = array('CourseSectionID' => $this->courseSectionID, 'DayOfWeek' => $this->dayOfWeek, 'StartTime' => $this->startTime, 'EndTime' => $this->endTime);
            
            $this->db->insert('CourseSectionTimes', $data);
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this course section time model from the database
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->courseSectionID != null && $this->dayOfWeek != null && $this->startTime != null && $this->endTime != null)
        {
            $this->db->where('CourseSectionID', $this->courseSectionID);
            $this->db->where('DayOfWeek', $this->dayOfWeek);
            $this->db->where('StartTime', $this->startTime);
            $this->db->where('EndTime', $this->endTime);
            $this->db->delete('CourseSectionTimes');
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of getAllCourseSectionTimes
     * Get all of the course section time models associated with a particular course section id
     * 
     * @param int $courseSectionID The course section id to find course section time models by
     * @return array An array containing all the course section time models associated with the course section id provided
     */
    public static function getAllCourseSectionTimes($courseSectionID)
    {
        $models = array();
        
        if($courseSectionID != null && filter_var($courseSectionID, FILTER_VALIDATE_INT))
        {
            $db = get_instance()->db;
            
            $results = $db->get_where('CourseSectionTimes', array('CourseSectionID' => $courseSectionID));
            
            foreach($results->result_array() as $row)
            {
                $model = new Course_section_time_model;
                
                $model->courseSectionID = $row['CourseSectionID'];
                $model->dayOfWeek = $row['DayOfWeek'];
                $model->startTime = $row['StartTime'];
                $model->endTime = $row['EndTime'];
                
                array_push($models, $model);
            }
        }
        
        return $models;
    }
}