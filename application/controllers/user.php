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
        $userID = $this->input->post('userID');
        $userExists = $this->User_model->loadPropertiesFromPrimaryKey($userID);
        if ($userExists) {
            $this->load->view('confirm_remove_user');
        } else {
            $this->load->view('prepare_remove_user');
        }
    }

    public function removeUser($userID) {
        echo $userID;
    }

    public function addUserCourses() {
        
    }

}
