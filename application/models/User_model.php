<?php

/**
 * Summary of User_model
 * 
 * A model used to describe a single user of the CSC Web App project
 */
class User_model extends CI_Model
{
    // Member variables, use getter / setter functions for access
    private $userID = null;
    private $emailAddress = null;
    private $passwordHash = null;
    private $name = null;
	private $userStateID = null;
    private $roles = array();
    private $coursesTaken = array();
	private $curriculums = array();
    
    // Constants to represent the various user roles as reflected in the CSC Web App database
    // If the table `Roles` or any of its rows are ever modified, reflect those changes in these constants
    const ROLE_ADMIN = 1;
    const ROLE_PROGRAM_CHAIR = 2;
    const ROLE_ADVISOR = 3;
    const ROLE_STUDENT = 4;
    
	// Constants to represent the various user states as reflected in the CSC Web app database
	// If the table `UserStates` or any of its rows are ever modified, reflect those changes in these constants
	const STATE_NOT_ACTIVATED = 1;
	const STATE_ACTIVATED = 2;
	const STATE_BLOCKED = 3;
	
    /**
     * Main Constructor for User_model
     */
    function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Summary of loadPropertiesFromPrimaryKey
     * Loads a user model's data from the database into this object using a UserID as a primary key lookup
     * 
     * @param int $userID The primary key (UserID) to lookup user properties in the database with
     * @return boolean True if a user model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromPrimaryKey($userID)
    {
        if($userID != null)
        {
            if(filter_var($userID, FILTER_VALIDATE_INT))
            {
                $results = $this->db->get_where('Users', array('UserID' => $userID), 1);
                
                if($results->num_rows() > 0)
                {
                    $row = $results->row_array();
                    
                    $this->userID = $row['UserID'];
                    $this->emailAddress = $row['EmailAddress'];
                    $this->passwordHash = $row['PasswordHash'];
                    $this->name = $row['Name'];
					//$this->userStateID = $row['UserStateID'];
                    
                    $role_results = $this->db->get_where('UserRoles', array('UserID' => $userID));
                    
                    if($role_results->num_rows() > 0)
                    {
                        foreach($role_results->result_array() as $row)
                        {
                            array_push($this->roles, $row['RoleID']);
                        }
                    }
                    
                    $results = $this->db->get_where('StudentCourseSections', array('StudentUserID' => $this->userID));
                    
                    foreach($results->result_array() as $row)
                    {
                        $courseSection = new Course_section_model;
                        
                        if($courseSection->loadPropertiesFromPrimaryKey($row['CourseSectionID']))
                        {
                            $this->addCourseSection($courseSection, $row['Grade']);
                        }
                    }
                    
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Summary of loadPropertiesFromEmailAddress
     * Loads a user model's data from the database into this object using an email address as lookup
     * 
     * @param mixed $emailAddress The email address to lookup user properties in the database with
     * @return boolean True if a user model's properties were successfully loaded from database, false otherwise
     */
    public function loadPropertiesFromEmailAddress($emailAddress)
    {
        if($emailAddress != null)
        {
            $emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);
            
            if(filter_var($emailAddress, FILTER_VALIDATE_EMAIL))
            {
                $results = $this->db->get_where('Users', array('EmailAddress' => $emailAddress), 1);
                
                if($results->num_rows() > 0)
                {
                    $row = $results->row_array();
                    
                    $this->userID = $row['UserID'];
                    $this->emailAddress = $row['EmailAddress'];
                    $this->passwordHash = $row['PasswordHash'];
                    $this->name = $row['Name'];
					$this->userStateID = $row['UserStateID'];
                    
                    $role_results = $this->db->get_where('UserRoles', array('UserID' => $userID));
                    
                    if($role_results->num_rows() > 0)
                    {
                        foreach($role_results->result_array() as $row)
                        {
                            array_push($this->roles, $row['RoleID']);
                        }
                    }
                    
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Summary of setPassword
     * Sets the password for the user model and associates is hash with the passwordHash of the model
     * 
     * @param string $password The new password to set for this user model
     */
    public function setPassword($password)
    {
        $this->passwordHash = hash('sha512', $password);
    }
    
    /**
     * Summary of getPasswordHash
     * Get the password hash string associated with this user model
     * 
     * @return string The hash of the password associated with this user model or null if model not saved in database
     */
    public function getPasswordHash()
    {
        return $this->passwordHash;
    }
    
    /**
     * Summary of getUserID
     * Get the UserID (Primary key) of this user model
     * 
     * @return int The user id associated with this user model or null if model not saved in database
     */
    public function getUserID()
    {
        return $this->userID;
    }
    
    /**
     * Summary of getEmailAddress
     * Get the email address of this user model
     * 
     * @return string The email address associated with this user model or null if model not saved in database
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    
	/**
	 * Summary of getCurriculums
	 * Get all of the curriculums that this user model is bound to
	 *
	 * @return Array An array containing all of the curriculum models this user is bound to
	 */
	public function getCurriculums()
	{
		return $this->curriculums;
	}
	
    /**
     * Summary of setEmailAddress
     * Set the email address to be assoicated with this user model
     * 
     * @param string $emailAddress The email address to associate with this user model
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = filter_var($emailAddress, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Summary of setName
     * Set the name of the user
     * 
     * @param string $name The name to associate with this user model
     */
    public function setName($name)
    {
        $this->name = filter_var($name, FILTER_SANITIZE_MAGIC_QUOTES);
    }
    
	/**
     * Summary of setState
     * Set the user account state of the user
     * 
     * @param integer $state The state of this user model (see the STATE constants)
     */
    public function setState($state)
    {
        $this->userStateID = filter_var($state, FILTER_SANITIZE_NUMBER_INT);
    }
	
	/**
	 * Summary of addCurriculum
	 * Add a curriculum model to bind to this user model
	 *
	 * @param Curriculum_model The curriculum model to bind to this user model
	 *
	 * @return boolean True if the curriculum was added, false otherwise
	 */
	public function addCurriculum($curriculum)
	{
		if($curriculum != null && $curriculum->getCurriculumID() != null && !isset($this->curriculums[$curriculum->getCurriculumID()]))
		{
			$this->curriculums[$curriculum->getCurriculumID()] = $curriculum;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Summary of removeCurriculum
	 * Remove a curriculum model that is bound to this user model
	 *
	 * @param Curriculum_model The curriculum model to remove from this user model
	 *
	 * @return boolean True if the curriculum was removed, false otherwise
	 */
	public function removeCurriculum($curriculum)
	{
		if($curriculum != null && $curriculum->getCurriculumID() != null && !isset($this->curriculums[$curriculum->getCurriculumID()]))
		{
			unset($this->curriculums[$curriculum->getCurriculumID()]);
			return true;
		}
		else
		{
			return false;
		}
	}
	
    /**
     * Summary of getName
     * Get the name of the user
     * 
     * @return string The name associated with this user model or null if model not saved in database
     */
    public function getName()
    {
        return $this->name;
    }
    
	/**
     * Summary of getState
     * Get the user account state of the user (see STATE constants of this class)
     * 
     * @return integer The state of this user model or null if model not saved in database
     */
    public function getState()
    {
        return $this->userStateID;
    }
	
    /**
     * Summary of isStudent
     * Check whether this user has the role of a student
     * 
     * @return boolean True is the user has a student role, false otherwise
     */
    public function isStudent()
    {
        return in_array(self::ROLE_STUDENT, $this->roles);
    }
    
    /**
     * Summary of isAdmin
     * Check whether this user has the role of a administrator
     * 
     * @return boolean True is the user has an administrator role, false otherwise
     */
    public function isAdmin()
    {
        return in_array(self::ROLE_ADMIN, $this->roles);
    }
    
    /**
     * Summary of isProgramChair
     * Check whether this user has the role of program chair
     * 
     * @return boolean True is the user has a program chair role, false otherwise
     */
    public function isProgramChair()
    {
        return in_array(self::ROLE_PROGRAM_CHAIR, $this->roles);
    }
    
    /**
     * Summary of isAdvisor
     * Check whether this user has the role of a advisor
     * 
     * @return boolean True is the user has a advisor role, false otherwise
     */
    public function isAdvisor()
    {
        return in_array(self::ROLE_ADVISOR, $this->roles);
    }
    
    /**
     * Summary of addRole
     * Adds a role to the user model if the role isn't already enabled
     * 
     * @param int $roleType The role to add to the user (see ROLE constants)
     */
    public function addRole($roleType)
    {
        if(!in_array($roleType, $this->roles))
        {
            array_push($this->roles, $roleType);
        }
    }
    
    /**
     * Summary of removeRole
     * Removes a role from the user model if the role was enabled
     * 
     * @param int $roleType The role to remove from the user (see ROLE constants)
     */
    public function removeRole($roleType)
    {
        if(in_array($roleType, $this->roles))
        {
            unset($this->roles[array_search($roleType, $this->roles)]);
        }
    }
    
    /**
     * Summary of getAdvisees
     * Get all of the student users who are advisees of this advisor user
     * 
     * @return array An array containing user models of the students who are advised by this user, array is empty if user model does not have an advisor role
     */
    public function getAdvisees()
    {
        $advisees = array();
        
        if($this->isAdvisor())
        {
            $this->db->select('StudentUserID');
            $this->db->from('StudentAdvisors');
            $this->db->where('AdvisorUserID', $this->userID);
            
            $results = $this->db->get();
            
            foreach($results->result_array() as $row)
            {
                $student = new User_model;
                if($student->loadPropertiesFromPrimaryKey($row['StudentUserID']))
                {
                    array_push($advisees, $student);
                }
            }
        }
        
        return $advisees;
    }
    
    /**
     * Summary of getAdvisor
     * Get the advisor user model associated with this student user model
     * 
     * @return User_model The user model for the advisor of this student user model, or null if no advisor exists or this model doesn't have a student role
     */
    public function getAdvisor()
    {
        if($this->isStudent())
        {
            $this->db->select('AdvisorUserID');
            $this->db->from('StudentAdvisors');
            $this->db->where('StudentUserID', $this->userID);
            
            $results = $this->db->get();
            
            $advisor = new User_model;
            
            $row = $results->row_array();
            
            if($advisor->loadPropertiesFromPrimaryKey($row['AdvisorUserID']))
            {
                return $advisor;
            }
        }
        
        return null;
    }
    
    /**
     * Summary of setAdvisor
     * Set the advisor to be associated with this student user model
     * 
     * @param User_model $advisor A user model that has the role of advisor
     * @return booelan True if the advisor was successfully associated with the student in the database, false otherwise
     */
    public function setAdvisor($advisor)
    {
        if($this->userID != null && $this->isStudent() && $advisor->getUserID() != null && $advisor->isAdvisor())
        {
            $this->db->where('StudentUserID', $this->userID);
            $this->db->delete('StudentAdvisors');
            
            $data = array('StudentUserID' => $this->userID, 'AdvisorUserID' => $advisor->getUserID());
            
            $this->db->insert('StudentAdvisors', $data);
            
            return $this->db->affected_rows() > 0;
        }
        
        return false;
    }
    
    /**
     * Summary of addCourseSection
     * Add a course section to be associated with this user model
     * 
     * @param mixed $courseSection The course section model to associate with this model
     * @param mixed $grade The grade this student got for the course section (0 = F, 4 = A)
     * @return boolean True if the course section was successfully added, false otherwise
     */
    public function addCourseSection($courseSection, $grade)
    {
        $searchstr = $courseSection->toString();
        
        if(!isset($this->coursesTaken[$searchstr]))
        {
            $this->coursesTaken[$searchstr] = array();
            $this->coursesTaken[$searchstr][0] = $courseSection;
            $this->coursesTaken[$searchstr][1] = $grade;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of removeCourseSection
     * Remove a course section model from being associated with this model
     * 
     * @param mixed $courseSection The course section to remove from this model
     * @return boolean True if the course section was successfully removed, false otherwise
     */
    public function removeCourseSection($courseSection)
    {
        $searchstr = $courseSection->toString();
        
        if(isset($this->coursesTaken[$searchstr]))
        {
            unset($this->coursesTaken[$searchstr]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Summary of getAllCoursesTaken
     * Get all of the courses that have been taken by this user model
     * 
     * @return Array An array containing all of the course sections taken by this user model
     */
    public function getAllCoursesTaken()
    {
        $data = array();
        
        foreach($this->coursesTaken as $courseTaken)
        {
            array_push($data, $courseTaken);
        }
        
        return $data;
    }
	
	/**
	 * Summary of getGradeForCourseSection
	 * Get the grade the student user model got for a particular course section
	 *
	 * @param Course_section_model The course section model to look up a grade for
	 * @return mixed Returns the grade a student got for that course section or false if no grade was found
	 */
	public function getGradeForCourseSection($courseSection)
	{
		$searchstr = $courseSection->toString();
		
		if(isset($this->coursesTaken[$searchstr]))
		{
			return $this->coursesTaken[$searchstr][1];
		}
		else
		{
			return false;
		}
	}
	
    /**
     * Summary of update
     * Update existing rows in the database associated with this user model with newly modified information
     * 
     * @return boolean True if all rows associated with this model were successfully modified in the database, false otherwise
     */
    public function update()
    {
        if($this->userID != null && filter_var($this->emailAddress, FILTER_VALIDATE_EMAIL) && filter_var($this->userStateID, FILTER_VALIDATE_INT))
        {
            $data = array('EmailAddress' => $this->emailAddress, 'PasswordHash' => $this->passwordHash, 'Name' => $this->name, 'UserStateID' => $this->userStateID);
            
            $this->db->where('UserID', $this->userID);
            $this->db->update('Users', $data);
            
            $sum = $this->db->affected_rows();
            
            $this->db->where('UserID', $this->userID);
            $this->db->delete('UserRoles');
            
            if(count($this->coursesTaken) > 0)
            {
                foreach($this->roles as $role)
                {
                    $this->db->insert('UserRoles', array('UserID' => $this->userID, 'RoleID' => $role));
                }
            }
            
			$this->db->where('UserID', $this->userID);
			$this->db->delete('UserCurriculums');
			
			if(count($this->curriculums) > 0)
			{
				foreach($this->curriculums as $curriculum)
				{
					$this->db->insert('UserCurriculums', array('UserID' => $this->userID, 'CurriculumID' => $curriculum->getCurriculumID()));
				}
			}
			
            $this->db->where('StudentUserID', $this->userID);
            $this->db->delete('StudentCourseSections');
            
            
            if(count($this->coursesTaken) > 0)
            {
                $data_arr = array();
                
                foreach($this->coursesTaken as $courseTaken)
                {
                    $this->db->insert('StudentCourseSections', array('StudentUserID' => $this->userID, 'CourseSectionID' => $courseTaken[0]->getCourseSectionID(), 'Grade' => $courseTaken[1]));
                }
            }
            
            return $sum > 0;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Summary of create
     * Save a new user model into the Users table in the database and all associated user role rows into the UserRoles table
     * and binds the newly generated row id to the user id property of the user model
     * 
     * @return boolean True if all rows were successfully saved in the database, false otherwise
     */
    public function create()
    {   
        if(filter_var($this->emailAddress, FILTER_VALIDATE_EMAIL) && filter_var($this->userStateID, FILTER_VALIDATE_INT))
        {
            $data = array('EmailAddress' => $this->emailAddress, 'PasswordHash' => $this->passwordHash, 'Name' => $this->name, 'UserStateID' => $this->userStateID);
            
            $this->db->insert('Users', $data);
            
            if($this->db->affected_rows() > 0)
            {
                $this->userID = $this->db->insert_id();
                
                foreach($this->roles as $role)
                {
                    $roledata = array('UserID' => $this->userID, 'RoleID' => $role);
                    
                    $this->db->insert('UserRoles', $roledata);
                }
                
				if(count($this->curriculums) > 0)
				{
					foreach($this->curriculums as $curriculum)
					{
						$this->db->insert('UserCurriculums', array('UserID' => $this->userID, 'CurriculumID' => $curriculum->getCurriculumID()));
					}
				}
				
                if(count($this->coursesTaken) > 0)
                {
                    $data_arr = array();
                    
                    foreach($this->coursesTaken as $courseTaken)
                    {
                        $this->db->insert('StudentCourseSections', array('StudentUserID' => $this->userID, 'CourseSectionID' => $courseTaken[0]->getCourseSectionID(), 'Grade' => $courseTaken[1]));
                    }
                }
                
                return true;
            }
        }
        return false;
    }
    
    /**
     * Summary of delete
     * Delete this user from the database and all associated models for this user
     * 
     * @return boolean True if the model and associated models were successfully deleted, false otherwise
     */
    public function delete()
    {
        if($this->userID != null)
        {
            $this->db->where('UserID', $this->userID);
            $this->db->delete('UserRoles');
            
            $this->db->where('StudentUserID', $this->userID);
            $this->db->delete('StudentCourseSections');
            
            $this->db->where('StudentUserID', $this->userID);
            $this->db->or_where('AdvisorUserID', $this->userID);
            $this->db->delete('StudentAdvisors');
            
            $this->db->where('UserID', $this->userID);
            $this->db->delete('Users');
            
            return $this->db->affected_rows() > 0;
        }
        else
        {
            return false; 
        }
    }
    
    /**
     * Summary of authenticate
     * Check a submitted password guess against this user model's hashed password from the database
     * Usees a constant time string comparison to prevent timing attacks
     * 
     * @param string $passwordGuess The raw password used to authenticate against this user model
     * @return boolean True if the password hashes and matches this user model, false otherwise
     */
    public function authenticate($passwordGuess)
    {
        $hashedPasswordGuess = hash('sha512', $passwordGuess);
        
        $len = strlen($hashedPasswordGuess);
        
        $finalFlag = true;
        
        for($i=0;$i<$len;$i++)
        {
            if ($finalFlag && $hashedPasswordGuess[$i] != $this->passwordHash[$i])
            {
                $finalFlag = false;
            }
        }
        
        return $finalFlag;
    }
}
