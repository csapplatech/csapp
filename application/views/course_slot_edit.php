<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Course Slot Edit</h1>

<?php var_dump($data); ?>

<form method="POST">
<p>Filter: <input id="CourseSlotEditFilter" /></p>
  <select multiple size='5' id="AvailCourseSelect" name='validCourseIDs[]'>
    <?php
      foreach($data['courses'] as $row)
      {
	echo "<option value=\"$row[id]\"";
	if ($row['selected'] == TRUE)
		echo "selected";
	echo ">$row[name] $row[number]</option>"; 
      }
    ?>
  </select>
<br /><br />
<p>Name: <input name='name' value="<?php echo $data['name']; ?>" /></p>
<p>Recommended Quarter: </p>
<select size=4 name='recommendedQuarter'>
  <option <?php if (strcmp($data['recommendedQuarter'], 'Fall')   == 0) echo 'selected'; ?>>Fall</option>
  <option <?php if (strcmp($data['recommendedQuarter'], 'Winter') == 0) echo 'selected'; ?>>Winter</option>
  <option <?php if (strcmp($data['recommendedQuarter'], 'Spring') == 0) echo 'selected'; ?>>Spring</option>
  <option <?php if (strcmp($data['recommendedQuarter'], 'Summer') == 0) echo 'selected'; ?>>Summer</option>
</select>
<p>Recommended Year:</p> 
<select size=4 name='recommendedYear'>
  <option <?php if (strcmp($data['recommendedYear'], 'Freshman')  == 0) echo 'selected'; ?>>Freshman</option>
  <option <?php if (strcmp($data['recommendedYear'], 'Sophomore') == 0) echo 'selected'; ?>>Sophomore</option>
  <option <?php if (strcmp($data['recommendedYear'], 'Junior')    == 0) echo 'selected'; ?>>Junior</option>
  <option <?php if (strcmp($data['recommendedYear'], 'Senior')    == 0) echo 'selected'; ?>>Senior</option>
</select>
<p>Minimum Grade:</p>
<select size=5 name='minimumGrade'>
  <option <?php if (strcmp($data['minimumGrade'], 'A') == 0) echo 'selected'; ?>>A</option>
  <option <?php if (strcmp($data['minimumGrade'], 'B') == 0) echo 'selected'; ?>>B</option>
  <option <?php if (strcmp($data['minimumGrade'], 'C') == 0) echo 'selected'; ?>>C</option>
  <option <?php if (strcmp($data['minimumGrade'], 'D') == 0) echo 'selected'; ?>>D</option>
  <option <?php if (strcmp($data['minimumGrade'], 'F') == 0) echo 'selected'; ?>>F</option>
</select>
<br /><br />
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/setCurriculumCourseSlot'); ?>">Save</button>
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/cancelCurriculumCourseSlot'); ?>">Cancel</button>
</form>

<script type="text/javascript"> //Uses jQuery
// ID of <input> filter
var Filter = $("#CourseSlotEditFilter");
// ID of <select> to filter
var Select = $("#AvailCourseSelect");

/**
* Only shows options that contain a given text.
* @OriginalAuthor Larry Battle <bateru.com/news>
* @ModifiedBy     William Keen
*     Modification: Streamlined functions for our purpose
*	(Removed unnecessary if statements, variables, made easier to modify)
*/
var FilterSelect = function (select, str) 
{
  str = str.toLowerCase();
  
  //cache the jQuery object of the element
  var $el = $(select);
  
  //cache all the options inside the element
  if (!$el.data("options")) 
    $el.data("options", $el.find("option").clone());
  
  //Addeds the new options based on matches
  var newOptions = $el.data("options").filter(function () 
    {return $(this).text().toLowerCase().match(str);});
  $el.empty().append(newOptions);
};

Filter.on("keyup", function () 
{
  var userInput = Filter.val();
  FilterSelect(Select, userInput);
});
</script>
