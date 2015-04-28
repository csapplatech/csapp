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
	
	private $borderstyle = array(
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
	    
	    $curriculums = $user->getCurriculums();
	    foreach ($curriculums as $c)
	    	if ($c->getCurriculumType() == Curriculum_model::CURRICULUM_TYPE_DEGREE)
			$curriculum = $c;
	    $curriculums = array(new Curriculum_Model());
	    $curriculums[0]->loadPropertiesFromPrimaryKey($curriculumID);

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
	    $this->generatechecklist($checklist, $user, $curriculums);
	    
	    //Generate advisor checklist sheet
	    $advcheck = $Excel->createSheet(NULL, 1);
	    $advcheck->setTitle("Advisor Checklist");
	    $this->generateadvchecklist($advcheck);
	    
	    //Generate Quarter View
	    $qview = $Excel->createSheet(NULL, 2);
	    $qview->setTitle("Quarter View");
	    $this->generate_quarter_view($qview, $user, $curriculums);

	    //Download file object (PDF or XLS)
	    $Excel->setActiveSheetIndex(0);
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
	private function generatechecklist($checklist, $user, $curriculums)
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
	    $checklist->getColumnDimension('M')->setWidth(8.2);
	    $checklist->getColumnDimension('N')->setWidth(8.2);
	    $checklist->getColumnDimension('O')->setWidth(8.2);
	    $checklist->getColumnDimension('P')->setWidth(8.2);

	    $this->checklistheader($checklist, $user->getName(), $user->getUserID(), 
			  $user->getAdvisor()->getName(), "2014-15", $user->getEmailAddress());

	    $coursesTaken = $user->getAllCoursesTaken();
	    $this->checklistcore($checklist, $coursesTaken, $curriculums);
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
	private function checklistcore($checklist, $coursesTaken, $curriculums)
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

	    $degree;
	    foreach ($curriculums as $c)
	    	if ($c->getCurriculumType() == Curriculum_model::CURRICULUM_TYPE_DEGREE)
			$degree = $c;
	    if (!isset($degree))
	    {
	    	echo "ERROR NO CURRICULUM SET FOR USER";
		$test();
	    }

	    //for every course in the curriculum
	    $requiredCourses = $degree->getCurriculumCourseSlots();
	    $row = 10;
	    $prevCType = NULL;

	    $reqCour = array();
	    foreach ($requiredCourses as $c)
	    	$reqCour[$c->getName()] = $c;
	    ksort($reqCour);
	    $requiredCourses = $reqCour;		
		
	    foreach ($requiredCourses as $reqCourse)
	    {
	    	$cType = "cType";
		$cNum  = "cNum";
	    	//Grab course name
		$cName = $reqCourse->getName();
		$cType = $cName;
		$cNum  = strpbrk($cName, "0123456789");
		if ($cNum == FALSE) //Failed to find numbers in course name
			$cNum = "";
		else
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
			foreach ($reqCourse->getValidCourseIDs() as $reqkey=>$prereqID)
			{
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
						default: $term = '?';
					}
					$checklist->getCell("G$row")->setValue($term);
					$checklist->getCell("J$row")->setValue($taken[1]);
					unset($coursesTaken[$key]);
				}
				
				if ($checklist->getCell("F$row")->getValue() == NULL)
					$checklist->getCell("F$row")->setValue($taken[0]->getHours());
			}
		//Put in astrik if it's a prereq of another course
		

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
		$row++;
	    }
	    
	    //Add extra blank at the bottom for astectics
	    $checklist->getStyle("A$row:B$row")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $checklist->mergeCells("C$row:E$row");
	    for ($i = 'C'; $i <= 'J'; $i++)
	    {
	    	$checklist->getStyle("$i$row")->getBorders()->getRight() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	$checklist->getStyle("$i$row")->getBorders()->getLeft()  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	$checklist->getStyle("$i$row")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    }
	    
	    //Put all left over classes into Additional Courses
	    $checklist->getStyle("L8:P8")->applyFromArray($this->titlestyle);	
	    $checklist->getStyle("L8:P8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    $checklist->getCell("L8")->setValue("ADDITIONAL COURSE");
	    $checklist->getCell("M8")->setValue("SCH");
	    $checklist->getCell("N8")->setValue("TERM");
	    $checklist->getCell("O8")->setValue("YEAR");
	    $checklist->getCell("P8")->setValue("GRADE");

	    //Sort courses for putting into Additional Course
	    $courses = array();
	    foreach ($coursesTaken as $key=>$taken)
	    {
	    	$course = $taken[0]->getCourse();
	    	$courses[$course->getCourseName()." ".$course->getCourseNumber()] = $taken;
	    }
	    ksort($courses);

	    //Insert into additional course
	    $irow = 9;
	    $cols = array('L', 'M', 'N', 'O', 'P');
	    foreach ($cols as $col)
	    {
	    	$checklist->getStyle("$col$irow")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	$checklist->getStyle("$col$irow")->getBorders()->getLeft() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    }
	    $irow++;
	    foreach ($courses as $key=>$taken)
	    {
	    	foreach ($cols as $col)
	    	{
	    		$checklist->getStyle("$col$irow")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    		$checklist->getStyle("$col$irow")->getBorders()->getLeft() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	}
	    	$checklist->getStyle("M$irow:P$irow")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	    	$course = $taken[0]->getCourse();
	    	$checklist->getCell("L$irow")->setValue($key);
	    	$checklist->getCell("O$irow")->setValue($taken[0]->getAcademicQuarter()->getYear());
	    			
	    	$term = NULL;
	    	switch ($taken[0]->getAcademicQuarter()->getName())
	    	{
	    		case 'Fall':   $term = 'F'; break;
	    		case 'Winter': $term = 'W'; break;
	    		case 'Summer': $term = 'Su'; break;
	    		case 'Spring': $term = 'Sp'; break;
	    		default: $term = '?';
	    	}
	    	$checklist->getCell("N$irow")->setValue($term);
	    	$checklist->getCell("P$irow")->setValue($taken[1]);
	    
	    	if ($checklist->getCell("M$irow")->getValue() == NULL)
	    		$checklist->getCell("M$irow")->setValue($taken[0]->getHours());
	    	$irow++;
	    }
	    
	    $cols = array('L', 'M', 'N', 'O', 'P');
	    foreach ($cols as $col)
	    {
	    	$checklist->getStyle("$col$irow")->getBorders()->getRight() ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	$checklist->getStyle("$col$irow")->getBorders()->getLeft()  ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    	$checklist->getStyle("$col$irow")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    }
	}

	//Generate quarter view
	private function generate_quarter_view($sheet, $user, $curriculum)
	{
		//Set column widths
		$sheet->getColumnDimension('A')->setWidth(18);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(6);
		$sheet->getColumnDimension('D')->setWidth(1.5);
		$sheet->getColumnDimension('E')->setWidth(18);
		$sheet->getColumnDimension('F')->setWidth(30);
		$sheet->getColumnDimension('G')->setWidth(6);
		$sheet->getColumnDimension('H')->setWidth(1.5);
		$sheet->getColumnDimension('I')->setWidth(18);
		$sheet->getColumnDimension('J')->setWidth(30);
		$sheet->getColumnDimension('K')->setWidth(6);
		$sheet->getColumnDimension('L')->setWidth(1.5);
		
		//Generate header
		$this->generate_quarter_view_header($sheet, $user->getName(), $user->getUserID(), $user->getEmailAddress());
	
		//Generate Core
		$this->generate_quarter_view_core($sheet, $user, $curriculum);
	}
		
	//Quarter View Core
	private function generate_quarter_view_core($sheet, $user, $curriculums)
	{
	    $degree;
	    foreach ($curriculums as $c)
	    	if ($c->getCurriculumType() == Curriculum_model::CURRICULUM_TYPE_DEGREE)
		    $degree = $c;
	    
	    if (!isset($degree))
	    {
		echo "ERROR USER CURRICULUM DEGREE ISN'T SET";
		$test();
	    }

	    //Organize courses by year/quarter in an array $arr[$year][$quarter][$course]
	    $currcourses = $degree->getCurriculumCourseSlots();
	    $courses = array();
	    for ($i = 0; $i < 4; $i++)
		$courses[] = array(array(), array(), array(), array());
	    foreach ($currcourses as $cc)
	    {
		$year;
		$quarter;
		switch ($cc->getRecommendedYear())
		{
		   case Curriculum_Course_Slot_Model::YEAR_FRESHMAN:
			$year = 0; break;
		   case Curriculum_Course_Slot_Model::YEAR_SOPHOMORE:
			$year = 1; break;
		   case Curriculum_Course_Slot_Model::YEAR_JUNIOR:
			$year = 2; break;
		   case Curriculum_Course_Slot_Model::YEAR_SENIOR:
			$year = 3; break;
		   default:
			$year = 'Error';
		}
		switch ($cc->getRecommendedQuarter())
		{
		   case Academic_quarter_model::NAME_FALL:
			$quarter = 0; break;
		   case Academic_quarter_model::NAME_WINTER:
			$quarter = 1; break;
		   case Academic_quarter_model::NAME_SPRING:
			$quarter = 2; break;
		   case Academic_quarter_model::NAME_SUMMER:
			$quarter = 3; break;
		   default:
			$quarter = 'Error';
		}
			$courses[$year][$quarter][] = $cc;
		}

		//For every year set the header and do the quarters
		$row = 6;
		$years = array('FRESHMAN YEAR', 'SOPHOMORE YEAR', 'JUNIOR YEAR', 'SENIOR YEAR');
		foreach ($years as $ykey=>$year)
		{
		    $sheet->mergeCells("A$row:K$row");
		    $sheet->getStyle("A$row")->applyFromArray($this->titlestyle);	    
		    $sheet->getStyle("A$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		    $sheet->getCell("A$row")->setValue($year);
		    $row++;

		    //Get the amount of classes in largest quarter
		    //	This is for formating
		    $quartersize = 0;
		    foreach ($courses[$ykey] as $cc)
		    	if (sizeof($cc) > $quartersize)
			    $quartersize = sizeof($cc);

	  	    //For every quarter put in the header and all courses taken
		    $quarters = array('Fall Quarter', 'Winter Quarter', 'Spring Quarter');
		    $column   = array(array('A', 'B'), array('E', 'F'),   array('I', 'J'));
		    $startrow = $row;
		    foreach ($quarters as $qkey=>$quarter)
		    {
		    	$row = $startrow;
		    	$cols = $column[$qkey];
			//Put in quarter name header
		    	$sheet->mergeCells("$cols[0]$row:$cols[1]$row");
		        $style = $sheet->getStyle("$cols[0]$row");
			   $style->applyFromArray($this->borderstyle);
			   $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			   $style->getFont()->setBold(true);
			$sheet->getCell("$cols[0]$row")->setValue($quarters[$qkey]);
		        //Put in Credit header
		    	$cols[1]++;
		        $style = $sheet->getStyle("$cols[1]$row");
			   $style->applyFromArray($this->borderstyle);
			   $style->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			   $style->getFont()->setBold(true);
			$sheet->getCell("$cols[1]$row")->setValue('Cr');
		        $row++;

			//Add extra blank for the feels of the beatification
			$style = $sheet->getStyle("$cols[0]$row:$cols[1]$row")->applyFromArray($this->borderstyle);
			$row++;

			//Put in all the courses taken
			$strow = $row+1;
			$nameCol = $cols[0];
			$titleCol = ++$cols[0];
			$creditCol = $cols[1];
			foreach ($courses[$ykey][$qkey] as $cc)
			{
				$style = $sheet->getStyle("$nameCol$row:$creditCol$row");
				   $style->applyFromArray($this->borderstyle);
				$sheet->getCell("$nameCol$row")->setValue($cc->getName());
				$sheet->getCell("$titleCol$row")->setvalue($cc->getNotes());
				$credit = '?';
				if (isset($cc->getValidCourseIDs()[0]))
				{
					$temp = new Course_Section_Model();
					$temp->loadPropertiesFromPrimaryKey($cc->getValidCourseIDs()[0]);
					$credit = $temp->getHours();
				}
				$sheet->getStyle("$creditCol$row")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getCell("$creditCol$row")->setValue($credit);
				$row++;
			}

			//Fill in empty quarter courses lots with correct stlying
			for (; $row-$startrow <= $quartersize+1; $row++)
				$sheet->getStyle("$nameCol$row:$creditCol$row")->applyFromArray($this->borderstyle);
			
			//Added extra blank line to make the spreadsheet look sexy
			$sheet->getStyle("$nameCol$row:$creditCol$row")->applyFromArray($this->borderstyle);
	                $row++;
			
			//Slot of summing quarter's hours
			$sheet->getStyle("$nameCol$row:$creditCol$row")->applyFromArray($this->borderstyle);
			$strow -= 2; $enrow = $row-1;
			$sheet->getCell("$creditCol$row")->setValue('=SUM('."$creditCol"."$strow".':'."$creditCol"."$enrow".')');
			$row++;
		    }
		    $row++;
		}
	}

	//Quarter View Header
	private function generate_quarter_view_header($sheet, $name, $studID, $email)
	{
	    //Set name fields
	    $sheet->getCell("A2")->setValue("Name");
	    $sheet->getStyle("A2")->getFont()->setBold(True);
	    $sheet->mergeCells("B2:E2");
	    $sheet->getStyle("B2")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $sheet->getCell("B2")->setValue($name);

	    //Set student ID
	    $sheet->getCell("A4")->setValue("Student ID");
	    $sheet->getStyle("A4")->getFont()->setBold(True);
	    $sheet->mergeCells("B4:E4");
	    $sheet->getStyle("B4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $sheet->getStyle("B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	    $sheet->getCell("B4")->setValue(substr($studID, 0, 3).'-'.substr($studID, 3, 2).'-'.substr($studID, 5, 3));
	    
	    //Set Last Updated
	    $sheet->getCell("G4")->setValue("Date");
	    $sheet->getStyle("G4")->getFont()->setBold(True);
	    $sheet->mergeCells("I4:J4");
	    $sheet->getStyle("I4")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $sheet->getCell("I4")->setValue(date("m/d/y"));	

	    //Set email
	    $sheet->getCell("G2")->setValue("Email");
	    $sheet->getStyle("G2")->getFont()->setBold(True);
	    $sheet->mergeCells("I2:J2");
	    $sheet->getStyle("I2")->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	    $sheet->getCell("I2")->setValue($email);
	}
}
?>
