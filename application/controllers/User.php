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
    public function index($action = NULL, $userID = NULL) {
        $this->checkSec(); //Performs security check

        $data = array(
            'allUsers' => $this->User_model->getAllUsers(20000, 0)
        );

        $validActions = array('create', 'modify', 'remove');
        //Should just load Management view if no valid action is selected.
        if (!in_array($action, $validActions)) {
            return $this->load->view('user_mgmt_list', $data);
        }
        //Establish action to perform.
        echo 'setting action: ' . $action;
        $_SESSION['action'] = $action;
        //Action is modify or create with null ID
        if ($action != 'create' && $userID == NULL) {
//            $_SESSION['userActionMsg'] = '';
            return $this->load->view('user_mgmt_list', $data);
        }
        //At this point we have a valid action saved in the session,
        //and if the action is modify/remove there is some userID provided.
        // Need to see if userID can be related to an actual users.
        $user = new User_model;
        $userExists = $user->loadPropertiesFromPrimaryKey($userID);
        //Error attempting to create with an existing user.
        if ($action == 'create' && $userExists) {
            $_SESSION['userActionMsg'] = 'Cannot create user with existing ID.';
            $this->load->view('user_mgmt_list', $data);
        } else if ($action != 'create' && !$userExists) {
            $_SESSION['userActionMsg'] = 'Cannot modify/remove user who does not exist.';
            $this->load->view('user_mgmt_list', $data);
        } else {
            //At this point there should only be 3 possible cases:
            // 1 $action == create and the user does not exist.
            // 2-3 $action == modify or remove and the user exists.
            $userData = $this->loadUserData($userID);
            $userData['user'] = $user;
            $this->load->view('user_form', $userData);
        }
    }

    public function submitUserListQuery() {
        $searchStr = $this->input->post('searchStr');
        $filteredList = array();
        $unfilteredList = $this->User_model->getAllUsers();
        foreach ($unfilteredList as $listUser) {
            if (substr_count($listUser->getName(), $searchStr) > 0) {
                array_push($filteredList, $listUser);
            }
        }
        $data['allUsers'] = $filteredList;
        return $this->load->view('user_mgmt_list', $data);
    }

    public function submitCourseListQuery() {
        $searchStr = $this->input->post('searchStr');
        $sID = $this->input->post('studentID');
        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);
        $studentCurriculumList = $student->getCurriculums();
        $studentData = array(
            'sID' => $sID,
            'courseData' => array()
        );

        $filteredList = array();
        $unfilteredList = array();
        foreach ($studentCurriculumList as $curriculum) {
            $unfilteredList = array_merge($unfilteredList, $this->collectCourseData($curriculum->getCurriculumID()));
        }
        foreach ($unfilteredList as $listCourse) {
            if (strpbrk($listCourse['courseName'], $searchStr)) {
                array_push($studentData['courseData'], $listCourse);
            }
        }
        return $this->load->view('student_courses_form', $studentData);
    }

    //check for valid user session before performing actions.
    private function checkSec() {
        //todo change this to false to enable security.
        $authorized = false;
        if (isset($_SESSION['UserID'])) {
            $userID = $_SESSION['UserID'];
            $loggedInUser = new User_model;
            if ($loggedInUser->loadPropertiesFromPrimaryKey($userID)) {
                if ($loggedInUser->isAdmin() || $loggedInUser->isProgramChair()) {
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
            $userData['user'] = $user;
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
        if ($data['uID'] != 0) {
            $user->setUserID($data['uID']);
        }
        $user->setEmailAddress($data['email']);
        $user->setName($data['lName'] . ',' . $data['fName']);
        //todo Ensure there is a password and a name here.
        if ($data['pass'] == $data['confPass']) {
            $user->setPassword($data['pass']);
        }

        $user->setState(1);
        $user->setLastLogin(0);
        $isCreated = $user->create();
        if (!$isCreated && $data['uID'] != 0) {
            redirect('User/index/modify/' . $user->getUserID());
        }
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
        $this->load->view('select_user_form', array('user' => $user));
    }

    /**
     * 
     * @param int $uID ID of user to be modified, if this is null a new user should be created.
     */
    public function submitUserForm($uID = NULL) {
        $user = new User_model;

        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isAdmin())
            redirect('Login/logout');

        $userData = array();
        $userData['uID'] = $this->input->post('userID');
        $userData['email'] = $this->input->post('email');
        $userData['fName'] = $this->input->post('fName');
        $userData['mName'] = $this->input->post('mName');
        $userData['lName'] = $this->input->post('lName');
        $userData['pass'] = $this->input->post('pass');
        $userData['confPass'] = $this->input->post('confPass');
        $userData['user'] = $user;

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
            $userData['studentCurriculums'] = array();
            $this->load->view('student_info_form', $userData);
        } else {
            redirect('Mainpage/index');
        }
    }

    public function submitStudentInfoForm($studentID) {

        $allCurriculums = $this->Curriculum_model->getAllCurriculums();

        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($studentID);

        foreach ($allCurriculums as $curriculum) {
            $cID = $curriculum->getCurriculumID();
            if ($this->input->post('Curriculum' . $cID)) {
                if (!in_array($curriculum, $student->getCurriculums())) {
                    $student->addCurriculum($curriculum);
                }
            } else {
                $student->removeCurriculum($curriculum);
            }
        }
        $advisorID = $this->input->post('advisorID');

        $advisor = new User_model();
        $advisor->loadPropertiesFromPrimaryKey($advisorID);

        $curriculum = new Curriculum_model;
        $curriculum->loadPropertiesFromPrimaryKey($curriculumID);

        $student->setAdvisor($advisor);

        $student->update();

        redirect('User/prepareAddCourses/' . $studentID);
    }

    public function prepareAddCourses($sID) {
        $this->checkSec();
        $studentData = array(
            'sID' => $sID,
            'curriculumSlots' => NULL,
            'filledSlots' => array()
        );
        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);
        $curriculums = $student->getCurriculums();
        $allCurriculumSlots = array();
        foreach ($curriculums as $curriculum) {
            $cSlots = $curriculum->getCurriculumCourseSlots();
            $allCurriculumSlots = array_merge($allCurriculumSlots, $cSlots);
            
        }
        $studentData['curriculumSlots'] = $allCurriculumSlots;
        $sectionsTaken = $student->getAllCoursesTaken();
        $coursesTaken = array();
        foreach ($sectionsTaken as $section) {
            array_push($coursesTaken, $section[0]->getCourse());
        }
        $selectedCourses = $this->getSelectedCourses($coursesTaken, $allCurriculumSlots);
        $filledSlots = array();
        foreach ($selectedCourses as $selCourse) {
            $course = $selCourse[0];
            $slotName = $selCourse[1];
            $filledSlots[$slotName] = $this->getSectionForCourse($course);
        }
        $studentData['filledSlots'] = $filledSlots;
        
        $this->load->view('student_courses_form', $studentData);
    }

    private function getSelectedCourses($courses, $slots) {
        $selectedCourses = array();
        foreach ($slots as $slot) {
            $course = $this->getSelectedCourse($courses, $slot);
            if ($course) {
                $name = $slot->getName();
                array_push($selectedCourses, array($course, $name));
            }
        }
        return $selectedCourses;
    }

    private function getSelectedCourse($courses, $slot) {
        foreach ($courses as $course) {
            if (in_array($slot, $course->getAllCurriculumCourseSlots())) {
                return $course;
            }
        }
        return false;
    }

    private function getSectionForCourse($course) {
        $sections = $this->Course_section_model->getAllCourseSections();
        foreach ($sections as $section) {
            if ($section->getCourse() == $course) {
                return $section->getCourseSectionID();
            }
        }
        return false;
    }

    public function prepareAddCourseSection($slotID) {
        $sID = $this->input->post('sID');
        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);

        $currSlot = new Curriculum_course_slot_model();
        $currSlot->loadPropertiesFromPrimaryKey($slotID);
        $name = $currSlot->getName();

        $data = array(
            'sID' => $sID,
            'action' => 'add',
            'slotName' => $name,
            'sections' => array()
        );

        $allSections = $this->Course_section_model->getAllCourseSections();
        $validSections = array();
        foreach ($allSections as $section) {
            $course = $section->getCourse();
            $courseName = $course->getCourseName();
            $courseNumber = $course->getCourseNumber();
            if ($name == $courseName . ' ' . $courseNumber) {
                array_push($validSections, $section);
            }
        }
        $data['sections'] = $validSections;
        $this->load->view('course_section_form', $data);
    }

    public function prepareRemoveCourseSection($sectionID) {
        $sID = $this->input->post('sID');
        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);
        $section = new Course_section_model;
        $section->loadPropertiesFromPrimaryKey($sectionID);
        $grade = $student->getGradeForCourseSection($section);
        $data = array(
            'sID' => $sID,
            'action' => 'remove',
            'section' => $section,
            'quarter' => $section->getAcademicQuarter(),
            'grade' => $grade
        );

        $this->load->view('course_section_form', $data);
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

    public function removeCourseSection() {
        $this->checkSec();
        $sID = $this->input->post('sID');

        $student = new User_model;
        $student->loadPropertiesFromPrimaryKey($sID);
        $section = new Course_section_model;
        $section->loadPropertiesFromPrimaryKey($this->input->post('sectionID'));

        $student->removeCourseSection($section);

        redirect('User/prepareAddCourses/' . $sID);
    }

//    //method to add courses to new user
    public function addCourseSection() {
        $this->checkSec();
        $sID = $this->input->post('sID');
        $sectionID = $this->input->post('sectionID');
        $grade = $this->input->post('grade');
        if(!isset($grade)) {
            redirect('User/prepareAddCourses/'.$sID);
        }
        
        $student = new User_model();
        $student->loadPropertiesFromPrimaryKey($sID);
        $section = new Course_section_model;
        $section->loadPropertiesFromPrimaryKey($sectionID);
        $student->addCourseSection($section, $grade);
        
        redirect('User/prepareAddCourses/'.$sID);
    }

}
