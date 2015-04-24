<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Select Advisor and Curriculum</title>
    </head>

    <body>
        <p><b>Please select a curriculum.</b></p><br/>
        <form action="<?php echo site_url('User/submitStudentInfoForm/'.$uID); ?>" method="POST"> 
            
            <select name="curriculumID" >
                <?php
                $Curriculums = $this->Curriculum_model->getAllCurriculums();
                foreach ($Curriculums as $Curriculum) {
                    $id = $Curriculum->getCurriculumID();
                    $name = $Curriculum->getName();
                    echo '<option value="' . $id . '">' . $name . '</option>';
                }
                ?>
            </select>
            <p><b>Please select an advisor.</b></p><br/>
            <select name="advisorID">
                <?php
                $Advisors = $this->User_model->getAllAdvisors();
                foreach($Advisors as $Advisor) {
                    $id = $Advisor->getUserID();
                    $name = $Advisor->getName();
                    echo '<option value="' . $id . '">' . $name .'</option>';
                }
                ?>
            </select>
            <input type="submit" value="NEXT" />
        </form>
    </body>
</html>

