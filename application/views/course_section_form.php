<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title><?=  ucfirst($action) ?> Course Section</title>
        <link rel="icon" href="<?php echo IMG . '/icon.ico'; ?>">
    </head>
    <body>
        <?php include_once('application/views/Templates/navbar.php'); ?>
        <form action="<?php echo site_url('User/' . $action . 'CourseSection/'); ?>" method="POST" >
            <input type="hidden" name="sID" value="<?=$sID ?>" />
            <?php if ($action == 'add') { ?>
                <select name="sectionID" selected="0">
                    <option value="0">Select a Course Section</option>
                    <?php
                    foreach ($sections as $section) {
                        $quarter = $section->getAcademicQuarter();
                        echo '<option value="' . $section->getCourseSectionID() . '" >';
                        echo $slotName . ' ' . $section->getSectionName() . ' ' . $quarter->getName() . ' ' . $quarter->getYear() . '</br>';
                        echo '</option>';
                    }
                    ?>
                </select>
                <select name="grade">
                    <option value="N/A">N/A</option>
                    <option value="0">F</option>
                    <option value="1">D</option>
                    <option value="2">C</option>
                    <option value="3">B</option>
                    <option value="4">A</option>
                </select>
                <input type="submit" value="Add Section" />
                <?php
            } else {
                ?>
                <input type="hidden" name="sectionID" value="<?=$section->getCourseSectionID() ?>" />
                <table cellPadding="15" allignment="center" border='1'>
                    <tr><th>Course Section</th><th>Quarter</th><th>Grade</th></tr>
                    <tr><td>
                            <?php echo $section->getCourse()->getCourseName().' '.$section->getCourse()->getCourseNumber().'-'.$section->getSectionName(); ?>
                        </td>
                        <td>
                            <?=$quarter->getName() . ' ' . $quarter->getYear()?>   
                        </td>
                        <td>
                            <?=$grade ?>   
                        </td>    
                    </tr>
                </table>
                <input type="submit" value="Remove Section" />
                <?php
            }
            ?>
        </form>
    </body>
</html>
