<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
	//Class Variables
	private $titlestyle = array(
	 'fill' => array(
		  'type'  => PHPExcel_Style_Fill::FILL_SOLID, 
		  'color' => array('rgb' => 'B0B0B0')),
	 'font' => array('bold' => true),
	 'borders' => array('allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
	);

	//Main Function
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
	    
	    //Generate course checklist sheet
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
			
	    $this->generatechecklist($checklist, $user, $curriculum);

	    //Generate advisor checklist sheet
	    $advcheck = $Excel->createSheet(NULL, 1);
	    $advcheck->setTitle("Advisor Checklist");
	    $this->generateadvchecklist($advcheck);

	    //Download file object (PDF or XLS)
	    $objWriter = PHPExcel_IOFactory::createWriter($Excel, 'Excel5');
	    header("Content-type: application/vnd.ms-exel");
	    header("Content-Disposition: attachment; filename=test.xls");
	    $objWriter->save('php://output');
	}

	//Generate Advisor Checklist
	private function generateadvchecklist($advlist)
	{
	    //Set column dimensions
	    $advlist->getColumnDimension('A')->setWidth(65);
	    for ($i = 'B'; $i < 'Q'; $i++)
	    	$advlist->getColumnDimension("$i")->setWidth(6.5);
	    
	    //Set Styles for cells
	    $advlist->getStyle("A1:P14")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $advlist->getStyle("A1:P14")->getFont()->setBold(true);
	    $advlist->getStyle("A1:P2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $advlist->getStyle("A1:P2")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	    $advlist->getStyle("A1:A14")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	 
	    //Set background for header cells
	    $advlist->getStyle("B1:P2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    					  ->getStartColor()->setRGB('EEEEEE');
	    $advlist->getStyle("A3:A14")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    					   ->getStartColor()->setRGB('EEEEEE');

	    //Set up top row (header) cells
	    $advlist->mergeCells("A1:A2");
	    for ($y = 0, $fc = 'B', $lc = 'D'; $y < 5; $y++)
	    {
		$advlist->mergeCells("$fc".'1:'."$lc".'1');
		$advlist->getCell("$fc".'2')->setValue("F");  $fc++; $lc++;
		$advlist->getCell("$fc".'2')->setValue("W");  $fc++; $lc++;
		$advlist->getCell("$fc".'2')->setValue("Sp"); $fc++; $lc++;
	    }
	
	    //Set text for row titles (Should be hard coded)
	    $advlist->getStyle("A1:A14")->getAlignment()->setWrapText(true);
	    $advlist->getCell("A14")->SetValue("The student has been released on BOSS or CICS screen 7R3.");
	    $advlist->getCell("A1")->setValue("Put your initials in the appropriate cell when completed");
	    $advlist->getCell("A3")->SetValue("The term, year, and grade of courses already taken have been updated on the check sheet.");
	    //Style needed for EVERY (9) following rich text run
	    $fontstyle = new PHPExcel_Style_Font();
	    $fontstyle->setName("Arial")->setSize(10)->setBold(true);
	    //Cell A4 -- A lot of code to make a single word green
	    $text = new PHPExcel_RichText();
	    $run1 = $text->createTextRun("The term and year of current (ongoing) courses have been highlighted in ");
	    $run1->setFont($fontstyle);
	    $run2 = $text->createTextRun("green");
	    $run2->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_GREEN))->setName("Arial")->setSize(10)->setBold(true);
	    $run3 = $text->createTextRun(" on the check sheet.");
	    $run3->setFont($fontstyle);
	    $advlist->getCell("A4")->SetValue($text);
	    //Cell A5 -- A lot of code to make a single word yellow
	    $text = new PHPExcel_RichText();
	    $run1 = $text->createTextRun("The term and year of courses scheduled for the next term are highlighted in ");
	    $run1->setFont($fontstyle);
	    $run2 = $text->createTextRun("yellow");
	    $run2->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_YELLOW))->setName('Arial')->setSize(10)->setBold(true);
	    $run3 = $text->createTextRun(" on the check sheet.");
	    $run3->setFont($fontstyle);
	    $advlist->getCell("A5")->SetValue($text);
	    //Cell A6 -- A lot of code to make a single word red
	    $text = new PHPExcel_RichText();
	    $run1 = $text->createTextRun("Problems have been highlighted in ");
	    $run1->setFont($fontstyle);
	    $run2 = $text->createTextRun("red");
	    $run2->getFont()->setColor(new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_RED))->setName('Arial')->setSize(10)->setBold(true);
	    $run3 = $text->createTextRun(" on the check sheet and have been discussed with the student.");
	    $run3->setFont($fontstyle);
	    $advlist->getCell("A6")->SetValue($text);
	    $advlist->getCell("A7")->SetValue("All course substitutions have been approved either in the system (BOSS/CICS) or by the Program Chair.");
	    $advlist->getCell("A8")->SetValue("The student has taken all COES prerequisites (and earned a 'C' or better) for all COES courses on the advisement sheet.");
	    $advlist->getCell("A9")->SetValue("The student was informed of the requirement for a minimum 2.0 GPA on all CSC courses, including all attempts.");
	    $advlist->getCell("A10")->SetValue("The student was informed of the requirement for a minimum 2.0 GPA on the MATH 240 series, including all attempts.");
	    $advlist->getCell("A11")->SetValue("A sanity check was done to ensure that the student doesn't get into trouble with once-a-year classes and is subsequently unduly delayed.");
	    $advlist->getCell("A12")->SetValue("All deviances have been documented on a completed petition (one copy has been put in the Program Chair's box, and one copy has been sent to the Associate Dean of Undergraduate Studies).");
	    $advlist->getCell("A13")->SetValue("The check sheet has been uploaded/copied to the cloud space.");
	    $advlist->getCell("A14")->SetValue("The student has been released on BOSS or CICS screen 7R3.");
	    
	    //Set row heights
	    //  There's an issue with excel
	    //	  A row can't have autosized height, so it must be hard coded
	    $advlist->getRowDimension(3)->setRowHeight(14*2);
	    $advlist->getRowDimension(4)->setRowHeight(14*2);
	    $advlist->getRowDimension(5)->setRowHeight(14*2);
	    $advlist->getRowDimension(6)->setRowHeight(14*2);
	    $advlist->getRowDimension(7)->setRowHeight(14*2);
	    $advlist->getRowDimension(8)->setRowHeight(14*2);
	    $advlist->getRowDimension(9)->setRowHeight(14*2);
	    $advlist->getRowDimension(10)->setRowHeight(14*2);
	    $advlist->getRowDimension(11)->setRowHeight(14*2);
	    $advlist->getRowDimension(12)->setRowHeight(14*3);
	    $advlist->getRowDimension(13)->setRowHeight(14*1);
	    $advlist->getRowDimension(14)->setRowHeight(14*1);
	}

	//Generate Course Checklist
	private function generatechecklist($checklist, $user, $curriculum)
	{
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
	    $checklist->getColumnDimension('P')->setWidth(8.2);

	    $this->checklistheader($checklist, $user->getName(), $user->getUserID(), 
			  $user->getAdvisor(), "2014-15", $user->getEmailAddress());

	    $coursesTaken = $user->getAllCoursesTaken();
	    //Slot in the core curriculum courses
	    $this->checklistcore($checklist, $coursesTaken, $curriculum);
	
	    //Where to put every course that wasn't slotted
	    $this->checklistLeftOvers($checklist, $coursesTaken);
	}

	//Course Checklist Header
	private function checklistheader($checklist, $name, $studID, $advisor, $year, $email)
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

	//Course Checklist Core
	private function checklistcore($checklist, $coursesTaken, $curriculum)
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
			foreach ($reqCourse->getValidCourseIDs() as $reqID)
				if ($reqID == $taken[0]->getCourse()->getCourseID())
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

	//Course Checklist LeftOvers
	private function checklistLeftOvers($checklist, $coursesTaken)
	{
		$row = 8;
		$checklist->getStyle("L$row:P$row")->applyFromArray($this->titlestyle);
	        $checklist->getStyle("M$row:P$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$array = array('ADDITIONAL COURSE', 'SCH', 'TERM', 'YEAR', 'GRADE');
		$checklist->fromArray($array, NULL, 'L8');
	}
}
?>
