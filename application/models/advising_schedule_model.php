<?php

class Advising_schedule_model extends CI_Model
{
    public $advisingScheduleID;
    public $advisorUserID;
    public $academicQuarterID;
    
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Searches the 'Advising Schedules' table by the Advising Schedule's primary key and loads the properties from the Advising Schedule that matches the ID provided
     * Returns null if no matching advising schedule model can be found in the database.
     */
    public function loadPropertiesFromPrimaryKey($advisingScheduleID)
    {
        $results = $this->db->get_where('AdvisingSchedules', array('AdvisingScheduleID' => $advisingScheduleID), 1);
        
        if($results->num_rows() > 0)
        {
            $row = $results->row_array();
            
            $this->advisingScheduleID = $row['UserID'];
            $this->advisorUserID = $row['AdvisorUserID'];
            $this->academicQuarterID = $row['AcademicQuarterID'];
            
            return true;
        }
        
        return false;
    }
    
    public function getAdvisingScheduleID()
    {
    	return $this->$advisingScheduleID;
    }
    
    public function getAdvisorUserID()
    {
    	return $this->advisorUserID;
   	}
   	
   	public function getAcademicQuarterID()
   	{
   		return $this->academicQuarterID;
   	}
   	
   	public function setAdvisorUserID($desiredAdvisorUserID)
   	{
   		$this->advisorUserID = filter_var($desiredAdvisorUserID, FILTER_SANITIZE_NUMBER_INT);
   	}
   	
   	public function setAcademicQuarterID($desiredAcademicQuarterID)
   	{
   		$this->academicQuarterID = filter_var($desiredAcademicQuarterID, FILTER_SANITIZE_NUMBER_INT);
   	}
   	
   	public function update()
   	{
   		$this->advisorUserID = filter_var($this->advisorUserID, FILTER_VALIDATE_INT);
   		
   		if ($this->advisorUserID == false)
   		{
   			$this->advisorUserID == NULL;
   		}
   		
   		$this->academicQuarterID = filter_var($this->academicQuarterID, FILTER_VALIDATE_INT);
   		
   		if ($this->academicQuarterID == false)
   		{
   			$this->academicQuarterID == NULL;
   		}
   	
   		$data = array('AdvisorUserID' => $this->advisorUserID, 'AcademicQuarterID' => $this->academicQuarterID);
   		
   		$this->db->where('AdvisingScheduleID', $this->advisingScheduleID);
        $this->db->update('AdvisingSchedules', $data);
   	}
   	
   	public function create()
   	{
   		$data = array('AdvisorUserID' => $this->advisorUserID, 'AcademicQuarterID' => $this->academicQuarterID);
   		
   		$this->db->insert('AdvisingSchedules', $data);
   		
        if($this->db->affected_rows() > 0)
        {
            $this->advisingScheduleID = $this->db->insert_id();
            return true;
        }
        else
        {
            return false;
        }
   	}
}