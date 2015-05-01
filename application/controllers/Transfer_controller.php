<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_controller extends CI_Controller
{
    public function index()
    {
        /*$this->load->model('Student_transfer_course_model');
        $this->load->model('User_model');
        $courses = new Course_model();
        $tcourse = new Student_transfer_course_model();
        $data = array('name', 'id');
        //get all available courses and pass to data
        $availableCourses = $courses->getAllCourses();

        foreach ($availableCourses as $course)
        {
            $arr=[
                'name'=>$course->getCourseName(),
                'id'=>$course->getCourseID()
            ];
             array_push($data, $arr);
        } */       

        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');

        $this->load->model('User_model');
        $students = User_model::getAllStudents();

        $this->load->view('transfer_credit_student_list', array('students'=>$students, 'user'=>$user));
    }

    public function viewStudentIds()
    { 
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');
        $this->load->view('transfer_credit_student_list');
    }

    public function editIdMapping()
    {
         if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');

        
        $this->load->view('edit_transfer_map');
    }

    public function update()
    {

    }

    public function viewIdMapping()
    {
        if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');

        $t_user = new User_model();
        if (!$t_user->loadPropertiesFromPrimaryKey($this->uri->segment(3)))
        {
            redirect('Transfer_controller/index');
        }

        $t_courses = $t_user->getAllTransferCourses();

        var_dump($t_courses);

        $this->load->view('transfer_credit_map', array('user' =>$user , 't_user' =>$t_user, 't_courses' => $t_courses));
    }

    public function addTransferCredit()
    {
         if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');

        $tcourse  = $this->input->post("transferCourseID");
        $this->load->model('Course_model');
        $courses = Course_model::getAllCourses();
        $this->load->view('transfer_add_view', array('user' => $user, 'tcourse' => $tcourse, 'courses' => $courses));
    }

    public function confirm()
    {
        
    }

    public function removeTransferCredit()
    {
         if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');
        $courses = new Course_model();
        $this->load->model('Student_transfer_course_model');
        $tcourse = new Student_transfer_course_model();
        $tcouse->removeEquivilentCourse($courses);
    }
}

