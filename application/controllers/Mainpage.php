<?php
class Mainpage extends CI_Controller
{
    public function index()
    {
        $user = new User_model;
        $user->loadPropertiesFromPrimaryKey($username);
        $user->create();
        $data = array(
                'name' => $user->getName(),
                'id' => $user->getUserID(),
                'is_logged_in' => TRUE,
                'isStudent' => $user->isStudent(),
                'isAdmin' => $user->isAdmin(),
                'isProgramChair' => $user->isProgramChair(),
                'isAdvisor' => $user->isAdvisor(),
                );
        $this->load->view('main_page', $data);
    }
}

