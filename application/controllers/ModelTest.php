<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ModelTest short summary.
 *
 * ModelTest is a controller for outputting simple test results for unit testing various models as they are created
 * This controller should not exist in the final software product for the CS App product
 */
class ModelTest extends CI_Controller
{
    /*
     * Simple Index action for this controller to ensure the controller is accessible
     */
    public function index()
    {
        echo "THIS IS A TEST";
    }
    
    public function user()
    {
        // Check to see if a user id segment in the URI was specified
        if( ! $this->uri->segment(3, 0))
        {
            $user = new User_model;
            
            $user->setEmailAddress("jch101@latech.edu");
            $user->setName("John Hawkins");
            $user->setPassword("Password");
            $user->addRole(User_model::ROLE_STUDENT);
			$user->setState(User_model::STATE_ACTIVATED);
            
            $user->create();
            
            echo "<h3>No user model Primary key specified</h3>";
            echo "<p>Try <code>https://localhost/index.php/ModelTest/user/1</code></p>";
        }
        else
        {
            $user = new User_model;
            
            // Try to load the user model values from the primary key provided in the URL
            if($user->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
            {
                $courseSection = new Course_section_model;
                
                $courseSection->loadPropertiesFromPrimaryKey(1);
                
                $user->addCourseSection($courseSection, 4);
                
                $user->update();
                
                echo "<h3>Success in finding user!</h3><code>";
                print_r($user);
                echo "</code>";
                
                echo "<br/><br/><br/><br/><br/><br/>";
                
                print_r($user->getAllCoursesTaken());
            }
            // Invalid UserID format or no user of that ID was found
            else
            {
                echo "<h2>Error 404 - User not found";
            }
        }
    }
    
    public function course()
    {   
        $courses = Course_model::getAllCourses();
        
        $count = count($courses);
        
        for($i=$count;$i<$count+10;$i++)
        {
            $newCourse = new Course_model;
            
            $newCourse->setCourseName("CSC");
            $newCourse->setCourseNumber(100 + $i);
			$newCourse->setCourseTitle("Intro to Databases");
            $newCourse->setCourseDescription("An example course for the database unit testing");
            
            if($newCourse->create())
            {
                array_push($courses, $newCourse);
            }
        }
        
        print_r($courses);
    }
    
    public function curriculum()
    {
        if($this->uri->segment(3, 0) && $this->uri->segment(4, 0) && $this->uri->segment(5, 0))
        {
            $num = filter_var($this->uri->segment(3,0), FILTER_SANITIZE_NUMBER_INT);
            $num2 = filter_var($this->uri->segment(4,0), FILTER_SANITIZE_NUMBER_INT);
            $num3 = filter_var($this->uri->segment(5,0), FILTER_SANITIZE_NUMBER_INT);
            
            
            for($i=0;$i<$num;$i++)
            {
                $curriculum = new Curriculum_model;
                
                $curriculum->setName("Curriculum " . ($i + 1));
                
                for($j=0;$j<$num2;$j++)
                {
                    $courseSlot = new Curriculum_course_slot_model;
                    
                    $courseSlot->setName("Course Slot #" . ($j + 1));
                    
                    for($k=1;$k<=$num3;$k++)
                    {
                        $courseSlot->addValidCourseID($k);
                    }
                    
                    $curriculum->addCurriculumCourseSlot($courseSlot);
                }
                
                $curriculum->create();
            }
            
            echo "Created " . $num . " curriculum(s) with " . $num2 . " course slot(s) each and " . $num3 . " valid course id(s) per slot<br />";
            print_r(Curriculum_model::getAllCurriculums());
        }
        else
        {
            $curriculums = Curriculum_model::getAllCurriculums();
            
            print_r($curriculums);
            
            if(count($curriculums) > 0)
            {
                $curriculums[0]->delete();
                
                echo "Curriculum: " . $curriculums[0]->getCurriculumID() . " deleted...";
            }
        }
    }

    public function course_section()
    {
        if($this->uri->segment(3, 0))
        {
            if($this->uri->segment(5, 0))
            {
                $academicQuarterID = $this->uri->segment(3, 0);
                $courseID = $this->uri->segment(4, 0);
                $sectionName = $this->uri->segment(5, 0);
                
                $courseSection = new Course_section_model;
                $courseSection->setSectionName($sectionName);
				$courseSection->setHours(3);
				$courseSection->setCallNumber(111222);
                
                if($courseSection->setAcademicQuarterFromID($academicQuarterID) && $courseSection->setCourseFromID($courseID))
                {
                    if($courseSection->create())
                    {
                        echo "Course Section " . $courseSection->getCourseSectionID() . " created!<br/><br/>";
                        print_r($courseSection);
                    }
                    else
                    {
                        echo "Course Section not created!";
                    }
                }
                else
                {
                    echo "401 - Invalid resource ids";
                }
            }
            else
            {
                $courseSection = new Course_section_model;
                
                if($courseSection->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
                {
                    print_r($courseSection);
                }
                else
                {
                    echo "404 Course Section Model not found";
                }
            }
        }
        else
        {
            $coureSections = Course_section_model::getAllCourseSections();
            
            foreach($coureSections as $courseSection)
            {
                echo "Course Section # " . $courseSection->getCourseSectionID() . " " . ($courseSection->delete() ? "Succeeded" : "Failed") . "<br/>";
            }
        }
    }
    
    public function academic_quarter()
    {
        if($this->uri->segment(3, 0))
        {
            $quarter = new Academic_quarter_model;
            
            if($quarter->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
            {
                print_r($quarter);
            }
            else
            {
                echo "404 Academic Quarter not found";
            }
        }
        else
        {
            $quarter = new Academic_quarter_model;
            
            $quarter->setName(Academic_quarter_model::NAME_FALL);
            $quarter->setYear(2014);
            
            echo "Fall 2014: " . $quarter->create();
            
            $quarter = new Academic_quarter_model;
            
            $quarter->setName(Academic_quarter_model::NAME_WINTER);
            $quarter->setYear(2015);
            
            echo "Winter 2015: " . $quarter->create();
            
            $quarter = new Academic_quarter_model;
            
            $quarter->setName(Academic_quarter_model::NAME_SPRING);
            $quarter->setYear(2015);
            
            echo "Spring 2015: " . $quarter->create();
            
            $quarter = new Academic_quarter_model;
            
            $quarter->setName(Academic_quarter_model::NAME_SUMMER);
            $quarter->setYear(2015);
            
            echo "Summer 2015: " . $quarter->create();
        }
    }
    
    public function advising_schedule()
    {
        // Check to see if a user id segment in the URI was specified
        if( ! $this->uri->segment(3, 0))
        {
            echo "<h3>No advising schedule model Primary key specified</h3>";
            echo "<p>Try <code>https://localhost/index.php/ModelTest/advising_schedule/1</code></p>";
        }
        else
        {
            // Load a user model instance and give the variable name 'user'
            $this->load->model('Advising_schedule_model', 'advisingSchedule');
            
            // Try to load the user model values from the primary key provided in the URL
            if($this->advisingSchedule->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
            {
                echo "<h3>Success in finding advising schedule!</h3><code>";
                print_r($this->advisingSchedule);
                echo "</code>";
            }
            // Invalid UserID format or no user of that ID was found
            else
            {
                echo "<h2>Error 404 - Schedule not found";
            }
        }
    }
}
