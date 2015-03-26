<?php
require_once('application/libraries/phpexcel/PHPExcel/IOFactory.php');

class Checklistexport extends CI_Controller
{
	//Funciton must be given the user and a curriculum
	public function index($userID = NULL, $curriculum = NULL, $type = "xls")
	{
	    //Assuming a user with classes is passed and curriculum
            //	Must be valid!
	    $this->load->model('User_model');
	    $user = new User_Model();
	    $user->loadPropertiesFromPrimaryKey($userID);

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
		"advisor" => NULL);
	    //Find and set user information (name, email, etc)
	    for ($row = 0; $row < count($cells); $row++)
	    	for ($col = 0; $col < count($cells[$row]); $col++)
		{
			$val = $checksheet->getCellByColumnAndRow($row, $col)->getValue();
			if (strcasecmp($val, "name")    == 0)
				$location["name"]    = array($row, $col);
			if (strcasecmp($val, "advisor") == 0)
				$location["advisor"] = array($row, $col);
			if (strcasecmp($val, "email")   == 0)
				$location["email"]   = array($row, $col);
		}
	    //Default to two cells over from label, but should do a search instead of static
	    $location["name"] = $checksheet->getCellByColumnAndRow($location["name"][0]+2, $location["name"][1]);
	    $location["email"] = $checksheet->getCellByColumnAndRow($location["email"][0]+2, $location["email"][1]);
	    $location["advisor"] = $checksheet->getCellByColumnAndRow($location["advisor"][0]+2, $location["advisor"][1]);
	    
	    $location["name"]->setValue($user->getName());
	    $location["email"]->setValue($user->getEmailAddress());
	    $location["advisor"]->setValue($user->getAdvisor());
 
	    
	    $checksheet	->setCellValue('I2', '2015')			//Catalog year
			->setCellValue('C4', '698-42-478')		//Student ID
			->setCellValue('I6', date(DATE_RFC2822));	//Last Updated
	    /*
            //get usable transcript info
            //From Users:
            $courseSections = $user->getAllCoursesTaken(); //array of course sections

            //From Course Section Model:
            foreach ($courseSections as $courseSection) 
                    array_push($courses, $courseSection->getCourse()); //creates array of taken courses ids

            //get usable curriculum info
            //From curriculum model:
            // need this $curriculum = $user->getCurriculum();
            $courseSlots = $curriculum->getCurriculumCourseSlots(); //array of course slots

            //put them together
            foreach ($courseSlots as $currentCourseSlot) {
                    $courseTaken = FALSE;

                    $validCourses = $currentCourseSlot->getValidCourseIDS(); //From course slot model

                    foreach ($validCourses as $currentValidCourse) 
                            //need to be able to check for grades
                            $currentCourseSlot = in_array($currentValidCourse, $courses [, bool $strict = FALSE] );

                    if ($courseTaken)
                            //check off checklist
            }    */
            
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
