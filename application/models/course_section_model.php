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
    private $sectionName = null;
    private $course = null;
    private $academicQuarter = null;
    private $courseSectionTimes = array();
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function loadPropertiesFromPrimaryKey($courseSectionID)
    {
        
    }
    
    public function getCourseSectionID()
    {
        return $this->courseSectionID;
    }
    
    public function getCourse()
    {
        return $this->course;
    }
    
    public function getSectionName()
    {
        return $this->sectionName;
    }
    
    public function getAcademicQuarter()
    {
        return $this->academicQuarter;
    }
    
    public function getCourseSectionTimes()
    {
        return $this->courseSectionTimes;
    }
    
    public function setCourseSectionName($courseSectionName)
    {
        
    }
    
    public function setCourse($course)
    {
        
    }
    
    public function setAcademicQuarter($academicQuarter)
    {
        
    }
    
    public function addCourseSectionTime($courseSectionTime)
    {
        
    }
    
    public function removeCourseSectionTime($courseSectionTime)
    {
        
    }
    
    public function create()
    {
        
    }
    
    public function update()
    {
        
    }
    
    public function delete()
    {
        
    }
}
