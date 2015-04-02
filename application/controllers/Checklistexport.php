<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
	//Funciton takes in three parameters, last two are optional
	//	The userID, also known as CWID, to pull a students information
	//	A curriculumID represnting the curriculum to use (from the database)
	//	The file extensions/type (example pdf for PDF and xls for excel spreadsheet)
	//		Only xls is supported right now
	public function index($userID = NULL, $curriculumID = 1, $type = "xls")
	{
	    //Assuming a user with classes is passed and curriculum
            //	Must be valid!
	    $this->load->model('User_model', 'Curriculum_model');
	    
	    $user = new User_Model();
	    $user->loadPropertiesFromPrimaryKey($userID);
	    $curriculum = new Curriculum_Model();
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);

	    $filename = "checklist.xls";

	    //Load necessary data from spreadsheet
	    $objReader = PHPExcel_IOFactory::createReader('Excel5');
            $outputfile = $objReader->load($filename);
            
            $sheets = $outputfile->getSheetNames();
	    
	    for ($i = 0; $i < count($sheets); $i++)
		if (strcasecmp($sheets[$i], "checklist") == 0)
			$outputfile->setActiveSheetIndex($i);

	    $checksheet = $outputfile->getActiveSheet();
	    $cells = $checksheet->toArray();
	    $location = array(
	    	"name"    => NULL,
		"email"   => NULL,
		"advisor" => NULL,
		"year"    => NULL,
		"date"    => NULL,
		"cwid"    => NULL);
	    //Find and set user information (name, email, etc)
	    for ($row = 0; $row < count($cells); $row++)
	    	for ($col = 0; $col < count($cells[$row]); $col++)
		{
			$val = $checksheet->getCellByColumnAndRow($row, $col)->getValue();
			if ($location["name"] == NULL && strcasecmp($val, "name")           == 0)
				$location["name"]    = array($row, $col);
			if ($location["advisor"] == NULL && strcasecmp($val, "advisor")     == 0)
				$location["advisor"] = array($row, $col);
			if ($location["email"] == NULL && strcasecmp($val, "email")         == 0)
				$location["email"]   = array($row, $col);
			if ($location["year"] == NULL && strcasecmp($val, "catalog year")   == 0)
				$location["year"]    = array($row, $col);
			if ($location["date"] == NULL && strcasecmp($val, "last updated")   == 0)
				$location["date"]    = array($row, $col);
			if ($location["cwid"] == NULL && strcasecmp($val, "student id")     == 0)
				$location["cwid"]    = array($row, $col);
		}
	    //Default to two cells over from label, but should do a search instead of static
	    $location["cwid"] = $checksheet->getCellByColumnAndRow($location["cwid"][0]+2, $location["cwid"][1]);
	    $location["name"] = $checksheet->getCellByColumnAndRow($location["name"][0]+2, $location["name"][1]);
	    $location["date"] = $checksheet->getCellByColumnAndRow($location["date"][0]+2, $location["date"][1]);
	    $location["year"] = $checksheet->getCellByColumnAndRow($location["year"][0]+2, $location["year"][1]);
	    $location["email"] = $checksheet->getCellByColumnAndRow($location["email"][0]+2, $location["email"][1]);
	    $location["advisor"] = $checksheet->getCellByColumnAndRow($location["advisor"][0]+2, $location["advisor"][1]);
	   
	    $location["cwid"]->setValue($user->getUserID());
	    $location["name"]->setValue($user->getName());
	    $location["email"]->setValue($user->getEmailAddress());
	    $location["advisor"]->setValue($user->getAdvisor());
	    $location["date"]->setValue(date(DATE_RFC2822));
	    $location["year"]->setValue("2015"); 

	    $course = NULL;
	    for ($col = 0; $col < count($cells); $col++)
	    	for ($row = 0; $row < count($cells[$col]); $row++)
	    		if (strcmp($checksheet->getCellByColumnAndRow($col, $row)->getValue(), "COURSE") == 0)
				$course = array($row, $col);
	    
	    $requiredCourses = $curriculum->getCurriculumCourseSlots();
	    $takenCourses    = $user->getAllCoursesTaken();

	    //$course holds the row/col of the COURSE cell, the following cells are the headers for courses
	    $year  = NULL;
	    $term  = NULL;
	    $grade = NULL;
	    for ($col = 0; $col < count($cells[$course[0]]); $col++)
	    {
		$val = $checksheet->getCellByColumnAndRow($col, $course[0])->getValue();
		if ($year  == NULL && strcmp($val, "YEAR") == 0)
			$year = array($course[0], $col);
	    	if ($term  == NULL && strcmp($val, "TERM") == 0)
			$term = array($course[0], $col);
	    	if ($grade == NULL && strcmp($val, "GRADE") == 0)
			$grade = array($course[0], $col);
	    }

	    //Based on $course, get the major class types and their associated class numbers
	    $checkCourses = array();
	    for ($row = $course[0]+1; true; $row++)
	    {
	    	$val = $checksheet->getCellByColumnAndrow($course[1], $row)->getValue();
		if ($val == NULL)
			continue;
		if (strlen($val) >= 5)
			break;
		for ($row2 = $row; true; $row2++)
		{
	    		$val2 = $checksheet->getCellByColumnAndrow($course[1]+1, $row2)->getValue();
			if ($val2 == NULL)
				break;
			$checkCourses[] = array($val . $val2, $row2);
		}
	    }
	    
	    //Get usable transcript info
	    $courseSections = $user->getAllCoursesTaken(); //array of course sections
	    
	    //Get the class names from the checklist
	    //	Each slot as validCourseIDs which is an array of the classes that fill the slot
	    $curriculumCourses = $curriculum->getCurriculumCourseSlots();
	    foreach ($curriculumCourses as $currCourse)
	    {
	        $validCourseIDs = $currCourse->getValidCourseIDs();
	        //Check every curriculum course against taken courses
	        foreach ($courseSections as $key => $courseSection)
		    if (in_array($courseSection[0]->getCourseSectionID(), $validCourseIDs))
		    {
		        $c = $courseSection[0]->getCourse()->getCourseName().$courseSection[0]->getCourse()->getCourseNumber();
		        foreach ($checkCourses as $checkCourse)
			    if (strcmp($c, $checkCourse[0]) == 0)
			    {
			    	$checksheet->getCellByColumnAndrow($grade[1], $checkCourse[1])->setValue($courseSection[1]);
				$q = $courseSection[0]->getAcademicQuarter();
			    	$checksheet->getCellByColumnAndrow($year[1], $checkCourse[1])->setValue($q->getYear());
			    	$checksheet->getCellByColumnAndrow($term[1], $checkCourse[1])->setValue($q->getName());
			    }
			    unset($courseSections[$key]);
		}
	    }
	    //Any leftover classes from courseSections should be put in the box on the right
            
	    //Download file object (PDF or XLS)
            switch ($type)
            {
                case "xls":
                    $objWriter = PHPExcel_IOFactory::createWriter($outputfile, 'Excel5');
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
