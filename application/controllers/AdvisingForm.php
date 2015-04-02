<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdvisingForm extends CI_Controller
{
    
    public function index()
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_STRICT);
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
        
        //Container holding course sections for courses already passed
        $courseSections_passed = array();
        
        //At this point, the available course sections will be whittled down
        //based on what courses have already been taken.  First, the classes
        //already completed will be extracted
        foreach($course_sections as $key => $value)
        {
            //We've already gotten an array of passed courses.  Now we must remove
            //all sections whose course ID matches a passed one
            if (array_search($value->getCourse()->getCourseID(), $courseIDs_passed) != false)
            {
                array_push($courseSections_passed, $value);
                unset($course_sections[$key]);
            }
        }
        
        //Now, what we need is classes that can be taken.  To that end, we will
        //pull all of the course sections for a quarter and unset and move those
        //classes whose prerequisites are not met to a signature_required array
        
        //First, the array which will hold all course sections where signatures
        //are required (prerequisites not met) must be declared
        $signature_required = array();
        
        //Now, parse through and selectively move sig_required courses
        foreach($course_sections as $key => $value)
        {
            $prereqs = $value->getCourse()->getPrerequisiteCourses();
        
            if (count($prereqs) != 0)
            {
                $sig_required = true;
                foreach($prereqs as $prereq)
                {
                    if (array_search($prereq->getCourseID(), $courseIDs_passed) == true)
                    {
                        $sig_required = false;
                        break;
                    }
                }
                if ($sig_required)
                {
                    array_push($signature_required, $value);
                    unset($course_sections[$key]);
                }
            }
        }
        //Now, we have a three lists of course sections.  One, $courseSections_passed,
        //contains all course sections for courses offered this quarter that the
        //student has passed in the past
        
        //The second, $signature_required, contains all course sections for which the
        //prerequisites are not met.  For the student to take these courses,
        //a signature will have to be procured
        
        //The third and final array, $course_sections, contains only those sections for
        //courses that don't fall into the previous two categories.  Essentially, they
        //would be higher priority courses than the others.
        
        //At this point, we need to sort each array by subject, then by number,
        //then by section.
        
        $working_list = $this->get_list($signature_required, $courseSections_passed, $course_sections);
        $name_arr = array('Recommended', 'Passed', 'Signature');
        $index = 0;
        foreach ($working_list as $cat)
        {
            $name = $name_arr[$index];
            $index = $index + 1;
            foreach($cat as $subj)
            {
                foreach($subj as $course)
                {
                    foreach($course as $sec)
                    {
                        echo $name . "\n" . $sec->getCourse()->getCourseName() .
                                "\n" . $sec->getCourse()->getCourseNumber() .
                                "\n" . $sec->getSectionName() . "\n";
                    }
                }
            }
        }
    }
    
    public function get_list($signature_required, $courseSections_passed, $course_sections)
    {
        $result = array();
        array_push($result, $this->course_sort($course_sections));
        array_push($result, $this->course_sort($courseSections_passed));
        array_push($result, $this->course_sort($signature_required));
        return $result;
    }
    
    public function course_sort($courseSections)
    {
        //First, we declare an array to hold subjects
        $result = array();
        //We get the name of the first element, then use that to re-sort each
        //section by subject
        while(count($courseSections) > 0)
        {
           $name = reset($courseSections)->getCourse()->getCourseName();
           $new_res = array_filter($courseSections, function($elem) use($name)
           {
               return $elem->getCourse()->getCourseName() === $name;
           });
           $courseSections = array_filter($courseSections, function($elem) use($name)
           {
               return $elem->getCourse()->getCourseName() != $name;
           });
           
           array_push($result, $new_res);
        }
        $copy = $result;
        $result = array();
        //Now that we have the subjects, we need to group sections into courses by course number
        foreach($copy as $r)
        {
            $next = array();
            while(count($r) > 0)
            {
                $num = reset($r)->getCourse()->getCourseNumber();
                $new_res = array_filter($r, function($elem) use($num)
                {
                    return $elem->getCourse()->getCourseNumber() === $num;
                });
                $r = array_filter($r, function($elem) use($num)
                {
                    return $elem->getCourse()->getCourseNumber() != $num;
                });
                array_push($next, $new_res);
            }
            array_push($result, $next);
        }
        /*$result = array_filter($result, function($elem)
        {
            return count($elem) > 0;
        });*/
        $copy = $result;
        $result = array();
        foreach($copy as $res)
        {
            foreach($res as $r)
            {
                usort($r, function($a, $b)
                {
                    return $a->getSectionName() < $b->getSectionName() ? 1 : -1;
                });
            }
            array_push($result, $res);
        }
        $copy = $result;
        $result = array();
        foreach($copy as $r)
        {
            usort($r, function($a, $b)
            {
                return reset($a)->getCourse()->getCourseNumber() >
                        reset($b)->getCourse()->getCourseNumber() ? 1 : -1;
            });
            array_push($result, $r);
        }
        usort($result, function($a, $b)
        {
            return reset(reset($a))->getCourse()->getCourseName() >
                    reset(reset($b))->getCourse()->getCourseName() ? 1 : -1;
        });
        
        
        
        return $result;
    }
}