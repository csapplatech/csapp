<?php

class Advising_appointment_model extends CI_Model
{
    private $advisingAppointmentID;
    private $advisingScheduleID;
    private $startTime;
    private $endTime;
    
    function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Searches the 'Advising Appointments' table by the Advising Appointment's primary key and loads the properties from the Advising Appointment that matches the ID provided
     * Returns null if no matching advising schedule model can be found in the database.
     */
    public function loadPropertiesFromPrimaryKey($advisingAppointmentID)
    {
        $results = $this->db->get_where('AdvisingAppointments', array('AdvisingAppointmentID' => $advisingAppointmentID), 1);
        
        if($results->num_rows() > 0)
        {
            $row = $results->row_array();
            
            $this->advisingAppointmentID = $row['AdvisingAppointmentID'];
            $this->advisingScheduleID = $row['AdvisingScheduleID'];
            $this->startTime = $row['StartTime'];
            $this->endTime = $row['EndTime'];
            
            return true;
        }
        
        return false;
    }
    
    public function getAdvisingAppointmentID()
    {
    	return $this->$advisingAppointmentID;
    }
    
    public function getAdvisingScheduleID()
    {
    	return $this->advisingScheduleID;
   	}
   	
   	public function getStartTime()
   	{
   		return $this->startTime;
   	}
   	
   	public function getEndTime()
   	{
   		return $this->endTime;
   	}
   	
   	public function setAdvisingScheduleID($desiredAdvisingScheduleID)
   	{
   		$this->advisingScheduleID = filter_var($desiredAdvisingScheduleID, FILTER_SANITIZE_NUMBER_INT);
   	}
   	
   	public function setStartTime($desiredStartTime)
   	{
   		$this->startTime = preg_replace("([^0-9])", "", $desiredStartTime);
   	}
   	
   	public function setEndTime($desiredEndTime)
   	{
   		$this->endTime = preg_replace("([^0-9])", "", $desiredEndTime);
   	}
   	
   	public function update()
   	{
   		$this->advisingScheduleID = filter_var($this->advisingScheduleID, FILTER_VALIDATE_INT);
   		
   		if ($this->advisingScheduleID == false)
   		{
   			$this->advisingScheduleID == NULL;
   		}
   		
   		$this->startTime = filter_var($this->startTime, FILTER_VALIDATE_INT);
   		
   		if ($this->startTime == false)
   		{
   			$this->startTime == NULL;
   		}
   		
   		$this->endTime = filter_var($this->endTime, FILTER_VALIDATE_INT);
   		
   		if ($this->endTime == false)
   		{
   			$this->endTime == NULL;
   		}
   	
   		$data = array('AdvisingScheduleID' => $this->advisingScheduleID, 'StartTime' => $this->startTime, 'EndTime' => $this->endTime);
   		
   		$this->db->where('AdvisingAppointmentID', $this->advisingAppointmentID);
        $this->db->update('AdvisingAppointments', $data);
   	}
   	
   	public function create()
   	{
   		$data = array('AdvisingScheduleID' => $this->advisingScheduleID, 'StartTime' => $this->startTime, 'EndTime' => $this->endTime);
   		
   		$this->db->insert('AdvisingAppointments', $data);
   		
   		return $this->db->insert_id();
   	}
}