<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends CI_Controller {

    /**
     * Decides which action should be performed and loads
     * the appropriate form view for each action. 
     * Only {create, modify, remove} are valid actions.
     * 
     * @param type $action The action to be performed.
     * @param int  $userID The ID of user that will be acted upon.
     */
    public function index($action = 'create', $userID = NULL) {
        $this->checkSec();
        $validActions = array('create', 'modify', 'remove');
        if (!in_array($action, $validActions)) {
            show_error('Invalid Action Selected');
        }
        $_SESSION['action'] = $action;

        if (!isset($userID) && $action != 'create') {
            $this->load->view('select_user_form');
        } else {
            $userData = $this->loadUserData($userID);
            $this->load->view('user_form', $userData);
        }
    }

    //check for valid user session before performing actions.
    private function checkSec() {
        //todo change this to false to enable security.
        $authorized = false;
        if (isset($_SESSION['UserID'])) {
            $userID = $_SESSION['UserID'];
            $loggedInUser = new User_model;
            if ($loggedInUser->loadPropertiesFromPrimaryKey($userID)) {
                if ($loggedInUser->isAdmin()) {
                    $authorized = true;
                }
            }
        }
        if (!$authorized) {
            redirect('Login/logout');
        }
    }

    /**
     * @param type $uID Identifies which user data to load.
     * @return Returns Filled userData if user is loaded successfully.
     */
    private function loadUserData($uID) {

        $userData = array(
            'uID' => $uID,
            'email' => NULL,
            'fName' => NULL,
            'mName' => NULL,
            'lName' => NULL,
            'roles' => array(NULL, False, False, False, False)
        );

        $user = new User_model;
        $isLoaded = $user->loadPropertiesFromPrimaryKey($uID);

        if ($isLoaded) {
            $userData['email'] = $user->getEmailAddress();
            $userData = $this->loadUserName($userData, $user->getName());
            $userData['roles'] = $this->loadUserRoles($user);
        }
        return $userData;
    }

    private function loadUserName($data, $name) {
        $delim = ',';
        if (!strstr($name, $delim)) {
            $delim = ' ';
        }

        $uName = explode($delim, $name, 3);
        $data['lName'] = $uName[0];
        $data['fName'] = $uName[1];
//        if (strstr($fmName, ' ') || strstr($fmName, ',')) {
//            $delim = ' ';
//            $uName = explode($delim, $name, 2);
//            $data['fName'] = $uName[0];
//            $userData['mName'] = $uName[1];
//        } else {
//            $data['fName'] = $uName[1];
//        } 

        return $data;
    }

    private function loadUserRoles($user) {
        $roles[1] = $user->isAdmin();
        $roles[2] = $user->isProgramChair();
        $roles[3] = $user->isAdvisor();
        $roles[4] = $user->isStudent();

        return $roles;
    }

    /**
     * 
     * @param type $data
     * @return Bool If user is updated successfully this function will return true.
     */
    private function saveUserData($data) {
        $this->checkSec();
        $user = new User_model;
        $user->loadPropertiesFromPrimaryKey($data['uID']);

        $user->setEmailAddress($data['email']);

        $uName = $data['lName'] . ',' . $data['fName'];
        if (isset($data['mName'])) {
            $uName = $uName . ' ' . $data['mName'];
        }
        $user->setName($uName);
        //todo Ensure Name is set here
        if (isset($data['pass']) && $data['pass'] == $data['confPass']) {
            $user->setPassword($data['pass']);
        }
        $this->addUserRoles($data, $user);
        if ($user->update()) {
            return True;
        }
        return False;
    }

    /**
     * 
     * @param type $data
     * @return type This function will return the id of the newly created use on success
     * and returns false otherwise.
     */
    private function createUserData($data) {
        $this->checkSec();
        $user = new User_model;

        $user->setEmailAddress($data['email']);
        $user->setName($data['lName'] . ',' . $data['fName']);
        //todo Ensure there is a password and a name here.
        if ($data['pass'] == $data['confPass']) {
            $user->setPassword($data['pass']);
        }
		
        $user->setState(1);
        $user->setLastLogin(0);
        $user->create();
		
		$this->addUserRoles($data, $user);
		
        return $user->getUserID();
    }

    private function addUserRoles($userData, $user) {
        $newRoles = $userData['roles'];
        for ($i = 1; $i <= sizeof($newRoles); $i++) {
            if ($newRoles[$i]) {
                $user->addRole($i);
            } else {
                $user->removeRole($i);
            }
        }
    }

    public function submitSelectUserForm() {
        $uID = $this->input->post('userID');
        $action = $_SESSION['action'];
        $user = new User_model;
        if ($user->loadPropertiesFromPrimaryKey($uID)) {
            redirect('User/index/' . $action . '/' . $uID);
        }
        $this->load->view('select_user_form');
    }

    /**
     * 
     * @param int $uID ID of user to be modified, if this is null a new user should be created.
     */
    public function submitUserForm($uID = NULL) {
        $userData = array();
        $userData['uID'] = $uID;
        $userData['email'] = $this->input->post('email');
        $userData['fName'] = $this->input->post('fName');
        $userData['mName'] = $this->input->post('mName');
        $userData['lName'] = $this->input->post('lName');
        $userData['pass'] = $this->input->post('pass');
        $userData['confPass'] = $this->input->post('confPass');

        $roles = array();
        for ($i = 1; $i <= 4; $i++) {
            if ($this->input->post(strval($i)) != NULL) {
                $roles[$i] = true;
            } else {
                $roles[$i] = false;
            }
        }
        $userData['roles'] = $roles;
        switch ($_SESSION['action']) {
            case 'create':
                $userData['uID'] = $this->createUserData($userData);
                break;

            case 'modify':
                $this->saveUserData($userData);
                break;

            case 'remove':
                $this->load->view('confirm_remove_user', $userData);
                return;
        }
        //If the user is selected to have student role
        //assign an advisor and fill courses taken.
        $selectedUser = new User_model;
        $selectedUser->loadPropertiesFromPrimaryKey($userData['uID']);
        if ($selectedUser->isStudent()) {
            $this->load->view('student_info_form', $userData);
        } else {
            redirect('Mainpage/index');
        }
    }

    public function submitStudentInfoForm($studentID) {
        $curriculumID = $this->input->post('curriculumID');
        $advisorID = $this->input->post('advisorID');

        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($studentID);

        $advisor = new User_model();
        $advisor->loadPropertiesFromPrimaryKey($advisorID);

        $curriculum = new Curriculum_model;
        $curriculum->loadPropertiesFromPrimaryKey($curriculumID);

        $student->setAdvisor($advisor);
        $student->addCurriculum($curriculum);
        $student->update();

        redirect('User/prepareAddCourses/' . $studentID);
    }

    public function prepareAddCourses($sID) {
        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);
//        $curriculums = $student->getCurriculums();
        $curriculums = $this->Curriculum_model->getAllCurriculums();
        $studentData = array(
            'sID' => $sID,
            'courseData' => array()
        );
        $allCourseData = array();
        foreach ($curriculums as $curriculum) {
//            $courseData = $this->getCurriculumCourseData($curriculum);
            $courseData = $this->collectCourseData($curriculum->getCurriculumID());
            $allCourseData = array_merge($allCourseData, $courseData);
        }
//        $studentData['courseData'] = array_unique($allCourseData);
        $studentData['courseData'] = $allCourseData;
        $this->load->view('student_courses_form', $studentData);
    }

    //method to remove the selected user after confirmation
    public function removeUser($uID) {
        $this->checkSec();
        $remUser = new User_model();
        $remUser->loadPropertiesFromPrimaryKey($uID);
        $remUser->delete();
        redirect('Mainpage/index');
    }

    private function collectCourseData($curriculumID) {

        #get list of all curriculumcourseslots for names of courses
        $curriculum = new Curriculum_model();
        $curriculum->loadPropertiesFromPrimaryKey($curriculumID);
        $currCourseList = $curriculum->getCurriculumCourseSlots();

        #Courses list to grab courseIDs from courseName and course Number
        $course = new Course_model();
        $courseList = $course->getAllCourses();

        #Create a list of CourseIDs to reference courses
        $courseIDList = array();

        #loop through each currCourseListName and grab the matching courseID's
        #with matching CourseNames and CourseNumbers

        foreach ($currCourseList as $currCourse) {
            $currCourseName = $currCourse->getName();
            $nameLength = strlen($currCourseName);
            $cName = preg_split('%[0-9]%', $currCourseName);
            $cName = strtoupper($cName[0]);
            $cNumber = $currCourseName[$nameLength - 3] . $currCourseName[$nameLength - 2] . $currCourseName[$nameLength - 1];
            foreach ($courseList as $course) {
                if ($course->getCourseName() . ' ' . $course->getCourseNumber() == $cName . $cNumber) {
                    array_push($courseIDList, $course->getCourseID());
                }
            }
        }


        #now that we have a list of all courseIDs we need to make a list of
        #courseSectionIDs
        #first make a list of all courseSections
        $courseSections = new Course_section_model();
        $courseSectionList = $courseSections->getAllCourseSections();

        #next make a list to store the courseSectionIDs we need
        $courseSectionIDList = array();

        foreach ($courseIDList as $courseID) {
            foreach ($courseSectionList as $courseSection) {
                if ($courseSection->getCourse()->getCourseID() == $courseID) {
                    array_push($courseSectionIDList, $courseSection->getCourseSectionID());
                }
            }
        }

        $allCourseData = array();


        foreach ($courseSectionIDList as $csID) {

            $courseData = array(
                'sectionID' => null,
                'courseName' => null,
                'quarterID' => null,
                'quarterName' => null,
                'quarterYear' => null,
                'sectionNum' => null
            );

            $cSection = new Course_section_model();
            $cSection->loadPropertiesFromPrimaryKey($csID);
            $course = $cSection->getCourse();
            $cName = $course->getCourseName();
            $cNumber = $course->getCourseNumber();

            $courseData['quarterID'] = $cSection->getAcademicQuarter()->getAcademicQuarterID();
            $courseData['quarterName'] = $cSection->getAcademicQuarter()->getName();
            $courseData['quarterYear'] = $cSection->getAcademicQuarter()->getYear();
            $courseData['sectionNum'] = $cSection->getSectionName();
            $courseData['sectionID'] = $csID;
            $courseData['courseName'] = $cName . $cNumber;

            array_push($allCourseData, $courseData);
        }
        return $allCourseData;
    }

//    //method to add courses to new user
    public function addCourseSections() {
        $this->checkSec();
        $selectStudent = new User_model();
        $sID = $this->input->post('studentID');
        $selectStudent->loadPropertiesFromPrimaryKey($sID);
//        $curriculumID = $this->input->post('curriculumID');
        $allCourseData = $this->Course_section_model->getAllCourseSections();

        foreach ($allCourseData as $courseData) {
//            echo 'boxName: '.$courseData['sectionID'].'</br>';


            $csID = $courseData->getCourseSectionID();
            $courseSection = new Course_section_model();
            $courseSection->loadPropertiesFromPrimaryKey($csID);


            $isSelected = $this->input->post($csID);
            $grade = $this->input->post($csID . 'grade');

            if (!isset($isSelected)) {
//                if ($selectStudent->getGradeForCourseSection($courseSection)) {
//                    echo 'removing course with sectionID:' . $courseSection->getCourseSectionID() . '</br>';
                $selectStudent->removeCourseSection($courseSection);
//                }
            } else {
                $selectStudent->removeCourseSection($courseSection);
//                echo 'adding course with sectionID:' . $courseSection->getCourseSectionID() . ' with grade ' . $grade . '</br>';
                $selectStudent->addCourseSection($courseSection, $grade);
                //if($selectStudent->addCourseSection($courseSection, $grade)){echo 'success!!</br>';}
                //else{echo 'faildit.</br>';}
            }
        }
        $selectStudent->update();
        redirect('Mainpage/index');
    }

}
