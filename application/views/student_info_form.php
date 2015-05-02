<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title>Select Advisor and Curriculum</title>
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
			<p><b>Please select a curriculum.</b></p><br/>
			<form action="<?php echo site_url('User/submitStudentInfoForm/'.$uID); ?>" method="POST"> 
				
					<?php
                                        $student = new User_model;
                                        $student->loadPropertiesFromPrimaryKey($uID);
                                        $studentCurriculms = $student->getCurriculums();
					$Curriculums = $this->Curriculum_model->getAllCurriculums();
					foreach ($Curriculums as $Curriculum) {
						$id = $Curriculum->getCurriculumID();
						$slotName = $Curriculum->getName();
						echo '<input type="checkbox" value="true" name="Curriculum' . $id . '"';
                                                        if(in_array($Curriculum, $studentCurriculms)) { echo 'checked'; }
                                                        echo '/> '.$slotName.'</br>';
                                                }
					?>
				<p><b>Please select an advisor.</b></p><br/>
				<select name="advisorID">
					<?php
					$Advisors = $this->User_model->getAllAdvisors();
					foreach($Advisors as $Advisor) {
						$id = $Advisor->getUserID();
						$slotName = $Advisor->getName();
						echo '<option value="' . $id . '">' . $slotName .'</option>';
					}
					?>
				</select>
				<input type="submit" value="NEXT" />
			</form>
		</div>
		<?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>

