<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
	//Funciton must be given the user and a curriculum
	public function index($user = NULL, $curriculum = NULL, $type = "xls")	
	{
		//Assuming a user with classes is passed and curriculum
		//	Must be valid!
		
		//Parse classes
		//	Taken classes are under StudentCourseSections, 
		//		which has a Student ID, Course ID, Grade
		//	Curriculums have a CurriculumCourseSlot which has CurriculumSlotValidCourse 
		//		joined on CirriculumCourseSlotID which have CourseID for the valid courses
			
		//Create file object (plaintext?)

		//Change to xls or pdf or whatever
		
		//Return file object (PDF or XLS)
		switch ($type)
		{
		    case "xls":
			$objReader = PHPExcel_IOFactory::createReader('Excel5');
			$objPHPExcel = $objReader->load("checklist.xls");

			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			header("Content-type: application/vnd.ms-exel");
			header("Content-Disposition: attachment; filename=test.xls");
			$objWriter->save('php://output');
		    	break;
		    case "pdf":
			echo "NOT IMPLEMENTED YET";
		    	break;
		    default:
			echo "UNSUPPORTED TYPE";
			break;
		}
	}
}
