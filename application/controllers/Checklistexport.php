<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
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
}
