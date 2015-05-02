<?php

/**
 * Summary of Advising_log_entry_model
 * 
 * A model used to describe a single advising log entry in the CSC Web App project
 */
class Advising_log_entry_model extends CI_Model
{
	// Member variables, use getter / setter functions for access
	private $advisingLogEntryID = null;
	private $studentUserID = null;
	private $advisorUserID = null;
	private $advisingLogEntryTypeID = null;
	private $timestamp = null;
	
	const ENTRY_TYPE_ADVISING_APPOINTMENT_COMPLETE = 1;
	const ENTRY_TYPE_ADVISING_APPOINTMENT_CANCELED_BY_STUDENT = 2;
	const ENTRY_TYPE_ADVISING_APPOINTMENT_CANCELED_BY_ADVISOR = 3;
	const ENTRY_TYPE_ADVISING_APPOINTMENT_SAVED_BY_STUDENT = 4;
	const ENTRY_TYPE_ADVISING_APPOINTMENT_SAVED_BY_ADVISOR = 5;
	
	/**
     * Main Constructor 
     */
    function __construct()
    {
        parent::__construct();
    }
	
	/**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a advising log entry model's data from the database into this object using a StudentTransferCourseID as a primary key lookup
     * 
     * @param int $advisingLogEntryID The primary key to lookup model properties in the database with
     * @return boolean True if a model's properties were successfully loaded from database, false otherwise
     */
	function loadPropertiesFromPrimaryKey($advisingLogEntryID)
	{
		if($advisingLogEntryID != null && filter_var($advisingLogEntryID, FILTER_VALIDATE_INT))
		{
			$result = $this->db->get_where('AdvisingLogEntries', array('AdvisingLogEntryID' => $advisingLogEntryID), 1);
			
			if($result->num_rows() > 0)
			{
				$row = $result->row_array();
				
				$this->advisingLogEntryID = $row['AdvisingLogEntryID'];
				$this->studentUserID = $row['StudentUserID'];
				$this->advisorUserID = $row['AdvisorUserID'];
				$this->advisingLogEntryTypeID = $row['AdvisingLogEntryTypeID'];
				$this->timestamp = $row['Timestamp'];
				
				return true;
			}
		}
		
		return false;
	}
	
	public function setStudentUser($user)
	{
		if($user->getUserID() != null)
		{
			$this->studentUserID = $user->getUserID();
			
			return true;
		}
		
		return false;
	}
	
	public function setAdvisorUser($user)
	{
		if($user->getUserID() != null)
		{
			$this->advisorUserID = $user->getUserID();
			
			return true;
		}
		
		return false;
	}
	
	public function setAdvisingLogEntryType($advisingLogEntryTypeID)
	{
		$this->advisingLogEntryTypeID = filter_var($advisingLogEntryTypeID, FILTER_SANITIZE_NUMBER_INT);
	}
	
	public function getAdvisingLogEntryID()
	{
		return $this->advisingLogEntryID;
	}
	
	public function getStudentUserID()
	{
		return $this->studentUserID;
	}
	
	public function getAdvisorUserID()
	{
		return $this->advisorUserID;
	}
	
	public function getAdvisingLogEntryType()
	{
		return $this->getAdvisingLogEntryTypeID();
	}
	
	public function getTimestamp()
	{
		return $this->timestamp;
	}
	
	public function create()
	{
		if($this->studentUserID != null && $this->advisorUserID != null && $this->advisingLogEntryTypeID != null && $this->timestamp != null)
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'AdvisorUserID' => $this->advisorUserID,
				'AdvisingLogEntryTypeID' => $this->advisingLogEntryTypeID,
				'Timestamp' => "NOW()"
			);
			
			$this->db->insert('AdvisingLogEntries', $data);
			
			if($this->db->affected_rows() > 0)
			{
				$this->advisingLogEntryID = $this->db->insert_id();
				
				return true;
			}
		}
		
		return false;
	}
	
	public function update()
	{
		if($this->advisingLogEntryID != null && $this->studentUserID != null && $this->advisorUserID != null && $this->advisingLogEntryTypeID != null && $this->timestamp != null)
		{
			$data = array(
				'StudentUserID' => $this->studentUserID,
				'AdvisorUserID' => $this->advisorUserID,
				'AdvisingLogEntryTypeID' => $this->advisingLogEntryTypeID,
				'Timestamp' => "NOW()"
			);
			
			$this->db->where('AdvisingLogEntryID', $this->advisingLogEntryID);
			$this->db->update('AdvisingLogEntries', $data);
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
	
	public function delete()
	{
		if($this->studentTransferCourseID != null)
		{
			$this->db->where('AdvisingLogEntryID', $this->advisingLogEntryID);
			$this->db->delete('AdvisingLogEntries');
			
			return $this->db->affected_rows() > 0;
		}
		
		return false;
	}
}