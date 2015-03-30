<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends CI_Controller {

//    public function __construct() {
//        parent::__construct();
//    }
    //First View of Creating a User
    public function initCreateUser() {
        $this->load->view('create_new_user1');
    }

    //Grabs data from First view and combines it with rest of User attributes
    //to complete user creation
    public function initUser() {
         $data = array(
            'userID' => '0'
        );
        //creating variables to hold the data from view 1
        $activeUser = new User_model();
        $userFName = $this->input->post('userFName');
        $userLName = $this->input->post('userLName');
        $emailAddress = $this->input->post('emailAddress');
        $password = $this->input->post('password');

        $activeUser->setName($userLName . ',' . $userFName);
        $activeUser->setEmailAddress($emailAddress);
        $activeUser->setPassword($password);

        //adds selected roles to user being created
        for ($role = 1; $role <= 4; $role = $role + 1) {
            $hasRole = $this->input->post($role);
            if ($hasRole) {
                $activeUser->addRole($role);
            }
        }
        //if user being created is not a student the creation process ends here
        //otherwise...
        if(!$activeUser->create()) {
             $this->load->view('create_new_user1');
             return;
        }

        if ($activeUser->isStudent()) {
            $data['userID'] = $activeUser->getUserID();
            echo 'data->userID: '.$data['userID'];
            $this->load->view('create_new_user2', $data);
        } else {
            $this->load->view('confirm_user_added');
        }
        //$allCurriculums = $this->Curriculum_model->getAllCurriculums();
        //$allCourses = $this->Course_model->getAllCourses();
    }

    //method to add courses to new user
    public function addUserCourses() {
        $studentID = $this->input->post('userID');
        echo 'studentID: '.$studentID;
        $selectedCurriculumID = $this->input->post('curriculum');
        $selectedCurriculum = new Curriculum_model();
        $selectedCurriculum = loadPropertiesFromPrimaryKey($selectedCurriculumID);
        $selectedCurriculumCSlots = new Curriculum_course_slot_model();
        $selectedCurriculumCslots = $selectedCurriculum->getCurriculumCourseSlots();
        foreach($sectionSlot as $selectedCurriculumCslots){
            
            $courseNames[] = $sectionSlot->strtoupper(getName());
        }
        //get all courses compare (to-upper)"courseNames" to getCourseName().getCourseNumber()
        //for all matches get CourseIDs
        //
        //from the CourseIDs we can use the Course_section_model function get
        $this->load->view('new_student_courses');
        //use curriculumID and getCurriculumCourseSlots()
        //use curriculumCourseSlot model function getName() to populate name list
        //look at courses(model) in data base for same names to getCourseID()
        //use courseID's and courseSection model function getAllSections() /w 
        //matching courseID's and pull scourseSectionID's
    }

    //loads view to select user to be removed
    public function prepareRemoveUser() {
        $this->load->view('prepare_remove_user');
    }

    //method to confirm user removal
    public function confirmRemoveUser() {
        $data = array(
            'userID' => '0'
        );

        $data['userID'] = $this->input->post('userID');
        $userExists = $this->User_model->loadPropertiesFromPrimaryKey($data['userID']);
        if ($userExists) {
            echo 'A user with userID: ' . $data['userID'];
            $this->load->view('confirm_remove_user', $data);
        } else {
            $this->load->view('prepare_remove_user');
        }
    }

    //method to remove the selected user after confirmation
    public function removeUser() {
        $userID = $this->input->post('userID');
        $remUser = new User_model();
        $remUser->loadPropertiesFromPrimaryKey($userID);

        if ($remUser) {
            $remUser->delete();
        }
    }

    //method to load the view to select a user for editing
    public function prepareEditUser() {
        $this->load->view('prepare_edit_user');
    }

    //method to edit the selected user with user creation views
    public function editUser() {
        //this loads the userID given by 'prepare_edit_user'
        $userID = $this->input->post('userID');
        //the next 2 lines create the editable user data model and fill it with the information associated with the
        //given userID
        $edUser = new User_model();
        $edUser->loadPropertiesFromPrimaryKey($userID);
        //
        $data = array(
            'emailAddress' => null,
            'password' => null,
            'name' => null,
            'role1' => null,
            'role2' => null,
            'role3' => null,
            'role4' => null
        );
        //fill data with edUser's attributes
        $data['emailAddress'] = $edUser->getEmailAddress();
        $data['name'] = $edUser->getName();
        $data['role1'] = $edUser->isAdmin();
        $data['role2'] = $edUser->isProgramChair();
        $data['role3'] = $edUser->isAdvisor();
        $data['role4'] = $edUser->isStudent();
        //if the user exists load the edit_user1 page
        if ($edUser) {

            $this->load->view('edit_user1', $data);
            //otherwise reload the id request page    
        } else {
            $this->load->view('prepare_edit_user');
            //$this->load->view('prepare_edit_user_e'); _e for error page
        }
    }

}
