<?php

class Advising_schedule_model extends CI_Model
{
    private $advisingScheduleID;
    private $advisorUserID;
    private $academicQuarterID;
    
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
            
            $this->advisingScheduleID = $row['AdvisingScheduleID'];
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
      if($this->advisingScheduleID != null && filter_var($this->advisingScheduleID) && $this->advisorUserID != null && filter_var($this->advisorUserID, FILTER_VALIDATE_INT) && $this->academicQuarterID != null && filter_var($this->academicQuarterID, FILTER_VALIDATE_INT))
      {
        $data = array('AdvisorUserID' => $this->advisorUserID, 'AcademicQuarterID' => $this->academicQuarterID);
        
        $this->db->where('AdvisingScheduleID', $this->advisingScheduleID);
        $this->db->update('AdvisingSchedules', $data);
        
        if($this->db->affected_rows() > 0)
        {
          return true;
        }
      }
      
      return false;
    }
    
    public function create()
    {
        if($this->advisorUserID != null && filter_var($this->advisorUserID, FILTER_VALIDATE_INT) && $this->academicQuarterID != null && filter_var($this->academicQuarterID, FILTER_VALIDATE_INT))
        {
            $data = array('AdvisorUserID' => $this->advisorUserID, 'AcademicQuarterID' => $this->academicQuarterID);
     
            $this->db->insert('AdvisingSchedules', $data);
        
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
        if($this->advisingScheduleID != null)
        {
           //deletes all rows from ScheduledAdvisingAppointments having AdvisingAppointmentIDs under the current AdvisingScheduleID
           $advising_appointment_arr = $this->db->get_where('AdvisingAppointments', $this->advisingScheduleID);
           
           foreach($advising_appointment_arr->results_array() as $row)
           {
               $this->db->where('AdvisingAppointmentID', $row['AdvisingAppointmentID']);
               $this->db->delete('ScheduledAdvisingAppointments');  
           }
           
           //deletes all AdvisingAppointments under the current AdvisingScheduleID
           $this->db->where('AdvisingScheduleID', $this->advisingAppointmentID);
           $this->db->delete('AdvisingAppointments');
           
           //deletes all AdvisingSchedules under the current AdvisingScheduleID
           $this->db->where('AdvisingScheduleID', $this->advisingAppointmentID);
           $this->db->delete('AdvisingSchedules');
        }
        else
        {
            return false;
        }
    }

    public function getAllAdvisingAppointments()
    {
        $results = $this->db->get_where('AdvisingAppointments', $this->advisingScheduleID);
      
        $data_arr = array();
      
        foreach($results->result_array() as $row)
        {
            $appt = new Advising_appointment_model;
            
            $appt->advisingAppointmentID = $row['AdvisingAppointmentID'];
            $appt->advisingScheduleID = $row['AdvisingScheduleID'];
            $appt->startTime = $row['StartTime'];
            $appt->endTime = $row['EndTime'];
            
            array_push($data_arr, $course);
        }
        
        return $data_arr;      
    }

    public static function getAllAdvisingSchedules()
    {
        $db = get_instance()->db;
      
        $results = $db->get('AdvisingSchedules');
      
        $data_arr = array();
      
        foreach($results->result_array() as $row)
        {
            $schedule = new Advising_schedule_model;
        
            $schedule->advisingScheduleID = $row['AdvisingScheduleID'];
            $schedule->advisorUserID = $row['AdvisingUserID'];
            $schedule->academicQuarterID = $row['AcademicQuarterID'];
        
            array_push($data_arr, $course);
        }
      
        return $data_arr;
    }
}