<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ViewAdvisorInfo extends CI_Controller
{
    public function index()
    {
        if (!isset($_SESSION['UserID']))
        {   redirect('Login/logout');}
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
        {   redirect('Login/logout');} //If user did not load, logout the session
        if (!$user->isStudent())
        {   redirect('Mainpage');} //If not a student, redirect to mainpage
        $advisor = $user->getAdvisor();
        $advisorName = $advisor->getName();
        $data = array('user'=>$user,'advisor'=>$advisor);
        $this->load->view('advisorInfo', $data);
    }
}