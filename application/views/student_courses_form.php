<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Modify Student Courses</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
		<link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
		<style>
	
			body {
				padding-top: 60px;
			}
	
			.container {
				color: black;
				text-align: left;
			}
			
			.container * {
				text-align: left;
			}

		</style>
    </head>
    <body>
	<?php include_once('application/views/Templates/navbar.php'); ?>
	<div class="container">
        <form action="<?php echo site_url('User/addCourseSections'); ?>" method="POST">
            <input type="hidden" name="studentID" value="<?php echo $sID; ?>" />
           
            <?php
            $student = new User_model();
            $student->loadPropertiesFromPrimaryKey($sID);
            $userTakenCourses = $student->getAllCoursesTaken();


            $coursesTakenIDs = array();

            foreach ($userTakenCourses as $section) {
                array_push($coursesTakenIDs, $section[0]->getCourseSectionID());
            }
               
            foreach ($courseData as $course) {
                echo '<input type="checkbox" name="'.$course['sectionID'].'" value="1"';
                $grade = 4;
                if (in_array($course['sectionID'], $coursesTakenIDs)) {
                    echo 'checked';
                    $courseModel = new Course_section_model();
                    $courseModel->loadPropertiesFromPrimaryKey($course['sectionID']);
                    $grade = $student->getGradeForCourseSection($courseModel);                   
                }
                echo '/>' . $course['courseName'] . ' ';
                echo '<select name="' . $course['sectionID'] . 'grade">';
                if ($grade < 5 && $grade >= 0) {
                    if($grade == 4){
                        echo '<option value="4" selected>A</option>';
                    }   else {
                        echo '<option value="4">A</option>';
                    }
                    if($grade == 3){
                        echo '<option value="3" selected>B</option>';
                    }   else {
                        echo '<option value="3">B</option>';
                    }
                    if($grade == 2){
                        echo '<option value="2" selected>C</option>';
                    }   else {
                        echo '<option value="2">C</option>';
                    }
                    if($grade == 1){
                        echo '<option value="1" selected>D</option>';
                    }   else {
                        echo '<option value="1">D</option>';
                    }
                    if($grade == 0){
                        echo '<option value="0" selected>F</option>';
                    }   else {
                        echo '<option value="0">F</option>';
                    }
                }
                echo '</select>';
                echo '  Quarter: ' . $course['quarterName'] . ' ' . $course['quarterYear'];
                echo '  Section: ' . $course['sectionNum'];
                echo '</br>';
            }
            ?>
            <input type='submit' name='Submit' />
        </form>
		</div>
		<?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>



