<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coursemanager extends CI_Controller
{
	const MAX_FILE_SIZE = 262144000;
	
	const UPLOAD_FILE_DIR = "./uploads";
	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		$categories = Course_model::getAllCourseCategories();
		
		$courseName = null;
		
		if($this->uri->segment(3))
		{
			$courseName = $this->uri->segment(3);
		}
		else if(count($categories) > 0)
		{
			$courseName = $categories[0]['CourseName'];
		}
		
		$courses = Course_model::getAllCourses($courseName);
		
		$data = array(
			'user' => $user,
			'categories' => $categories,
			'courses' => $courses,
			'category' => Course_model::getCategoryName($courseName)
		);
		
		$this->load->view('coursemanager_index_view', $data);
	}
	
	public function edit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		if(!$this->uri->segment(3))
		{
			redirect('Coursemanager/index');
		}
		
		$course = new Course_model;
		
		if(!$course->loadPropertiesFromPrimaryKey($this->uri->segment(3)))
		{
			redirect('Coursemanager/index');
		}
		
		$categories = Course_model::getAllCourseCategories();
		
		$data = array(
			'user' => $user,
			'course' => $course,
			'courses' => Course_model::getAllCourses(),
			'courseTypes' => Course_model::getAllCourseTypes(),
			'categories' => $categories
		);
		
		$this->load->view('coursemanager_edit_view', $data);
	}
	
	public function edit_submit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		if(!isset($_POST['courseID']) || !isset($_POST['courseName']) || !isset($_POST['courseType']) || !isset($_POST['courseNumber']) || !isset($_POST['courseTitle']))
		{
			$_SESSION['coursemanager.edit.errormessage'] = "Missing required data";
			
			if(isset($_POST['courseID']))
			{
				redirect('Coursemanager/edit/' . $_POST['courseID']);
			}
			else
			{
				redirect('Coursemanager/index');
			}
		}
		
		$courseID = $_POST['courseID'];
		$courseName = $_POST['courseName'];
		$courseType = $_POST['courseType'];
		$courseNumber = $_POST['courseNumber'];
		$courseTitle = $_POST['courseTitle'];
		
		$courseDescription = ($_POST['courseDescription'] != null) ? $_POST['courseDescription'] : "";

		$model = new Course_model;
		
		if(!$model->loadPropertiesFromPrimaryKey($courseID))
		{
			redirect('Coursemanager/index');
		}
		
		$model->setCourseName($courseName);
		$model->setCourseType($courseType);
		$model->setCourseNumber($courseNumber);
		$model->setCourseTitle($courseTitle);
		$model->setCourseDescription($courseDescription);
		
		$coreqs = $model->getCorequisiteCourses();
		
		$prereqs = $model->getPrerequisiteCourses();
		
		$new_coreqs = ((isset($_POST['courseCoreqs'])) ? $_POST['courseCoreqs'] : array());
		
		$new_prereqs = ((isset($_POST['coursePrereqs'])) ? $_POST['coursePrereqs'] : array());
		
		foreach($coreqs as $coreq)
		{
			if(in_array($coreq->getCourseID(), $new_coreqs))
			{
				$index = array_search($coreq->getCourseID(), $new_coreqs);
				
				if($index)
					unset($new_coreqs[$index]);
			}
			else
			{
				$model->removeCourseRequisite($coreq);
			}
		}
		
		foreach($new_coreqs as $new_coreq_id)
		{
			$new_course = new Course_model;
			
			if($new_course->loadPropertiesFromPrimaryKey($new_coreq_id))
				$model->addCourseCorequisite($new_course);
		}
		
		foreach($prereqs as $prereq)
		{
			if(in_array($prereq->getCourseID(), $new_prereqs))
			{
				$index = array_search($prereq->getCourseID(), $new_prereqs);
				
				unset($new_prereqs[$index]);
			}
			else
			{
				$model->removeCourseRequisite($prereq);
			}
		}
		
		foreach($new_prereqs as $new_prereq_id)
		{
			$new_course = new Course_model;
			
			if($new_course->loadPropertiesFromPrimaryKey($new_prereq_id))
				$model->addCoursePrerequisite($new_course);
					
		}
		
		if($model->update())
		{
			redirect('Coursemanager/index/' . $courseName);
		}
		else
		{
			$_SESSION['coursemanager.edit.errormessage'] = "Invalid data format";
			
			redirect('Coursemanager/edit/' . $courseID);
		}
	}
	
	public function create()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		$categories = Course_model::getAllCourseCategories();
		
		$data = array(
			'user' => $user,
			'courseTypes' => Course_model::getAllCourseTypes(),
			'categories' => $categories
		);
		
		$this->load->view('coursemanager_create_view', $data);
	}
	
	public function create_submit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		if(!isset($_POST['courseName']) || !isset($_POST['courseType']) || !isset($_POST['courseNumber']) || !isset($_POST['courseTitle']))
		{
			$_SESSION['coursemanager.create.errormessage'] = "Missing required data";
			
			redirect('Coursemanager/create');
		}
		
		$courseName = $_POST['courseName'];
		$courseType = $_POST['courseType'];
		$courseNumber = $_POST['courseNumber'];
		$courseTitle = $_POST['courseTitle'];
		
		$courseDescription = ($_POST['courseDescription'] != null) ? $_POST['courseDescription'] : "";

		$model = new Course_model;
		
		$model->setCourseName($courseName);
		$model->setCourseType($courseType);
		$model->setCourseNumber($courseNumber);
		$model->setCourseTitle($courseTitle);
		$model->setCourseDescription($courseDescription);
		
		if($model->create())
		{
			redirect('Coursemanager/index/' . $courseName);
		}
		else
		{
			$_SESSION['coursemanager.create.errormessage'] = "Invalid data format";
			
			redirect('Coursemanager/create');
		}
	}
	
	public function delete()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isProgramChair())
			redirect('Login/logout');
		
		if(!$this->uri->segment(3))
			redirect('Coursemanager/index');
		
		$course = new Course_model;
		
		if(!$course->loadPropertiesFromPrimaryKey($this->uri->segment(3)))
			redirect('Coursemanager/index');
		
		$course->delete();
		
		redirect('Coursemanager/index/' . $course->getCourseName());
	}
}