<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class AdvisingForm extends CI_Controller
{
    private $uid;
    public function index()
    {
        error_reporting(E_ALL & ~E_WARNING & ~E_STRICT);
        $this->load->helper('url');
        //$uid = $_SESSION['UserID'];
        /*if (!isset($_SESSION['UserID']))
        {
            redirect('login');
        }*/
		
		$user = new User_model;
		
		if(!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
			redirect('Login/logout');
		
		if($user->isStudent())
		{
			$this->uid = $user->getUserID();
		}
		else if($user->isAdvisor())
		{
			if($this->uri->segment(3))
			{
				$this->uid = $this->uri->segment(3);
			}
			else if($_SESSION['StudCWID'])
			{
				$this->uid = $_SESSION['StudCWID'];
			}
			else
			{
				redirect('Login');
			}
		}
		else
		{
			redirect('Login');
		}
        
		$_SESSION['StudCWID'] = $this->uid;
		
       // $uid = 10210078;
        //$year = 2015;
         $prev_form = $this->loadAdvisingForm($this->uid);
        /*if (isset($_SESSION['StudentFormUID']))
        {
            $prev_form = $this->loadAdvisingForm($_SESSION['StudentFormUID']);
        }
        else
        {
            $prev_form = $this->loadAdvisingForm($_SESSION['UserID']);
        }*/
        
        //Get course list for student
        //First, get all courses for current quarter, set now to 'NAME_SPRING'
        $quarter = academic_quarter_model::NAME_SPRING;
        $aqm = academic_quarter_model::getLatestAcademicQuarter();
        //$aqm = new academic_quarter_model();
        //$aqm->loadPropertiesFromNameAndYear($quarter, $year);
        $qid = $aqm->getAcademicQuarterID();
        $course_sections = $aqm->getAllCourseSections(); 
        
        //Next, load the current user and get his courses taken
        $usermod = new user_model();
        $usermod->loadPropertiesFromPrimaryKey($this->uid);
        
        $courseids = array();
        //Get all of the course IDs for courses in the student's curricula
        $curricula = $usermod->getCurriculums();
        foreach($curricula as $curriculum)
        {
            $slots = $curriculum->getCurriculumCourseSlots();
            foreach($slots as $slot)
            {
                $validIDs = $slot->getValidCourseIDs();
                foreach($validIDs as $cor)
                {
                    if (!in_array($cor, $courseids))
                    {
                        array_push($courseids, $cor);
                    }
                }
            }
        }
        
        
        
        //Now, remove each course section that does not fit into the student's curricula
        foreach($course_sections as $key => $value)
        {
            if (!in_array($value->getCourse()->getCourseID(), $courseids))
            {
                unset($course_sections[$key]);
            }
        }
        
        //Then, we can remove courses whose prerequisites are not met
        $courseIDs_passed = array();
		
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
                    unset($course_sections[$key]);
                }
            }
        }
        
        $courses_taken = $usermod->getAllCoursesTaken();
        
        if (!empty($courses_taken))
        {
            //Populate an array of course IDs for which the course was passed by the student
            foreach($courses_taken as $key => $value)
            {
                if (!empty($value))
                {
                    if (!empty($value[0]->getCourse()->getAllCurriculumCourseSlots()))
                    {
                        $min_grade = $value[0]->getCourse()->getAllCurriculumCourseSlots()[0]->getMinimumGrade();
                        switch($min_grade)
                        {
                            case 4:
                                $min_grade = 'A';
                                break;
                            case 3:
                                $min_grade = 'B';
                                break;
                            case 2:
                                $min_grade = 'C';
                                break;
                            case 1:
                                $min_grade = 'D';
                                break;
                            default:
                                $min_grade = 'ZZZZZZ';
                                break;
                        }
                        
                        if ($usermod->getGradeForCourseSection($value[0]) <= $min_grade)
                        {
                            array_push($courseIDs_passed, $value[0]->getCourse()->getCourseID());
                        }
                    }
                }
                
            }
        }
        
        foreach ($course_sections as $key => $value)
        {
            if (in_array($value->getCourse()->getCourseID(), $courseIDs_passed))
            {
                unset($course_sections[$key]);
            }
        }
        
        //Now, we should have a complete list of course sections that are eligible
        //as well as available
        $working_list = $this->get_list($course_sections);
        
        
        
        
        
        $course_sections = $aqm->getAllCourseSections();
        
        $full_list = $this->get_list($course_sections);
        //Container holding course sections for courses already passed
        //$courseSections_passed = array();
        
        //At this point, the available course sections will be whittled down
        //based on what courses have already been taken.  First, the classes
        //already completed will be extracted
        /*foreach($course_sections as $key => $value)
        {
            //We've already gotten an array of passed courses.  Now we must remove
            //all sections whose course ID matches a passed one
            if (in_array($value->getCourse()->getCourseID(), $courseIDs_passed))
            {
                array_push($courseSections_passed, $value);
                unset($course_sections[$key]);
            }
        }*/
        
        //Now, what we need is classes that can be taken.  To that end, we will
        //pull all of the course sections for a quarter and unset and move those
        //classes whose prerequisites are not met to a signature_required array
        
        //First, the array which will hold all course sections where signatures
        //are required (prerequisites not met) must be declared
        //$signature_required = array();
        
        //Now, parse through and selectively move sig_required courses
        /*foreach($course_sections as $key => $value)
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
        }*/
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
        
        //$working_list = $this->get_list($signature_required, $courseSections_passed, $course_sections);
        //$name_arr = array('Recommended', 'Passed', 'Signature');
        //$index = 0;
        /*foreach ($working_list as $cat)
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
        }*/
        /*foreach ($working_list as $cat)
        {
            echo $cat->getName() . "\n" . $cat->getSubjects()[0]->getName() . "\n" .
                    $cat->getSubjects()[0]->getCourses()[0]->getName() . "\n" .
                    $cat->getSubjects()[0]->getCourses()[0]->getSections()[0]->getSectionName() . "\n";
        }*/
        /*foreach($working_list['Recommended']->getSubjects() as $subj)
        {
            foreach ($subj->getCourses() as $crs)
            {
                foreach($crs->getSections() as $sec)
                {
                    echo $subj->getName() . "\n" . $crs->getName() . "\n" . $sec->getSectionName() . "\n";
                }
            }
        }*/
        
        $data = array('recommended' => $working_list,
                    'all_courses' => $full_list,
                    'quarter_id' => $qid,
                    'cwid' => $usermod->getUserID(),
                    'student_name' => $usermod->getName(),
                    'form' => $prev_form,
					'user' => $user);
        $this->load->view('advising_view', $data);
    }
    
    public function get_list($course_sections)
    {
        //$result = array();
        /*$sig = new Category();
        $sig->setName(Category::NAME_SIG);
        $sig->setSubjects($this->course_sort($signature_required));
        
        
        $passed = new Category();
        $passed->setName(Category::NAME_PASSED);
        $passed->setSubjects($this->course_sort($courseSections_passed));*/
        
        $rec = new Category();
        $rec->setName(Category::NAME_REC);
        $rec->setSubjects($this->course_sort($course_sections));
        
        //$result['Recommended'] = $rec;
        //$result['Passed'] = $passed;
        //$result['Signature'] = $sig;
        return $rec;
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
        
        $copy = $result;
        $result = array();
        foreach($copy as $subj)
        {
            $subject = new Subject();
            $subject->setName(reset(reset($subj))->getCourse()->getCourseName());
            $subject->setTitle(reset(reset($subj))->getCourse()->getCourseCategoryName());
            $courses = array();
            foreach($subj as $crs)
            {
                $course = new Course();
                $course->setName(reset($crs)->getCourse()->getCourseNumber());
                $course->setTitle(reset($crs)->getCourse()->getCourseTitle());
                $course->setHours(reset($crs)->getHours());
                $course->setSections($crs);
                array_push($courses, $course);
            }
            $subject->setCourses($courses);
            array_push($result, $subject);                  
        }
        return $result;
    }
    
  
    public function save(){
        //*troubleshooting tip*
        //keep in mind when you press "save" this function will run 
        //and what ever you print_r will show in the success window popup
        //print_r($_POST);
        
        //this should first remove all data that is currently saved
        
        //next this will  gather the data from javascript
        //if(isset($_POST['name'])){
        //    print_r($_POST['name']);   
        
        //$jsonReceiveData = json_encode($_POST['{"Info":'], JSON_PRETTY_PRINT);
        //$uid = $_SESSION['UserID'];
        
        if (isset($_SESSION['StudCWID']))
         {
             $this->uid = $_SESSION['StudCWID'];
         }
         else 
         {
            if (!isset($_SESSION['UserID']))
            {
                redirect('login');
            }
            $this->uid = $_SESSION['UserID'];
         }
        
        $currentquarter = academic_quarter_model::getLatestAcademicQuarter();
        
        $previous_form = $this->loadAdvisingForm($this->uid);
        if ($previous_form !== false)
            $previous_form->delete();
        
        
		if(!isset($_POST['data']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Missing data";
			return;
		}
		
        //$data = $_POST['Info'];
        $data = json_decode($_POST['data']);//['data']);
		
        $mod = new advising_form_model();
        $mod->setStudentUserID(intval($this->uid));
        $mod->setAcademicQuarterID($currentquarter->getAcademicQuarterID());
        $mod->create();
		
		$entry = new Advising_log_entry_model;
				
		$student = new User_model;
		
		if($student->loadPropertiesFromPrimaryKey($this->uid))
		{
			$createdByAdvisor = ($this->uid != $_SESSION['UserID']);
		
			$entry->setStudentUser($student);
			$entry->setAdvisorUser($student->getAdvisor());
			$entry->setAdvisingLogEntryType(($createdByAdvisor) ? Advising_log_entry_model::ENTRY_TYPE_ADVISING_FORM_SAVED_BY_ADVISOR : Advising_log_entry_model::ENTRY_TYPE_ADVISING_FORM_SAVED_BY_STUDENT);
			
			$entry->create();
		}
		
        foreach($data->Info as $section)
        {
            //print_r($course->Type);
            $callNum = $section->CallNumber;
            
            
            $sections = $currentquarter->getAllCourseSections();
            $target = new course_section_model();
            /*foreach($sections as $sec)
            {
                if ($sec->getCallNumber() === $callNum)
                {
                    $target->loadPropertiesFromPrimaryKey($sec->getCourseSectionID());
                    break;
                }
            }*/
            $target->loadPropertiesFromPrimaryKey($callNum);
           $state = ($section->Type == "norm") ? advising_form_model::COURSE_SECTION_STATE_PREFERRED
                   : advising_form_model::COURSE_SECTION_STATE_ALTERNATE;
           $mod->addCourseSection($target, $state);
        }
        
        //$previous_form->delete();
        
        
        
        
        //print_r($_POST['{"Info":']);
        /*$blarg = json_decode($jsonReceiveData, true);
        foreach($blarg as $item)
        {
            foreach($item as $key => $value)
            {
                $info = $json_decode($key, true);
                foreach($info as $inf)
                {
                    echo "\n\n" . $inf;
                }
            }
        }*/
        /*$blarg = json_decode($jsonReceiveData);
        var_dump($blarg);*/
        //}
        
        //then it will store the new information in the database
    }
    
    public function loadAdvisingForm($uid)
    {
        $qid = academic_quarter_model::getLatestAcademicQuarter()->getAcademicQuarterID();
        $forms = advising_form_model::getAllAdvisingFormsByStudentID($uid);
        foreach($forms as $form)
        {
            if ($form->getAcademicQuarterID() === $qid)
                return $form;
        }
        return false;
    }
    
    public function loadAllStudents()
    {
        //
        $puid = $_SESSION['UserID'];
        
        $profmod = new user_model();
        $profmod->loadPropertiesFromPrimaryKey($puid);
        
        $student_list = $profmod->getAdvisees();
        
        $pdata = array('students' => $student_list);
        
        $this->load->view('all_students_view', $pdata);
        
    }
    public function loadStudentID()
    {
        //if(isset($_POST['StudID']))
       // {
            //print_r($_POST['StudID']);
            $StudID = $_POST['StudID'];
            $_SESSION['StudCWID'] = $StudID;
            print_r($StudID);
            //index();
       // }
    }
}
class Subject
{
    private $name;
    private $title;
    private $courses;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getCourses()
    {
        return $this->courses;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setCourses($courses)
    {
        $this->courses = $courses;
    }
}
class Course
{
    private $name;
    private $title;
    private $hours;
    private $sections;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function getHours()
    {
        return $this->hours;
    }
    
    public function getSections()
    {
        return $this->sections;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function setHours($hours)
    {
        $this->hours = $hours;
    }
    
    public function setSections($sections)
    {
        $this->sections = $sections;
    }
}
class Category
{
    private $name;
    private $subjects;
    
    const NAME_SIG = "Signature Required";
    const NAME_PASSED = "Passed";
    const NAME_REC = "Recommended";
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getSubjects()
    {
        return $this->subjects;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setSubjects($subjects)
    {
        $this->subjects = $subjects;
    }
}