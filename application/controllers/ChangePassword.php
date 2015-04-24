<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChangePassword extends CI_Controller
{
    public function index()
    {
        if (!isset($_SESSION['UserID']))
        {   redirect('Login/logout');}
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
        {   redirect('Login/logout');} //If user did not load, logout the session
        if ($user->isGuest())
        {   redirect('Mainpage');} //If not a student, redirect to mainpage
        $this->load->view('changePassword', array('user'=>$user));
    }
    
    public function change()
    {
        if (!isset($_SESSION['UserID']))
        {   redirect('Login/logout');}
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
        {   redirect('Login/logout');} //If user did not load, logout the session
        if ($user->isGuest())
        {   redirect('Mainpage');} //If not a student, redirect to mainpage
        $oldpw = $this->input->post('oldpw');
        $newpw = $this->input->post('newpw');
        $newpw2 = $this->input->post('newpw2');
        if(!$user->authenticate($oldpw))
        {
            $this->load->view('changePassword', array('user'=>$user, 'error'=>TRUE));
        }
        elseif($newpw != $newpw2)
        {
            $this->load->view('changePassword', array('user'=>$user, 'error2'=>TRUE));
        }
        elseif((strpbrk ($newpw, '!@#$%&*-+=1234567890')===FALSE)||(strlen($newpw)<8))
        {
            $this->load->view('changePassword', array('user'=>$user, 'error3'=>TRUE));
        }
        elseif((strpbrk ($newpw, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')===FALSE)||(strlen($newpw)<8))
        {
            $this->load->view('changePassword', array('user'=>$user, 'error3'=>TRUE));
        }
        else
        {
            $user->setPassword($newpw);
            $user->update();
            $this->load->view('changePassword', array('user'=>$user, 'success'=>TRUE));
        }
    }
}