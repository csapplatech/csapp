<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Curriculum Edit</h1>

<form method="POST">
<p>Filter: <input id="CurrEditFilter" /></p>
  <select size='3' id="CourseSlotSelect" name='courseSlot'>
    <?php
      foreach($data['course'] as $row)
	echo "<option value='$row[1]'>$row[0]</option>"; 
    ?>
  </select>
<br />
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/editCurriculumCourseSlot');   ?>">Edit</button>
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/newCurriculumCourseSlot');    ?>">New</button>
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/cloneCurriculumCourseSlot');  ?>">Clone</button>
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/deleteCurriculumCourseSlot'); ?>">Delete</button>
<br />
<p>Name: <input name='name' value="<?php echo $data['name']; ?>"></p>
<p>Type:<br />
<select size='3' name='type' required>
  <option <?php if (strcmp($data['type'], 'Degree')        == 0) echo 'selected'; ?>>Degree</option>
  <option <?php if (strcmp($data['type'], 'Minor')         == 0) echo 'selected'; ?>>Minor</option>
  <option <?php if (strcmp($data['type'], 'Concentration') == 0) echo 'selected'; ?>>Concentration</option>
</select></p>
<br />
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/setCurriculum');    ?>">Save</button>
<button type="sumbit" formaction="<?php echo site_url('CurriculumCreator/cancelCurriculum'); ?>">Cancel</button>
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
