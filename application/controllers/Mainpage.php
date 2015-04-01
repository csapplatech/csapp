<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mainpage extends CI_Controller
{
    public function index()
    {
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
        $user->create();
        
        //Load the appropriate mainpage
        if ($user->isStudent())
            $this->load->view('MainPages/student_main_page', array('user'=>$user));
        elseif ($user->isProgramChair())
            $this->load->view('MainPages/pc_main_page', array('user'=>$user));
        elseif ($user->isAdvisor())
            $this->load->view('MainPages/advisor_main_page', array('user'=>$user));
        elseif ($user->isAdmin())
            $this->load->view('MainPages/admin_main_page', array('user'=>$user));
        else
            $this->load->view('MainPages/guest_main_page', array('user'=>$user));
    }
    
    public function student()
    {
        //Load the student mainpage if user is a student
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
        $user->create();
        if ($user->isStudent())
            $this->load->view('MainPages/student_main_page', array('user'=>$user));
        else
            index();
    }
    
    public function advisor()
    {
        //Load the advisor mainpage if user is an advisor
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
        $user->create();
        if ($user->isAdvisor())
            $this->load->view('MainPages/advisor_main_page', array('user'=>$user));
        else
            index();
    }
    
    public function programChair()
    {
        //Load the program chair mainpage if user is a program chair
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
        $user->create();
        if ($user->isProgramChair())
            $this->load->view('MainPages/pc_main_page', array('user'=>$user));
        else
            index();
    }
    
    public function admin()
    {
        //Load the admin mainpage if user is a admin
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
        $user->create();
        if ($user->isAdmin())
            $this->load->view('MainPages/admin_main_page', array('user'=>$user));
        else
            index();
    }
    
    public function guest()
    {
        //Load the guest mainpage
        $this->load->view('MainPages/guest_main_page', array('user'=>$user));
    }
}

