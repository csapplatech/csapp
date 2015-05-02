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

    /*public function viewStudentIds()
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
    }*/

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
            if (isset($_SESSION['t_user']))
            {
                if (!$t_user->loadPropertiesFromPrimaryKey($_SESSION['t_user']))
                {
                    redirect('Transfer_controller/index');
                }
            }
            else
            {
                redirect('Transfer_controller/index');
            }
        }
       $_SESSION['t_user'] =$t_user->getUserID(); 
        $t_courses = $t_user->getAllTransferCourses();
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
         if (!isset($_SESSION['UserID']))
            redirect('Login/logout');
        //Create new user and load its data
        $user = new User_model;
        if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');

        if (!$user->isProgramChair())
            redirect('Mainpage');

        $tcourse  = $this->input->post("transferCourseID");
        $t_course = new Student_transfer_course_model();
        $norm_course = new Course_model();
        //explode this, then load the data from pimary key, then load course from primary, then add equilvilent course
        $str_array = explode(",", $tcourse);
        $t_course->loadPropertiesFromPrimaryKey(intval($str_array[0]));
        $norm_course->loadPropertiesFromPrimaryKey(intval($str_array[1]));
        $t_course->addEquivilentCourse($norm_course);
        redirect('Transfer_controller/viewIdMapping');
    }

    public function remove()
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
        $t_course = new Student_transfer_course_model();
        $t_course->loadPropertiesFromPrimaryKey($tcourse);
        $courses = $t_course->getAllEquivilentCourses();
        $this->load->view('transfer_remove_view', array('user' => $user, 'tcourse' => $tcourse, 'courses' => $courses));
    }

    public function confirm_remove()
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
        $t_course = new Student_transfer_course_model();
        $norm_course = new Course_model();
        //explode this, then load the data from pimary key, then load course from primary, then add equilvilent course
        $str_array = explode(",", $tcourse);
        $t_course->loadPropertiesFromPrimaryKey(intval($str_array[0]));
        $norm_course->loadPropertiesFromPrimaryKey(intval($str_array[1]));
        $t_course->removeEquivilentCourse($norm_course);
        redirect('Transfer_controller/viewIdMapping');
    }
}

