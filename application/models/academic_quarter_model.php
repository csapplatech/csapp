<?php

/**
 * academic_quarter_model short summary.
 *
 * A model to represent a single Academic Quarter in the database
 */
class Academic_quarter_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $academicQuarterID = null;
    private $name = null;
    private $year = null;
    
    // Constants to represent the names of the different quarters
    const NAME_FALL = "Fall";
    const NAME_WINTER = "Winter";
    const NAME_SPRING = "Spring";
    const NAME_SUMMER = "Summer";
    
    /**
     * Main constructor for Academic_quarter_model
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads an academic quarter model's data from the database into this object using AcademicQuarterID as a primary key lookup
     * 
     * @param int $userID The primary key (AcademicQuarterID) to lookup user properties in the database with
     * @return boolean True if an academic quarter model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($academicQuarterID)
    {
        if($academicQuarterID != null && filter_var($academicQuarterID, FILTER_VALIDATE_INT))
        {
            $results = $this->db->get_where('AcademicQuarters', array('AcademicQuarterID'=>$academicQuarterID), 1);
            
            $row = $results->row_array();
            
            $this->academicQuarterID = $row['AcademicQuarterID'];
            $this->name = $row['Name'];
            $this->year = $row['Year'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of getAcademicQuarterID
     * Get the academic quarter id (primary key) associated with this model
     * 
     * @return int The academic quarter id (primary key) associated with this model
     */
    public function getAcademicQuarterID()
    {
        return $this->academicQuarterID;
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
     * Summary of getYear
     * Get the year associated with this model
     * 
     * @return int The year associated with this model
     */
    public function getYear()
    {
        return $this->year;
    }
    
    /**
     * Summary of setName
     * Set the name to be associated with this academic calendar model
     * 
     * @param string $name The name to be associated with this model
     */
    public function setName($name)
    {
        $this->name = filter_var($name, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
    /**
     * Summary of setYear
     * Set the year to be associated with this academic calendar model
     * 
     * @param int $year The year to be associated with this model
     */
    public function setYear($year)
    {
        $this->year = filter_var($year, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Summary of create
     * Save a new academic quarter model into the AcademicQuarters table in the database
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {
        if($this->name != null && $this->year != null && filter_var($this->year, FILTER_VALIDATE_INT))
        {
            $data = array('Name' => $this->name, 'Year' => $this->year);
            
            $this->db->insert('AcademicQuarters', $data);
            
            $this->academicQuarterID = $this->db->insert_id();
            
            return ($this->db->affected_rows() > 0);
        }
        
        return false;
    }
    
    /**
     * Summary of update
     * Update existing rows in the database associated with this academic quarter model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->academicQuarterID != null && $this->name != null && $this->year != null && filter_var($this->academicQuarterID, FILTER_VALIDATE_INT) && filter_var($this->year, FILTER_VALIDATE_INT))
        {
            $data = array('Name' => $this->name, 'Year' => $this->year);
            
            $this->db->where('AcademicQuarterID', $this->academicQuarterID);
            $this->db->update('AcademicQuarters', $data);
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
}
