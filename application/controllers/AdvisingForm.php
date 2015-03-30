<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdvisingForm extends CI_Controller
{
    public function index()
    {
        $this->load->helper('url');
        /*$uid = $_SESSION['UserID'];
        if (!isset($_SESSION['UserID']))
        {
            redirect('login');
        }*/
        $uid = 10210078;
        $year = 2015;
        
        //Get course list for student
        //First, get all courses for current quarter, set now to 'NAME_SPRING'
        $quarter = academic_quarter_model::NAME_SPRING;
        $aqm = new academic_quarter_model();
        $aqm->loadPropertiesFromNameAndYear($quarter, $year);
        $qid = $aqm->getAcademicQuarterID();
        $course_sections = $aqm->getAllCourseSections(); 
        
        //Next, load the current user and get his courses taken
        $usermod = new user_model();
        $usermod->loadPropertiesFromPrimaryKey($uid);
        $ct = $usermod->getAllCoursesTaken();
        $courseIDs_passed = array();
        foreach($ct as $c)
        {
            array_push($courseIDs_taken, $c[0]->getCourse()->getCourseID())
        }
        
        //Remove all courses from the master list that have been taken by the user
        foreach ($course_sections as $key => $tag)
        {
            foreach ($ct as $course)
            {
                if ($course[0]->getCourse()->getCourseID() == $tag->getCourse()->getCourseID())
                {
                    unset($course_sections[$key]);
                    break;
                }
            }
        }
        
        //Now print the remaining available courses as a subject/number pair
        foreach ($course_sections as $cs)
        {
            
        }
    }
}