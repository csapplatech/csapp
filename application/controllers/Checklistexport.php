<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
	private $titlestyle = array(
	 'fill' => array(
		  'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
		  'color' => array('rgb' => 'B0B0B0')),
	 'font' => array('bold' => true),
	 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);
		    
	public function index($userID = NULL, $curriculumID = 1, $type = "xls")
	{

	    //Assuming a user with classes is passed and curriculum
	    //	Must be valid!
	    $this->load->model('User_model', 'Curriculum_model');
	    
	    $user = new User_Model();
	    $user->loadPropertiesFromPrimaryKey($userID);
	    $curriculum = new Curriculum_Model();
	    $curriculum->loadPropertiesFromPrimaryKey($curriculumID);

	    //Create excel file
	    $Excel = new PHPExcel();
	    $Excel->getProperties()->setCreator("Keen-Hjorth")
				   ->setLastModifiedBy("Keen-Hjorth")
				   ->setTitle("Test Checklist")
				   ->setSubject("Advising Checklist")
				   ->setDescription("Auto Generated Checklist")
				   ->setCategory("Advisee checklist file");
	    

	    //Set global defaults
	    $Excel->getDefaultStyle()->getFont()->setSize(10)->setName('Arial');
	    
	    //Handle checklist sheet stuff
	    $checklist = $Excel->getActiveSheet();
	    $checklist->setTitle("Checklist");
	   
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
	    //Set column widths across the checklist
	    $checklist->getColumnDimension('A')->setWidth(6.5);
	    $checklist->getColumnDimension('B')->setWidth(4.8);
	    $checklist->getColumnDimension('C')->setWidth(10);
	    $checklist->getColumnDimension('D')->setWidth(15);
	    $checklist->getColumnDimension('E')->setWidth(15);
	    $checklist->getColumnDimension('G')->setWidth(6);
	    $checklist->getColumnDimension('H')->setWidth(6);
	    $checklist->getColumnDimension('I')->setWidth(4.8);
	    $checklist->getColumnDimension('J')->setWidth(8.2);
	    $checklist->getColumnDimension('K')->setWidth(3.2);
	    $checklist->getColumnDimension('L')->setWidth(20);
	    $checklist->getColumnDimension('M')->setWidth(6);
	    $checklist->getColumnDimension('O')->setWidth(6);
	    $checklist->getColumnDimension('P')->setWidth(6);

	    //Set first six rows with the below information
	    $this->header($checklist, $user->getName(), $user->getUserID(), 
			  $user->getAdvisor(), "2014-15", $user->getEmailAddress());

	    $coursesTaken = $user->getAllCoursesTaken();
	    $this->primary($checklist, $coursesTaken, $curriculum);

	    //Download file object (PDF or XLS)
	    $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
	    header("Content-type: application/vnd.ms-exel");
	    header("Content-Disposition: attachment; filename=test.xls");
	    $objWriter->save('php://output');
	}

	private function header($checklist, $name, $studID, $advisor, $year, $email)
	{
	    //Set advisee name fields
	    $checklist->mergeCells("A2:B2");
	    $checklist->getCell("A2")->setValue("Name");
	    $checklist->getStyle("A2")->getFont()->setBold(True);
	    $checklist->mergeCells("C2:E2");
	    $checklist->getStyle("C2")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getCell("C2")->setValue($name);

	    //Set catalog year
	    $checklist->mergeCells("G2:H2");
	    $checklist->getCell("G2")->setValue("Catalog Year");
	    $checklist->getStyle("G2")->getFont()->setBold(True);
	    $checklist->mergeCells("I2:L2");
	    $checklist->getStyle("I2")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getCell("I2")->setValue($year);

	    //Set student ID
	    $checklist->mergeCells("A4:B4");
	    $checklist->getCell("A4")->setValue("Student ID");
	    $checklist->getStyle("A4")->getFont()->setBold(True);
	    $checklist->mergeCells("C4:E4");
	    $checklist->getStyle("C4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("C4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	    $checklist->getCell("C4")->setValue(substr($studID, 0, 3).'-'.substr($studID, 3, 2).'-'.substr($studID, 5, 3));

	    //Set email
	    $checklist->mergeCells("G4:H4");
	    $checklist->getCell("G4")->setValue("Email");
	    $checklist->getStyle("G4")->getFont()->setBold(True);
	    $checklist->mergeCells("I4:L4");
	    $checklist->getStyle("I4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getCell("I4")->setValue($email);
	    
	    //Set advisor
	    $checklist->mergeCells("A6:B6");
	    $checklist->getCell("A6")->setValue("Advisor");
	    $checklist->getStyle("A6")->getFont()->setBold(True);
	    $checklist->mergeCells("C6:E6");
	    $checklist->getStyle("C6")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getCell("C6")->setValue($advisor);
	    
	    //Set Last Updated
	    $checklist->mergeCells("G6:H6");
	    $checklist->getCell("G6")->setValue("Last Updated");
	    $checklist->getStyle("G6")->getFont()->setBold(True);
	    $checklist->mergeCells("I6:L6");
	    $checklist->getStyle("I6")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getCell("I6")->setValue(date("m/d/y"));	
	}

	private function primary($checklist, $coursesTaken, $curriculum)
	{
	    //Set an array for section headers
	    //Get starting/title row for courses
	    $checklist->getStyle("A8:J8")->applyFromArray($this->titlestyle);	    
	    $checklist->getStyle("F8:J8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $checklist->mergeCells("A8:B8");
	    $checklist->mergeCells("C8:E8");
	    $checklist->getCell("A8")->setValue("COURSE");
	    $checklist->getCell("C8")->setValue("PREREQUISITES");
	    $checklist->getCell("F8")->setValue("SCH");
	    $checklist->getCell("G8")->setValue("TERM");
	    $checklist->getCell("H8")->setValue("YEAR");
	    $checklist->getCell("I8")->setValue("*");
	    $checklist->getCell("J8")->setValue("GRADE");

	    //for every course in the curriculum
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
	    $row = 10;
	    $prevCType = NULL;
	    foreach ($requiredCourses as $reqCourse)
	    {
	    	//Grab course name
		$cName = $reqCourse->getName();
		$cNum  = strpbrk($cName, "0123456789");
		$cType = substr($cName, 0, strpos($cName, $cNum));

		//Put course name into checklist
		if (strcmp($cType, $prevCType) != 0)
		{
	    		if ($prevCType != NULL)
			{
	    			$row++;
				$checklist->getStyle("A$row:J$row")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$row++;
			}
			$checklist->getCell("A$row")->setValue($cType);
			$prevCType = $cType;
		}
		$checklist->getCell("B$row")->setValue($cNum);

		//Input Prerequisites
		$course = new Course_model();
//====================================================================== POTENTIAL ISSUE, ignoring valid courses after the first one
		$course->loadPropertiesFromPrimaryKey($reqCourse->getValidCourseIDs()[0]);
		foreach ($course->getPrerequisiteCourses() as $preReq)
		{
			$cell = $checklist->getCell("C$row");
			$cell->setValue($cell->getValue()." ".$preReq->getCourseName());
		}
		
		//Put in all course credit information and term/year
	        $checklist->getStyle("F$row:J$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		foreach ($coursesTaken as $key=>$taken)
			foreach ($reqCourse->getValidCourseIDs() as $prereqID)
				if ($prereqID == $taken[0]->getCourse()->getCourseID())
				{
					$checklist->getCell("H$row")->setValue($taken[0]->getAcademicQuarter()->getYear());
					$term = NULL;
					switch ($taken[0]->getAcademicQuarter()->getName())
					{
						case 'Fall':   $term = 'F'; break;
						case 'Winter': $term = 'W'; break;
						case 'Summer': $term = 'Su'; break;
						case 'Spring': $term = 'Sp'; break;
						default: $term = '?'; break;
					}
					$checklist->getCell("G$row")->setValue($term);
					$grade = NULL;
					switch ($taken[1])
					{
						case 4: $grade = 'A'; break;
						case 3: $grade = 'B'; break;
						case 2: $grade = 'C'; break;
						case 1: $grade = 'D'; break;
						case 0: $grade = 'F'; break;
						default: $grade = $taken[1];
					}
					$checklist->getCell("J$row")->setValue($grade);
					unset($coursesTaken[$key]);
				}

		//Put in astrik if it's a prereq of another course

		$row++;
	    }

	    //Set borders between columns for course information
	    $checklist->getStyle("B9:B$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("F9:F$row")->getBorders()->getLeft() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("F9:F$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("G9:G$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("H9:H$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("I9:I$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->getStyle("J9:J$row")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    //Merge preresuiqite cells
	    for ($i = 0; $i <= $row; $i++)
	    	$checklist->mergeCells("C$i:E$i");
	}
}
