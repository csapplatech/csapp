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
