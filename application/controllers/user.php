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

    public function initCreateUser() {
        $this->load->view('create_new_user1');
    }

    public function initUser() {
        $activeUser = new User_model();
        $userName = $this->input->post('userName');
        $emailAddress = $this->input->post('emailAddress');
        $password = $this->input->post('password');

        $activeUser->setName($userName);
        $activeUser->setEmailAddress($emailAddress);
        $activeUser->setPassword($password);

        for ($role = 1; $role <= 4; $role = $role + 1) {
            $hasRole = $this->input->post($role);
            if ($hasRole) {
                $activeUser->addRole($role);
            }
        }

        if (!$activeUser->isStudent()) {
            if ($activeUser->create()) {
                $this->load->view('confirm_user_added');
            } else {
                $this->load->view('create_new_user1');
            }
        }
//        $allCurriculums = $this->Curriculum_model->getAllCurriculums();
        //$allCourses = $this->Course_model->getAllCourses();

        $this->load->view('create_new_user2'); //, $allCurriculums);
    }

    public function prepareRemoveUser() {
        $this->load->view('prepare_remove_user');
    }

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

    public function removeUser() {
        $userID = $this->input->post('userID');
        $remUser = new User_model();
        $remUser->loadPropertiesFromPrimaryKey($userID);

        if ($remUser) {
            $remUser->delete();
        }
    }

    public function addUserCourses() {
        
    }

    public function prepareEditUser() {
        $this->load->view('prepare_edit_user');
    }

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
