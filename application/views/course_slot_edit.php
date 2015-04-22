<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Course Slot Edit</h1>

<form method="POST">
<p>Filter: <input id="CourseSlotEditFilter" /></p>
  <select multiple size='3' id="AvailCourseSelect" name='validCourseIDs[]'>
    <?php
      foreach($data['courses'] as $row)
	echo "<option value=\"$row[id]\">$row[name] $row[number]</option>"; 
    ?>
  </select>
<br /><br />
<p>Name: <input name='name' value="<?php echo $data['name']; ?>" /></p>
<p>Minimum Grade:</p>
<select size=5 name='minimumGrade'>
  <option>A</option>
  <option>B</option>
  <option>C</option>
  <option>D</option>
  <option>F</option>
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
