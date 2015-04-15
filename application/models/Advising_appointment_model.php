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
        if($advisingAppointmentID != null && filter_var($advisingAppointmentID, FILTER_VALIDATE_INT))
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
        }
        
        return false;
    }
    
    public function getAdvisingAppointmentID()
    {
        return $this->advisingAppointmentID;
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
        if($this->advisingScheduleID != null && filter_var($this->advisingScheduleID, FILTER_VALIDATE_INT) && $this->startTime != null && filter_var($this->startTime, FILTER_VALIDATE_INT) && $this->endTime != null && filter_var($this->endTime, FILTER_VALIDATE_INT))
        {   
            $data = array('AdvisingScheduleID' => $this->advisingScheduleID, 'StartTime' => $this->startTime, 'EndTime' => $this->endTime);
            
            $this->db->where('AdvisingAppointmentID', $this->advisingAppointmentID);
            $this->db->update('AdvisingAppointments', $data);
            
            if($this->db->affected_rows() > 0)
            {
                return true;
            }
        }
      
        return false;
    }
    
    public function create()
    {
        if($this->startTime != null && filter_var($this->startTime, FILTER_VALIDATE_INT) && $this->endTime != null && filter_var($this->endTime, FILTER_VALIDATE_INT))
        {
            $data = array('AdvisingScheduleID' => $this->advisingScheduleID, 'StartTime' => $this->startTime, 'EndTime' => $this->endTime);
            
            $this->db->insert('AdvisingAppointments', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->advisingAppointmentID = $this->db->insert_id();
                
                return true;
            }
        }
        
        return false;
    }
    
    public function delete()
    {
        if($this->advisingAppointmentID != null)
        {
            $this->db->where('AdvisingAppointmentID', $this->advisingAppointmentID);
            $this->db->delete('ScheduledAdvisingAppointments');
            
            $this->db->where('AdvisingAppointmentID', $this->advisingAppointmentID);
            $this->db->delete('AdvisingAppointments');
            
            return $this->db->affected_rows() > 0;        
        }
        else
        {
            return false;
        }
    }
    
    public static function getAllAdvisingAppointments()
    {
        $db = get_instance()->db;
      
        $results = $db->get('AdvisingAppointments');
        
        $data_arr = array();
        
        foreach($results->result_array() as $row)
        {
            $appt = new Advising_appointment_model;
            
            $appt->advisingAppointmentID = $row['AdvisingAppointmentID'];
            $appt->advisingScheduleID= $row['AdvisingScheduleID'];
            $appt->startTime = $row['StartTime'];
            $appt->endTime = $row['EndTime'];
            
            array_push($data_arr, $course);
        }
        
        return $data_arr;
    }    
}