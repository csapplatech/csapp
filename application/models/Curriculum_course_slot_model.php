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
    private $name = null;
    private $minimumGrade = null;
    private $validCourseIDs = array();
    
    /**
     * Main constructor for Curriculum_course_slot_model
     */
    public function __construct()
    {
        parent::__construct();
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
                $this->name = $row['Name'];
                $this->minimumGrade = $row['MinimumGrade'];
                
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
     * @return int The minimum grade for this curriculum course slot
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
     * Summary of setCurriculum
     * Set the curriculum for this curriculum course slot model to be associated with
     * 
     * @param int $curriculum The curriculum to associate with this model
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
     * @param int $minimumGrade The minimum grade to be associated with this curriculum course slot
     */
    public function setMinimumGrade($minimumGrade)
    {
        $this->minimumGrade = filter_var($minimumGrade, FILTER_SANITIZE_NUMBER_INT);
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
     * Summary of create
     * Save a new curriculum course slot model into the CurriculumCourseSlots table and save all associated models
     * for storing valid courses to fill this slot
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {
        if($this->curriculumID != null && filter_var($this->curriculumID, FILTER_VALIDATE_INT) && $this->name != null)
        {
            $data = array('CurriculumID' => $this->curriculumID, 'Name' => $this->name);
            
            $this->db->insert('CurriculumCourseSlots', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->curriculumCourseSlotID = $this->db->insert_id();
                
                $data_arr = array();
                
                foreach($this->validCourseIDs as $courseID)
                {
                    array_push($data_arr, array('CurriculumCourseSlotID' => $this->curriculumCourseSlotID, 'CourseID' => $courseID));
                }
                
                $this->db->insert_batch('CurriculumSlotValidCourses', $data_arr);
                
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
        if($this->curriculumCourseSlotID != null)
        {
            $data = array('Name' => $this->name);
            
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->update('CurriculumCourseSlots');
            
            $sum = $this->db->affected_rows();
            
            $this->db->where('CurriculumCourseSlotID', $this->curriculumCourseSlotID);
            $this->db->delete('CurriculumSlotValidCourses');
            
            $data_arr = array();
            
            foreach($this->validCourseIDs as $courseID)
            {
                array_push($data_arr, array('CurriculumCourseSlotID' => $this->curriculumCourseSlotID, 'CourseID' => $courseID));
            }
            
            $this->db->insert_batch('CurriculumSlotValidCourses', $data_arr);
            
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
