<?php
//=============================================================================
//This program was translated from python by Christian Dean and additional functionality
//was added by Sean Manteris for Senior Capstone. Spring of 2015
//=============================================================================
/* Checks for existence of $substring within $string.
* Returns true if $substring was found in $string.
* Returns false otherwise.
*/

function contains($substring, $string)
{
	$pos = strpos($string, $substring);
	if ($pos === FALSE) {
		return false;
	}
	else {
		return true;
	}
}

//=============================================================================
//This function takes a term value like 131 and translates it to what it represents
//in this case Fall of 2012.
//=============================================================================
function parseterm($term){
	$insertstring = "INSERT INTO AcademicQuarters (AcademicQuarterID, Name, Year) VALUES('".$term."'";
	if (substr($term, 2 , 1)==1){
		$temp = (int)substr($term,0 ,2)-1;
		if ($temp < 10)
			$temp = '0'.(string)$temp;
		else
			$temp = (string)$temp;
		$insertstring = $insertstring.",'Fall','20".$temp."');";
	}
	if (substr($term, 2 , 1)==2)
		$insertstring = $insertstring.",'Winter','20". (int)substr($term,0 ,2)."');";
	if (substr($term, 2 , 1)==3)
		$insertstring = $insertstring.",'Spring','20". (int)substr($term,0 ,2)."');";
	if (substr($term, 2 , 1)==4)
		$insertstring = $insertstring.",'Winter','20". (int)substr($term,0 ,2)."');";

	return $insertstring;
}
//=============================================================================
//This function checks to see if a record already exists in the database, and how many.
//If the record doesn't exist it is added.
//=============================================================================
function checkinsertdatabase($conn, $sql, $insertquery){


	$result = mysqli_query($conn, $sql)or die(mysqli_error($conn)); 
	$primaryID = '';
	
	//if one result is returned then the record already exists
	if (mysqli_num_rows($result) >= 1){
		$row = mysqli_fetch_row($result);
		$primaryID = (string)$row[0];
	}
	//if zero records are returned it does not exist
	else if (mysqli_num_rows($result) == 0){
		$result = mysqli_query($conn, $insertquery)or die(mysqli_error($conn));
		$primaryID = $conn->insert_id;
		//if(!empty($result)) echo $insertquery."\n";
	}
	return (string)$primaryID;
}

function ParseFile($filePath){
	
	//=============================================================================
	// Open file
	//=============================================================================

	$file = fopen($filePath, "r");

	if ($file == false) {
		return "fopen(): Error in opening file.";
		exit();
	}
	//=============================================================================
	//setup and check sql connection
	//=============================================================================
	$servername = "127.0.0.1";
	$username = "root";
	$password = ""; 
	$dbname = "csc_webapp";

	// Create connection
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	
	// Check connection
	if (!$conn) 
		die("Connection failed: " . mysqli_connect_error());
	//=============================================================================
	// Read each line into array $lines
	//=============================================================================

	// Disable MySQL autocommit
	mysqli_autocommit($conn, false);
	
	// Begin a MySQL transaction
	mysqli_begin_transaction($conn);
	
	$lines = file($filePath, FILE_IGNORE_NEW_LINES + FILE_SKIP_EMPTY_LINES);

	if ($lines == false) {
		
		mysqli_rollback($conn);
		
		mysqli_autocommit($conn, true);
	
		return "file(): Error reading lines from file.";
	}
	//
	$student_string = "";
	$student_cwid = "";
	$advisor_string = "";
	//=============================================================================
	/**
	 Main program.
	 */
	//=============================================================================

	foreach($lines as $line_num => $line) {
		if (contains("STUDENT NAME", $line)) {
			$student_name_line = explode(' ', $line);
			$student_string = "INSERT INTO Users (Name, UserID, EmailAddress) VALUES ('".(string)$student_name_line[4]." ".(string)$student_name_line[5]."'";
			$advisor_cwid = str_replace("-", "", (string)$student_name_line[count($student_name_line) - 8]);
		}

		if (contains("STUDENT NUMBER", $line)) {
			$student_cwid_line = explode(' ', $line);
			$student_cwid = str_replace("-", "", (string)$student_cwid_line[2]);
			$student_string = $student_string . ", '" . $student_cwid . "', '".$student_cwid."@latech.edu');";
			$advisor_string = "('" . (string)$student_cwid_line[count($student_cwid_line) - 4]
				.(string)$student_cwid_line[count($student_cwid_line) - 3] . " " . (string)$student_cwid_line[count($student_cwid_line) - 2] . "', ";
			$advisor_string ="INSERT INTO Users (Name, UserID, EmailAddress) VALUES".$advisor_string . $advisor_cwid . ", '".$advisor_cwid."@latech.edu');";

			// Users table for students
			$sql = "SELECT UserID FROM Users WHERE UserID='".$student_cwid."' LIMIT 1 ;";
			checkinsertdatabase($conn,$sql,$student_string);
			// Users table for Advisors
			$sql = "SELECT UserID FROM Users WHERE UserID='".$advisor_cwid."' LIMIT 1 ;";
			checkinsertdatabase($conn,$sql,$advisor_string);
			// StudentAdvisors table
			$sql = "SELECT StudentUserID FROM StudentAdvisors WHERE StudentUserID='".$student_cwid."' LIMIT 1;";
			$tempinsert = "INSERT INTO StudentAdvisors (StudentUserID, AdvisorUserID) VALUES"
				."(" . str_replace("-", "", (string)$student_cwid_line[2] . ", " . $advisor_cwid . ");");
			checkinsertdatabase($conn,$sql,$tempinsert);
			// UserRoles table for students
			$sql = "SELECT UserID FROM UserRoles WHERE UserID='".$student_cwid."' LIMIT 1 ;";
			$tempinsert = "INSERT INTO UserRoles (UserID, RoleID) VALUES"."( " . str_replace("-", "", (string)$student_cwid_line[2] . ", 4);");
			checkinsertdatabase($conn,$sql,$tempinsert);
			// UserRoles table for Advisors
			$sql = "SELECT UserID FROM UserRoles WHERE UserID='".$advisor_cwid."' LIMIT 1 ;";
			$tempinsert = "INSERT INTO UserRoles (UserID, RoleID) VALUES"."( " . (string)$advisor_cwid . ", 3);";
			checkinsertdatabase($conn,$sql,$tempinsert);

		}

		// This is to see if the student is a undergrad or graduate student
		// and is used to determine which classes are undergrad. This shiould be rewritten
		// to be based off the course number instead i.e. 500 and above is grad etc
		if (contains("LEVEL", $line)) {
			$level_line = explode(' ', $line);
			$level = (string)$level_line[10];
		}

		if (contains(".00", $line) and !contains("***", $line) and !contains("           MOST ", $line) and !contains("  GOOD STANDING         ", $line) 
			and !contains("DEGREE        ", $line)) {
			$student_courses_line = explode("  ", $line);
			if ($student_courses_line[0] != "") {
				$course_name = str_replace(" ", "", substr($student_courses_line[0], 0, 4));
				$course_number = str_replace(" ", "", substr($student_courses_line[0], 5, 4));
				$section_name = str_replace(" ", "", substr($student_courses_line[0], 10, 4));
				$course_title = substr($student_courses_line[0], 14);
				$hours = (int)(array_slice($student_courses_line, -5, 1) [0]);
				$grade = "";
				for($element=0; $element<=count($student_courses_line); $element++){
					if (empty($student_courses_line[$element-1]) and ctype_alpha(str_replace(" ", "", $student_courses_line[$element]))){
						$grade =str_replace(" ", "", $student_courses_line[$element]); 
					}
					if (empty($grade)){
						$grade="S";
					}
				}	

				//these next three lines are to determine whether the course is undergrad or graduate level
				$level = 1;
				if ((int)$course_number>500)
					$level=2;
				//create strings for the select and insert statements
				$selectquery="SELECT CourseID,CourseName,CourseNumber FROM Courses WHERE CourseName='".$course_name."' AND CourseNumber='".$course_number."';";
				$insertquery="INSERT INTO Courses ( CourseTypeID, CourseTitle, CourseName, CourseNumber) VALUES ("
					.$level.", '".$course_title."', '".$course_name."', '".$course_number."');";
				//This function checks to see if a record exists and if not inserts it then returns its primary key.
				$coursePrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);

				//AcademicQuarters
				$term=str_replace("\n", "", $student_courses_line[count($student_courses_line)-1]);
				$selectquery="SELECT AcademicQuarterID FROM AcademicQuarters WHERE AcademicQuarterID='".$term."';";
				$insertquery = parseterm($term); 
				checkinsertdatabase($conn, $selectquery, $insertquery);

				//CourseSections
				$selectquery="SELECT CourseSectionID,CourseID,SectionName FROM CourseSections 
					WHERE CourseID='".$coursePrimaryID."' AND SectionName='".$section_name."';";
				$insertquery="INSERT INTO CourseSections ( CourseID,SectionName,Hours,AcademicQuarterID) VALUES ("
					.$coursePrimaryID.", '".$section_name."', '".$hours."', '".$term."');";
				$courseSectionPrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);

				//StudentCourseSections
				//must update grades here
				$insertquery="INSERT INTO StudentCourseSections(StudentUserID,CourseSectionID,Grade) VALUES ("
					.$student_cwid.", '".$courseSectionPrimaryID."', '".$grade."') ON DUPLICATE KEY UPDATE Grade = '".$grade."';";
				$result = mysqli_query($conn, $insertquery)or die(mysqli_error($conn)); 

			}
			else{//this section parse the transfer courses as those lines have a different format
                if($student_courses_line[1]!=""){
                    $course_name=str_replace(" ", "", substr($student_courses_line[1], 0, 4));
                    $course_number = str_replace(" ", "", substr($student_courses_line[1], 5, 4));
                    $course_title = rtrim($student_courses_line[2]);
                    $hours = (int)(array_slice($student_courses_line, -4, 1) [0]);
                    //Each array created for each line is of variable length
                    //in order to fix this a filter is used and then a call to the remaining values.
                    $student_courses_line=array_values(array_filter($student_courses_line));
                    $schoolnumber = (int)(substr($student_courses_line[3],-7,7));
                    
                    //Course
                    //these next three lines are to determine whether the course is undergrad or graduate level
                    $level = 1;
                    if ((int)$course_number>500)
                        $level=2;
                    $selectquery="SELECT CourseID,CourseName,CourseNumber FROM Courses WHERE CourseName='"
                        .$course_name."' AND CourseNumber='".$course_number."';";
                    $insertquery="INSERT INTO Courses ( CourseTypeID, CourseTitle, CourseName, CourseNumber) VALUES ("
                        .$level.", '".$course_title."', '".$course_name."', '".$course_number."');";
                    $coursePrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);

                    //StudentTransferCourses table insert if it does not exist
                    $selectquery="SELECT StudentTransferCourseID,StudentUserID,CourseName, UniversityID, Hours FROM StudentTransferCourses "
                        ."WHERE StudentUserID=".$student_cwid." AND CourseName='".$course_title."' AND UniversityID=".$schoolnumber
                        ." AND Hours=".$hours.";";
                    $insertquery="INSERT INTO StudentTransferCourses ( StudentUserID,CourseName,UniversityID,Hours) VALUES ("
                        .$student_cwid.", '".$course_title."', '".$schoolnumber."', '".$hours."');";
                    $studentTransferPrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);

                    //StudentTransferCourseEquivilentCourses table insert if it does not exist
                    $selectquery="SELECT StudentTransferCourseID,EquivilentCourseID FROM StudentTransferCourseEquivilentCourses".
                        " WHERE StudentTransferCourseID='".$studentTransferPrimaryID."' AND EquivilentCourseID='".$coursePrimaryID."';";
                    $insertquery="INSERT INTO StudentTransferCourseEquivilentCourses( StudentTransferCourseID,EquivilentCourseID) VALUES ("
                        .$studentTransferPrimaryID.", '".$coursePrimaryID."');";
                    $studentTransferPrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);
                }
                else{//these are the transfer courses which are not yet mapped and only partial information is available for
                    //print_r ($student_courses_line);
                    $course_title = $student_courses_line[6];
                    $hours = (int)(array_slice($student_courses_line, -4, 1) [0]);
                    //Each array created for each line is of variable length
                    //in order to fix this a filter is used and then a call to the remaining values.
                    $student_courses_line=array_values(array_filter($student_courses_line));
                    $schoolnumber = (string)(substr($student_courses_line[2],-7,7));

                    //StudentTransferCourses table insert if it does not exist
                    $selectquery="SELECT StudentUserID,CourseName, UniversityID, Hours FROM StudentTransferCourses
                        WHERE StudentUserID='".$student_cwid."' AND CourseName='".$course_title."' AND UniversityID='".$schoolnumber
                        ."' AND Hours='".$hours."';";
                    $insertquery="INSERT INTO StudentTransferCourses ( StudentUserID,CourseName,UniversityID,Hours) VALUES ("
                        .$student_cwid.", '".$course_title."', '".$schoolnumber."', '".$hours."');";
                    $studentTransferPrimaryID=checkinsertdatabase($conn, $selectquery, $insertquery);
                }
			}
		}
	}

	//=============================================================================
	// Close file and SQL connection
	//=============================================================================
	mysqli_close($conn);
	fclose($file);
	
	mysqli_commit($conn);
	
	mysqli_autocommit($conn, true);
	
	return null;
	//=============================================================================
}
//mainprogram();
?>
