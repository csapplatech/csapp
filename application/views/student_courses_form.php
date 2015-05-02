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
        <link rel="icon" href="<?php echo IMG . '/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS . '/magic-bootstrapV2_1.css'; ?>" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>

        <style>
            body {
                padding-top: 60px;
                color: black;
            }

            .container {
                color: black;
                text-align: center;
            }

            .container * {
                text-align: center;
            }

            br{
                text-align: center;
                padding-top: 5px;
            }

        </style>
    </head>
    <body>
        <?php include_once('application/views/Templates/navbar.php'); ?>
        <div class="container">
<!--            <form action="<?php echo site_url('User/submitCourseListQuery'); ?>" method="POST" >
                <input type="text" name="searchStr" class="form-control" placeholder="Search by Name..." style="margin-bottom:5px" autofocus /> 
                <input type="submit" value="Search" />
            </form> -->
            <?php
            ?>
            <br/><br/>

            <table cellPadding="50" border="3" >
                <tr><th>Action</th><th>Name</th><th>Quarter</th><th>Section</th><th>Grade</th></tr>
                <?php
                //Character to display when there is no section for a course slot.
                $unassigned = '-';
                foreach ($curriculumSlots as $currSlot) {
                    $slotName = $currSlot->getName();
                    $sectionID = 0;
                    //getting wrong values
                    if (isset($filledSlots[$slotName])) {
                        //ID of course section that fills the current curriculum slot.
                        $sectionID = $filledSlots[$slotName];
                    }
                    $slotID = $currSlot->getCurriculumCourseSlotID();
                    //Setup action for button next to currriculum slot info.
                    if ($sectionID > 0) {
                        $submitAction = site_url('User/prepareRemoveCourseSection/' . $sectionID);
                    } else {
                        $submitAction = site_url('User/prepareAddCourseSection/' . $slotID);
                    }
                    echo '<form action="' . $submitAction . '" method="POST">';
                    echo '<input type="hidden" name="sID" value="' . $sID . '" />';
                    echo '<tr><td><input type="submit"';
                    if ($sectionID > 0) {
                        echo 'value="Remove Course" /></tc></td>';
                        $student = new User_model;
                        $student->loadPropertiesFromPrimaryKey($sID);
                        $section = new Course_section_model;
                        $section->loadPropertiesFromPrimaryKey($sectionID);
                        $quarterName = $section->getAcademicQuarter()->getName();
                        $quarterYear = $section->getAcademicQuarter()->getYear();
                        echo '<td>' . $currSlot->getName() . '</td>'
                        . '<td>' . $quarterName . $quarterYear . '</td>'
                        . '<td>' . $section->getSectionName() . '</td>'
                        . '<td>' . $student->getGradeForCourseSection($section) . '</td>';
                    } else {
                        echo 'value="   Add Course   " /></tc></td>'; // Need Curriculum Slot
                        echo '<td>' . $slotName . '</td>' . '<td>' . $unassigned . '</td>' . '<td>' . $unassigned . '</td>' . '<td>' . $unassigned . '</td>';
                    }
                    echo '</tr>';
                }
                ?>
            </table>
            <br/>
            <button type="button">
                <a href="<?php echo site_url('User/index'); ?>">User Management</a>
            </button>   
        </div>
        <?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>
