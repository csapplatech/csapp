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
        $courses_taken = $usermod->getAllCoursesTaken();
        $courseIDs_passed = array();
        
        //Populate an array of course IDs for which the course was passed by the student
        foreach($courses_taken as $key => $value)
        {
            $min_grade = $value[0]->getCourse()->getAllCurriculumCourseSlots()[0]->getMinimumGrade();
            if ($usermod->getGradeForCourseSection($value[0]) >= $min_grade)
            {
                array_push($courseIDs_passed, $value[0]->getCourse()->getCourseID());
            }
        }
        
        //Now, what we need is classes that can be taken.  To that end, we will
        //pull all of the course sections for a quarter and unset and move those
        //classes whose prerequisites are not met
        
        //First, the array which will hold all course sections where signatures
        //are required (prerequisites not met)
        $signature_required = array();
        
        foreach($course_sections as $key => $value)
        {
            $prereqs = $value->getCourse()->getPrerequisiteCourses();
            if (count($prereqs) == 0)
                echo $value->getCourse()->getCourseName() . "\n" .
                    $value->getCourse()->getCourseNumber() . "\n" .
                    $value->getSectionName() . "\nnext\n";
            else
            {
                foreach($prereqs as $prereq)
                {
                    if (array_search($prereq->getCourseID(), $courseIDs_passed) == true)
                    {
                        echo $value->getCourse()->getCourseName() . "\n" .
                        $value->getCourse()->getCourseNumber() . "\n" .
                        $value->getSectionName() . "\nnext\n";
                        break;
                    }
                }
            }
        }
        
    }
}