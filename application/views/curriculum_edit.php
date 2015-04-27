<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Curriculum Edit</h1>

<form method="POST">
<p>Filter: <input id="CurrEditFilter" /></p>
  <select size='3' id="CourseSlotSelect" name='courseSlot'>
    <?php
      foreach($data['course'] as $row)
	echo "<option value='$row[index]'>$row[name]</option>"; 
    ?>
  </select>
<br />
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/editCurriculumCourseSlot');   ?>">Edit</button>
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/newCurriculumCourseSlot');    ?>">New</button>
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cloneCurriculumCourseSlot');  ?>">Clone</button>
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/deleteCurriculumCourseSlot'); ?>">Delete</button>
<br />
<p>Name: <input name='name' value="<?php echo $data['name']; ?>"></p>
<p>Type:<br />
<select size='3' name='type' required>
	<?php
	$types = array('Degree', 'Minor', 'Concentration');
	foreach ($types as $type)
	{
		echo '<option';
		if (isset($data['type'])) 
		   if (strcmp($data['type'], $type) == 0) 
		      echo ' selected'; 
		echo ">$type</option>";
	}
	?>
</select></p>
<br />
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/setCurriculum');    ?>">Save</button>
<button type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cancelCurriculum'); ?>">Cancel</button>
</form>

<script type="text/javascript"> //Uses jQuery
// ID of <input> filter
var Filter = $("#CurrEditFilter");
// ID of <select> to filter
var Select = $("#CourseSlotSelect");

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
