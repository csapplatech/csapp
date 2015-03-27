<?php

/**
 * course_model short summary.
 *
 * A model used to describe a single curriculum for a program at the University
 */
class Curriculum_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $curriculumID = null;
    private $name = null;
    private $dateCreated = null;
    private $curriculumCourseSlots = array();
    
    /**
     * Main constructor for Curriculum_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a curriculum model's data and its associated models from the database into this object using a CurriculumID as a primary key lookup
     * 
     * @param int $curriculumID The primary key (CurriculumID) to lookup course properties in the database with
     * @return boolean True if a curriculum model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($curriculumID)
    {
        if($curriculumID != null && filter_var($curriculumID, FILTER_VALIDATE_INT))
        {
            $results = $this->db->get_where('Curriculums', array('CurriculumID' => $curriculumID), 1);
            
            if($results->num_rows() > 0)
            {
                $row = $results->row_array();
                
                $this->curriculumID = $row['CurriculumID'];
                $this->name = $row['Name'];
                $this->dateCreated = $row['DateCreated'];
                
                $this->db->select('CurriculumCourseSlotID');
                $this->db->from('CurriculumCourseSlots');
                $this->db->where('CurriculumID', $this->curriculumID);
                
                $results = $this->db->get();
                
                if($results->num_rows() > 0)
                {
                    foreach($results->result_array() as $row)
                    {
                        $curriculumCourseSlot = new Curriculum_course_slot_model;
                        $curriculumCourseSlot->loadPropertiesFromPrimaryKey($row['CurriculumCourseSlotID']);
                        
                        array_push($this->curriculumCourseSlots, $curriculumCourseSlot);
                    }
                }
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Summary of getCurriculumID
     * Get the curriculum id (primary key) associated with this model
     * 
     * @return int The curriculum id associated with this model
     */
    public function getCurriculumID()
    {
        return $this->curriculumID;
    }
    
    /**
     * Summary of getName
     * Get the name associated with this model
     * 
     * @return string The name associated with this model
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Summary of getDateCreated
     * Get the creation date associated with this model
     * 
     * @return mixed The date and time of when this model was created
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
    
    /**
     * Summary of getCurriculumCourseSlots
     * Get all of the curriculum course slots associated with this curriculum model
     * 
     * @return array An array containing all the curriculum course slot models associated with this curriculum
     */
    public function getCurriculumCourseSlots()
    {
        return $this->curriculumCourseSlots;
    }
    
    /**
     * Summary of setName
     * Set the name to be associated with this curriculum model
     * 
     * @param string $name The name to be associated with this curriculum model
     */
    public function setName($name)
    {
        $this->name = filter_var($name, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    /**
     * Summary of addCurriculumCourseSlot
     * Add a new curriculum course slot model to this curriculum model
     * 
     * @param Curriculum_course_slot_model $curriculumCourseSlot The curriculum course slot model to be associated with this curriculum model
     */
    public function addCurriculumCourseSlot($curriculumCourseSlot)
    {
        if( !isset($this->curriculumCourseSlots[$curriculumCourseSlot->getName()]))
        {
            if($this->curriculumID != null)
            {
                $curriculumCourseSlot->setCurriculum($this);
            }
            $this->curriculumCourseSlots[$curriculumCourseSlot->getName()] = $curriculumCourseSlot;
        }
    }
    
    /**
     * Summary of removeCurriculumCourseSlot
     * Remove a curriculum course slot model from this curriculum model
     * 
     * @param int $curriculumCourseSlot The curriculum course slot model to remove from this model
     */
    public function removeCurriculumCourseSlot($curriculumCourseSlot)
    {
        if(isset($this->curriculumCourseSlots[$curriculumCourseSlot->getName()]))
        {
            unset($this->curriculumCourseSlots[$curriculumCourseSlot->getName()]);
        }
    }
    
    /**
     * Summary of create
     * Save a new curriculum model into the Curriculums table in the database and all associated 
     * curriculum course slot models into the CurriculumCourseSlots table
     * and binds the newly generated row id to the curriculum id property of the curriculum model
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {
        if($this->name != null)
        {
            $this->dateCreated = date('Y-m-d H:i:s');
            
            $data = array('Name' => $this->name, 'DateCreated' => $this->dateCreated);
            
            $this->db->insert('Curriculums', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->curriculumID = $this->db->insert_id();
                
                foreach($this->curriculumCourseSlots as $course)
                {
                    $course->setCurriculum($this);
                    $course->create();
                }
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Summary of update
     * Update existing rows in the database associated with this curriculum model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->curriculumID != null && $this->name != null)
        {
            $data = array('Name' => $this->name);
            
            $this->db->where('CurriculumID', $this->curriculumID);
            $this->db->update('Curriculums');
            
            $results = $this->db->get_where('CurriculumCourseSlots', array('CurriculumID' => $this->curriculumID));
            
            foreach($results->result_array() as $row)
            {
                $courseSlot = new Curriculum_course_slot_model;
                $courseSlot->loadPropertiesFromPrimaryKey($row['CurriculumCourseSlotID']);
                
                $courseSlot->delete();
            }
            
            foreach($this->curriculumCourseSlots as $course)
            {
                $course->create();
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this curriculum from the database and all associated models for this curriculum
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->curriculumID != null)
        {
            foreach($this->curriculumCourseSlots as $course)
            {
                $course->delete();
            }
            
            $this->db->where('CurriculumID', $this->curriculumID);
            $this->db->delete('Curriculums');
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of getAllCurriculums
     * Get all the curriculums stored in the database
     * 
     * @return array An array of all the curriculum models currently stored in the database
     */
    public static function getAllCurriculums()
    {
        $db = get_instance()->db;
        
        $data = array();
        
        $db->select('CurriculumID');
        $db->from('Curriculums');
        
        $results = $db->get();
        
        foreach($results->result_array() as $row)
        {
            $model = new Curriculum_model;
            $model->loadPropertiesFromPrimaryKey($row['CurriculumID']);
            array_push($data, $model);
        }
        
        return $data;
    }
}   
