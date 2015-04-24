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
	    $checklist->getColumnDimension('P')->setWidth(6);

	    $this->checklistheader($checklist, $user->getName(), $user->getUserID(), 
			  $user->getAdvisor()->getName(), "2014-15", $user->getEmailAddress());

	    $coursesTaken = $user->getAllCoursesTaken();
	    $this->checklistcore($checklist, $coursesTaken, $curriculum);
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
?>
