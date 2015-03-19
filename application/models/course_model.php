<?php

/**
 * course_model short summary.
 *
 * A model used to describe a single Course offered at the university
 */
class Course_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $courseID;
    private $courseName;
    private $courseNumber;
    private $courseDescription;
    
    /**
     * Main constructor for Course_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
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
                $this->courseNumber = $row['CourseNumber'];
                $this->courseDescription = $row['CourseDescription'];
                
                return true;
            }
        }
        
        return false;
    }
    
    public function getCourseID()
    {
        return $this->courseID;
    }
    
    public function getCourseName()
    {
        return $this->courseName;
    }
    
    public function getCourseNumber()
    {
        return $this->courseNumber;
    }
    
    public function getCourseDescription()
    {
        return $this->courseDescription;
    }
    
    public function setCourseName($courseName)
    {
        $this->courseName = filter_var($courseName, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    public function setCourseNumber($courseNumber)
    {
        $this->courseNumber = filter_var($courseNumber, FILTER_SANITIZE_NUMBER_INT);
    }
    
    public function setCourseDescription($courseDescription)
    {
        $this->courseDescription = filter_var($courseDescription, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    public function update()
    {
        if($this->courseID != null && $this->courseName != null && $this->courseNumber != null && filter_var($this->courseNumber, FILTER_VALIDATE_INT))
        {
            $data = array('CourseName' => $this->courseName, 'CourseNumber' => $this->courseNumber, 'CourseDescription' => $this->courseDescription);
            
            $this->db->where('CourseID', $this->courseID);
            $this->db->update('Courses', $data);
            
            if($this->db->affected_rows() > 0)
            {
                return true;
            }
        }
        return false;
    }
    
    public function create()
    {
        if($this->courseName != null && $this->courseNumber != null && filter_var($this->courseNumber, FILTER_VALIDATE_INT))
        {
            $data = array('CourseName' => $this->courseName, 'CourseNumber' => $this->courseNumber, 'CourseDescription' => $this->courseDescription);
            
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
            $this->db->where('CourseID', $this->courseID);
            $this->db->delete('CourseSections');
            
            $this->db->where('CourseID', $this->courseID);
            $this->db->or_where('RequisiteCourseID', $this->courseID);
            $this->db->delete('CourseRequisites');
            
            $this->db->where('EquivilentCourseID', $this->courseID);
            $this->db->delete('TransferCourses');
            
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
     * Summary of getAllCourses
     * Get all of the courses saved in the database
     * 
     * @return An array of all the course models represented in the database
     */
    public static function getAllCourses()
    {
        $results = $this->db->get('Courses');
        
        $data_arr = array();
        
        foreach($results->result_array() as $row)
        {
            $course = new Course_model;
            
            $course->courseID = $row['CourseID'];
            $course->courseName = $row['CourseName'];
            $course->courseNumber = $row['CourseNumber'];
            $course->courseDescription = $row['CourseDescription'];
            
            array_push($data_arr, $course);
        }
        
        return $data_arr;
    }
}
