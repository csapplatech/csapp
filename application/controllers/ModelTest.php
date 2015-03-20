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
            echo "<h3>No user model Primary key specified</h3>";
            echo "<p>Try <code>https://localhost/index.php/ModelTest/user/1</code></p>";
        }
        else
        {
            // Load a user model instance and give the variable name 'user'
            $this->load->model('User_model', 'user');
            
            // Try to load the user model values from the primary key provided in the URL
            if($this->user->loadPropertiesFromPrimaryKey($this->uri->segment(3, 0)))
            {
                echo "<h3>Success in finding user!</h3><code>";
                print_r($this->user);
                echo "</code>";
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
}
