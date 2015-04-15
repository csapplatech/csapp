<h1>BUTTON FOR TESTING EXPORT</h1>

<?php
echo '<button type="button">';
echo "<a href=\"index.php/checklistexport/index/10210698\">Download</a>";
echo '</button>';
?>

<h1>BUTTON FOR TESTING ACTIVATION</h1>

<?php
echo '<button type="button">';
echo "<a href=\"index.php/activation/index/10210698\">Download</a>";
echo '</button>';

echo "<font color=\"blue\">";
include 'curriculum_choice.php';
include 'course_slot_choice.php';
include 'course_slot_edit.php';
echo "</font>";

?>
