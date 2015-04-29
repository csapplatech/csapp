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
	private $passwordSalt = null;
    private $name = null;
	private $userStateID = null;
	private $lastLogin = null;
    
    // Constants to represent the various user roles as reflected in the CSC Web App database
    // If the table `Roles` or any of its rows are ever modified, reflect those changes in these constants
    const ROLE_ADMIN = 1;
    const ROLE_PROGRAM_CHAIR = 2;
    const ROLE_ADVISOR = 3;
    const ROLE_STUDENT = 4;
	const ROLE_GUEST = 5;
    
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
					
					if(strpos($row['PasswordHash'], "$"))
					{
						$passwordComponents = explode("$", $row['PasswordHash']);
						
						$this->passwordHash = $passwordComponents[1];
						$this->passwordSalt = $passwordComponents[0];
					}
					else
					{
						$this->passwordHash = $row['PasswordHash'];
						$this->passwordSalt = "";
					}
					
                    $this->name = $row['Name'];
					$this->lastLogin = $row['LastLogin'];
					$this->userStateID = $row['UserStateID'];
                    
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
                    
					if(strpos($row['PasswordHash'], "$"))
					{
						$passwordComponents = explode("$", $row['PasswordHash']);
						
						$this->passwordHash = $passwordComponents[1];
						$this->passwordSalt = $passwordComponents[0];
					}
					else
					{
						$this->passwordHash = $row['PasswordHash'];
						$this->passwordSalt = "";
					}
					
                    $this->name = $row['Name'];
					$this->lastLogin = $row['LastLogin'];
					$this->userStateID = $row['UserStateID'];
					
                    return true;
                }
            }
        }
        return false;
    }
    
	/**
     * Summary of setUserID
     * Sets the user id for the user model
     * 
     * @param int $userID The user id to set for this user model
	 * @return boolean True if the user id provided is valid and bound to the model, false otherwise
     */
	public function setUserID($userID)
	{
		if($userID != null && filter_var($userID, FILTER_VALIDATE_INT))
		{
			$this->db->select('UserID');
			$this->db->from('Users');
			$this->db->where('UserID', $this->userID);
			
			$results = $this->db->get();
			
//			if($this->db->num_rows() == 0)
                        if($results->num_rows() == 0)
			{
				$this->userID = $userID;
				return true;
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
		$this->passwordSalt = md5(time() * rand());
		
        $this->passwordHash = hash('sha512', $this->passwordSalt . $password);
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
     * Summary of getPasswordSalt
     * Get the password hash salt string associated with this user model
     * 
     * @return string The salt of the password hash associated with this user model or null if model not saved in database
     */
    public function getPasswordSalt()
    {
        return $this->passwordSalt;
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
		$models = array();
		
		$this->db->select('CurriculumID');
		$this->db->from('UserCurriculums');
		$this->db->where('UserID', $this->userID);
		
		$results = $this->db->get();
		
		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				$model = new Curriculum_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['CurriculumID']))
					array_push($models, $model);
			}
		}
		
		return $models;
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
     * Summary of setLastLogin
     * Set the last login time of the user
     * 
     * @param integer $lastLogin The last login time associated with this user model
     */
	public function setLastLogin($lastLogin)
	{
		$this->lastLogin = filter_var($lastLogin, FILTER_SANITIZE_NUMBER_INT);
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
		if($curriculum != null && $curriculum->getCurriculumID() != null)
		{
                        echo 'this uID: '.$this->getUserID();
                        echo 'this cID: '.$curriculum->getCurriculumID();
			$data = array(
				"UserID" => $this->getUserID(),
				"CurriculumID" => $curriculum->getCurriculumID()
			);
			
			$this->db->insert('UserCurriculums', $data);
			
			return $this->db->affected_rows() > 0;
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
		if($curriculum != null && $curriculum->getCurriculumID() != null)
		{
			$this->db->where('UserID', $this->userID);
			$this->db->where('CurriculumID', $curriculum->getCurriculumID());
			
			$this->db->delete('UserCurriculums');
			
			return $this->db->affected_rows() > 0;
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
     * Summary of getLastLogin
     * Get the last login time of the user
     * 
     * @return integer The last login time associated with this user model or null if model not saved in database
     */
	public function getLastLogin()
	{
		return $this->lastLogin;
	}
	
	/**
     * Summary of isGuest
     * Check whether this user has the role of a guest
     * 
     * @return boolean True is the user has a guest role, false otherwise
     */
	public function isGuest()
	{
		$this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', self::ROLE_GUEST);
		
		$results = $this->db->get('UserRoles');
		
		return $results->num_rows() > 0;
	}
	
    /**
     * Summary of isStudent
     * Check whether this user has the role of a student
     * 
     * @return boolean True is the user has a student role, false otherwise
     */
    public function isStudent()
    {
        $this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', self::ROLE_STUDENT);
		
		$results = $this->db->get('UserRoles');
		
		return $results->num_rows() > 0;
    }
    
    /**
     * Summary of isAdmin
     * Check whether this user has the role of a administrator
     * 
     * @return boolean True is the user has an administrator role, false otherwise
     */
    public function isAdmin()
    {
        $this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', self::ROLE_ADMIN);
		
		$results = $this->db->get('UserRoles');
		
		return $results->num_rows() > 0;
    }
    
    /**
     * Summary of isProgramChair
     * Check whether this user has the role of program chair
     * 
     * @return boolean True is the user has a program chair role, false otherwise
     */
    public function isProgramChair()
    {
        $this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', self::ROLE_PROGRAM_CHAIR);
		
		$results = $this->db->get('UserRoles');
		
		return $results->num_rows() > 0;
    }
    
    /**
     * Summary of isAdvisor
     * Check whether this user has the role of a advisor
     * 
     * @return boolean True is the user has a advisor role, false otherwise
     */
    public function isAdvisor()
    {
        $this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', self::ROLE_ADVISOR);
		
		$results = $this->db->get('UserRoles');
		
		return $results->num_rows() > 0;
    }
    
    /**
     * Summary of addRole
     * Adds a role to the user model if the role isn't already enabled
     * 
     * @param int $roleType The role to add to the user (see ROLE constants)
     */
    public function addRole($roleType)
    {
		$this->db->select('RoleID');
		$this->db->from('UserRoles');
		$this->db->where('UserID', $this->userID);
		
		$results = $this->db->get();
		
		$arr = array();
		
		foreach($results->result_array() as $row)
		{
			array_push($arr, $row['RoleID']);
		}
		
		if(!in_array($roleType, $arr))
		{
			$data = array(
				"UserID" => $this->userID,
				"RoleID" => $roleType
			);
			
			$this->db->insert('UserRoles', $data);
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
        $this->db->where('UserID', $this->userID);
		$this->db->where('RoleID', $roleType);
		
		$this->db->delete('UserRoles');
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
     * @param string $grade The grade this student got for the course section (0 = F, 4 = A)
     * @return boolean True if the course section was successfully added, false otherwise
     */
    public function addCourseSection($courseSection, $grade)
    {
        $data = array(
			"StudentUserID" => $this->userID,
			"CourseSectionID" => $courseSection->getCourseSectionID(),
			"Grade" => $grade
		);
		
		$this->db->insert("StudentCourseSections", $data);
		
		return $this->db->affected_rows() > 0;
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
        $this->db->where('CourseSectionID', $courseSection->getCourseSectionID());
		$this->db->where('StudentUserID', $this->userID);
		
		$this->db->delete('StudentCourseSections');
		
		return $this->db->affected_rows() > 0;
    }
    
    /**
     * Summary of getAllCoursesTaken
     * Get all of the courses that have been taken by this user model
     * 
     * @return Array An array containing all of the course sections taken by this user model
     */
    public function getAllCoursesTaken()
    {
		$models = array();
		
		$this->db->select('CourseSectionID, Grade');
		$this->db->from('StudentCourseSections');
        $this->db->where('StudentUserID', $this->userID);
		
		$results = $this->db->get();
		
		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				$courseSection = new Course_section_model;
				
				if($courseSection->loadPropertiesFromPrimaryKey($row['CourseSectionID']))
				{
					array_push($models, array($courseSection, $row['Grade']));
				}
			}
		}
		
		return $models;
    }
	
	/**
	 * Summary of getGradeForCourseSection
	 * Get the grade the student user model got for a particular course section
	 *
	 * @param Course_section_model The course section model to look up a grade for
	 * @return string Returns the grade a student got for that course section or false if no grade was found
	 */
	public function getGradeForCourseSection($courseSection)
	{
		$this->db->select('Grade');
		$this->db->from('StudentCourseSections');
		$this->db->where('StudentUserID', $this->userID);
		$this->db->where('CourseSectionID', $courseSection->getCourseSectionID());
		
		$results = $this->db->get();
		
		if($results->num_rows() > 0)
		{
			return $results->row_array()["Grade"];
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Summary of getAllTransferCourses
	 * Get all of the student transfer course models associated with this user
	 *
	 * @return Array An array containing all the student transfer course models associated with this user model
	 */
	public function getAllTransferCourses()
	{
		$models = array();
		
		if($this->userID != null)
		{
			$this->db->select('StudentTransferCourseID');
			$this->db->from('StudentTransferCourses');
			$this->db->where('StudentUserID');
			
			$results = $this->db->get();
			
			if($results->num_rows() > 0)
			{
				foreach($results->result_array() as $row)
				{
					$model = new Student_transfer_course_model;
					
					if($model->loadPropertiesFromPrimaryKey($row['StudentTransferCourseID']))
					{
						array_push($models, $model);
					}
				}
			}
		}
		
		return $models;
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
            $data = array(
				'EmailAddress' => $this->emailAddress, 
				'PasswordHash' => $this->passwordSalt . "$" . $this->passwordHash, 
				'Name' => $this->name, 
				'LastLogin' => $this->lastLogin,
				'UserStateID' => $this->userStateID
			);
            
            $this->db->where('UserID', $this->userID);
            $this->db->update('Users', $data);
            
            return $this->db->affected_rows() > 0;
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
			if($this->userID != null)
			{
				$data = array(
					'UserID' => $this->userID,
					'EmailAddress' => $this->emailAddress, 
					'PasswordHash' => $this->passwordSalt . "$" . $this->passwordHash, 
					'Name' => $this->name, 
					'LastLogin' => $this->lastLogin,
					'UserStateID' => $this->userStateID
				);
			}	
			else
			{
				$data = array(
					'EmailAddress' => $this->emailAddress, 
					'PasswordHash' => $this->passwordSalt . "$" . $this->passwordHash, 
					'Name' => $this->name, 
					'LastLogin' => $this->lastLogin,
					'UserStateID' => $this->userStateID
				);
			}
            
            $this->db->insert('Users', $data);
            
			$this->userID = $this->db->insert_id();
			
            return $this->db->affected_rows() > 0;
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
        $hashedPasswordGuess = hash('sha512', $this->passwordSalt . $passwordGuess);
        
        $len = strlen($hashedPasswordGuess);
        
        $finalFlag = true;
		
		$len2 = strlen($this->passwordHash);
		
		if($len < 1 || $len2 != $len)
		{
			$finalFlag = false;
		}
		
        for($i=0;$i<$len;$i++)
        {
            if ($finalFlag && $hashedPasswordGuess[$i] != $this->passwordHash[$i])
            {
                $finalFlag = false;
            }
        }
        
        return $finalFlag;
    }
	
	/**
	 * Summary of getAllUsers
	 * Get all of the users in the database
	 *
	 * @param int $limit The maximum number of users to return
	 * @param int $offset The starting index to begin finding users
	 * @return Array An array containing all users
	 */
	public static function getAllUsers($limit = 0, $offset = 0)
	{
		$db = get_instance()->db;
		
		$models = array();
		
		$db->select('UserID');
		
		if($offset > 0 && $limit > 0)
		{
			$results = $db->get('Users', $limit, $offset);
		}
		else if($limit > 0)
		{
			$results = $db->get('Users', $limit);
		}
		else
		{
			$results = $db->get('Users');
		}
		
		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				$model = new User_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['UserID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getAllAdvisors
	 * Get all of the users in the database with an advising role
	 *
	 * @return Array An array containing all users who have an advisor role
	 */
	public static function getAllAdvisors()
	{
		$db = get_instance()->db;
		
		$models = array();
		
		$db->select('Users.UserID');
		$db->from('Users');
		$db->join('UserRoles', 'Users.UserID = UserRoles.UserID', 'inner');
		$db->where('UserRoles.RoleID', self::ROLE_ADVISOR);
		
		$results = $db->get();
		
		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				$model = new User_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['UserID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
	
	/**
	 * Summary of getAllProgramChairs
	 * Get all of the users in the database with a program chair role
	 *
	 * @return Array An array containing all users who have a program chair role
	 */
	public static function getAllProgramChairs()
	{
		$db = get_instance()->db;
		
		$models = array();
		
		$db->select('Users.UserID');
		$db->from('Users');
		$db->join('UserRoles', 'Users.UserID = UserRoles.UserID', 'inner');
		$db->where('UserRoles.RoleID', self::ROLE_PROGRAM_CHAIR);
		
		$results = $db->get();
		
		if($results->num_rows() > 0)
		{
			foreach($results->result_array() as $row)
			{
				$model = new User_model;
				
				if($model->loadPropertiesFromPrimaryKey($row['UserID']))
				{
					array_push($models, $model);
				}
			}
		}
		
		return $models;
	}
}

