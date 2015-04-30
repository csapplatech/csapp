<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class FutureCourses extends CI_Controller
{
	const MAX_FILE_SIZE = 262144000;
	
	const UPLOAD_FILE_DIR = "./uploads";
	
	public function index()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
			redirect('Login/logout');
		
		$data = array('user' => $user);
		
		$this->load->view('futurecourses_index_view', $data);
	}
	
	public function submit()
	{
		$user = new User_model;
		
		if (!$user->loadPropertiesFromPrimaryKey($_SESSION['UserID']))
            redirect('Login/logout');
		
		if (!$user->isAdmin())
		{
			header("Content-type: text/plain", true, 401);
			echo "Unauthorized access";
			return;
		}
		
		if(!isset($_POST['year']) || !isset($_POST['quarter']))
		{
			header("Content-type: text/plain", true, 400);
			echo "Missing required academic quarter information";
			return;
		}
		
		$academic_quarter = new Academic_quarter_model;
		
		if(!$academic_quarter->loadPropertiesFromNameAndYear($_POST['quarter'], $_POST['year']))
		{
			$academic_quarter->setName($_POST['quarter']);
			$academic_quarter->setYear($_POST['year']);
			
			if(!$academic_quarter->create())
			{
				header("Content-type: text/plain", true, 500);
				echo "Unable to load academic quarter";
				return;
			}
		}
		
		// Check $_FILES['upfile']['error'] value.
		switch ($_FILES['boss_file']['error']) 
		{
			case UPLOAD_ERR_OK:
				break;
				
			case UPLOAD_ERR_NO_FILE:
				header("Content-type: text/plain", true, 400);
				echo "No file sent";
				return;
				
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				header("Content-type: text/plain", true, 400);
				echo "Exceeded file size limit";
				return;
				
			default:
				header("Content-type: text/plain", true, 500);
				echo "Unknown error occurred";
				return;
		}
		
		// You should also check filesize here. 
		if ($_FILES['boss_file']['size'] > self::MAX_FILE_SIZE) 
		{
			header("Content-type: text/plain", true, 400);
			echo "Exceeded file size limit";
			return;
		}
		
		$file_name = hash("md5", time() . $_FILES['boss_file']['tmp_name']);
		
		$file_path = self::UPLOAD_FILE_DIR . "/" . $file_name . ".txt";
		
		if (!move_uploaded_file($_FILES['boss_file']['tmp_name'], $file_path))
		{
			header("Content-type: text/plain", true, 500);
			echo "Failed to move uploaded file";
			return;
		}
		
		$result = self::parseFutureCourseOfferingsFile($file_path, $academic_quarter->getAcademicQuarterID());
		
		// In future, possibly check to make sure file was successfully deleted here
		unlink($file_path);
		
		if($result == null)
		{
			header("Content-type: text/plain", true, 200);
			echo "Success";
		}
		else
		{
			header("Content-type: text/plain", true, 400);
			echo $result;
		}
	}
	
	private static function parseFutureCourseOfferingsFile($file_path, $academicQuarterID)
	{
		$servername = "127.0.0.1";
		$username = "root";
		$password = ""; 
		$dbname = "csc_webapp";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);
		
		if(!$conn)
		{
			return "Failed to connect to database";
		}
		
		mysqli_autocommit($conn, false);
		
		mysqli_begin_transaction($conn);
		
		$file = fopen($file_path, "r");
		
		if($file)
		{
			while(($line = fgets($file)) !== false)
			{
				$split = explode("~", $line);
				
				if(count($split) < 11)
					continue;
				
				$courseName = trim($split[0]);
				
				$courseNumber = trim($split[1]);
				
				$courseSectionName = trim($split[2]);
				
				$callNumber = trim($split[3]);
				
				$creditHours = trim($split[4]);
				
				$courseTitle = trim($split[5]);
				
				$days = trim($split[6]);
				
				$startTime = trim($split[7]);
				
				if(strlen($startTime) > 0)
				{
					if(strpos($startTime, "AM") !== false)
					{
						$startTimePM = false;
						
						$startTime = str_replace("AM", "", $startTime);
					}
					else if(strpos($startTime, "PM") !== false)
					{
						$startTimePM = true;
						
						$startTime = str_replace("PM", "", $startTime);
					}
					
					$startTimeSplit = explode(":", $startTime);
					
					$startTimeHour = intval($startTimeSplit[0]);
					$startTimeMinute = intval($startTimeSplit[1]);
					
					if($startTimePM && $startTimeHour < 12)
					{
						$startTimeHour += 12;
					}
				}
				
				$endTime = trim($split[8]);
				
				if(strlen($endTime) > 0)
				{
					if(strpos($endTime, "AM") !== false)
					{
						$endTimePM = false;
						
						$endTime = str_replace("AM", "", $endTime);
					}
					else if(strpos($endTime, "PM") !== false)
					{
						$endTimePM = true;
						
						$endTime = str_replace("PM", "", $endTime);
					}
				
					$endTimeSplit = explode(":", $endTime);
					
					$endTimeHour = intval($endTimeSplit[0]);
					$endTimeMinute = intval($endTimeSplit[1]);
					
					if($endTimePM && $endTimeHour < 12)
					{
						$endTimeHour += 12;
					}
				}
				
				$startTime = $startTimeHour * 100 + $startTimeMinute;
				
				$endTime = $endTimeHour * 100 + $endTimeMinute;
				
				$roomName = trim($split[9]);
				
				$instructor = trim($split[10]);
				
				$selectQuery = "SELECT CourseID FROM `Courses` WHERE `CourseName` = '$courseName' AND `CourseNumber` = '$courseNumber' LIMIT 1";
				
				$results = mysqli_query($conn, $selectQuery);
				
				if(mysqli_num_rows($results) > 0)
				{
					$arr = mysqli_fetch_assoc($results);
					
					$courseID = $arr['CourseID'];
					
					$insertQuery = "INSERT INTO `CourseSections` (`CourseID`, `SectionName`, `Hours`, `InstructorName`, `CallNumber`, `AcademicQuarterID`) VALUES ($courseID, '$courseSectionName', $creditHours, '$instructor', $callNumber, $academicQuarterID)";
					
					mysqli_query($conn, $insertQuery);
					
					$id = mysqli_insert_id($conn);
					
					for($i=0;$i<strlen($days);$i++)
					{
						$dayOfWeekLetter = $days[$i];
						
						if($dayOfWeekLetter == 'M' || $dayOfWeekLetter == 'm')
						{
							$dayOfWeek = Course_section_time_model::DAY_MONDAY;
						}
						else if($dayOfWeekLetter == 'T' || $dayOfWeekLetter == 't')
						{
							$dayOfWeek = Course_section_time_model::DAY_TUESDAY;
						}
						else if($dayOfWeekLetter == 'W' || $dayOfWeekLetter == 'w')
						{
							$dayOfWeek = Course_section_time_model::DAY_WEDNESDAY;
						}
						else if($dayOfWeekLetter == 'R' || $dayOfWeekLetter == 'r')
						{
							$dayOfWeek = Course_section_time_model::DAY_THURSDAY;
						}
						else if($dayOfWeekLetter == 'F' || $dayOfWeekLetter == 'f')
						{
							$dayOfWeek = Course_section_time_model::DAY_FRIDAY;
						}
						else
						{
							$dayOfWeek = "";
						}
						
						if(strlen($dayOfWeek) > 0)
						{
							$insertQuery = "INSERT INTO `CourseSectionTimes` (`CourseSectionID`, `DayOfWeek`, `StartTime`, `EndTime`) VALUES ($id, '$dayOfWeek', $startTime, $endTime)";
							
							mysqli_query($conn, $insertQuery);
						}
					}
				}
			}
			
			fclose($file);
			
			mysqli_commit($conn);
			
			mysqli_autocommit($conn, true);
			
			return null;
		}
		else
		{
			return "Unable to read file";
		}
	}
}