<?php
echo "<font color=\"blue\">";
echo "<h1>BUTTON FOR TESTING EXPORT</h1>";

echo '<button type="button">';
echo "<a href=\"index.php/checklistexport/index/10210698\">Download</a>";
echo '</button>';

echo "<h1>BUTTON FOR TESTING ACTIVATION</h1>";

echo '<button type="button">';
echo "<a href=\"index.php/activation/index/10210698\">Download</a>";
echo '</button>';

include 'curriculum_choice.php';
include 'curriculum_edit.php';
include 'course_slot_edit.php';
echo "</font>";

?>
