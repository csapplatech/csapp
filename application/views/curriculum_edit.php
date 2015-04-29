<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<head>
    <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    <style>body{
        padding-top: 60px;
}
form
{
	font-family: Courier;
}
</style>
</head>
<body>
<?php include_once('application/views/Templates/navbar.php'); ?>
<div class = "container">

<h1>Curriculum Edit</h1>

<form method="POST">
<input class="form-control" placeholder="Filter" id="CurrEditFilter" style="margin-bottom:5px"/>
  <select size='5' id="CourseSlotSelect" name='courseSlot'>
    <?php
    $largestNameLen = 1;
    $largestQuarterLen = 1;
	foreach($data['course'] as $row)
	{	
		$lenName = strlen($row['name']);
		if ($lenName > $largestNameLen)
			$largestNameLen = $lenName;
			
		$len = strlen($row['quarter']);
		if ($len > $largestQuarterLen)
			$largestQuarterLen = $len;
	}
	
	foreach($data['course'] as $row)
	{	
		$name = $row['name'];
		for ($i = strlen($name); $i < $largestNameLen + 2; $i++)
			$name = $name."&nbsp;";
			
		$quarter = $row['quarter'];	
		for ($j = strlen($quarter); $j < $largestQuarterLen + 2; $j++)
			$quarter = $quarter."&nbsp;";
			
		$string = $name.$quarter.$row['year'];
		echo "<option value=\"$row[index]\">$string</option>"; 
	}
	
    ?>
  </select>
<br />
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/editCurriculumCourseSlot');   ?>">Edit</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/newCurriculumCourseSlot');    ?>">New</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cloneCurriculumCourseSlot');  ?>">Clone</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/deleteCurriculumCourseSlot'); ?>">Delete</button>
<br />
<p>Name:</p>
<input class="form-control" placeholder="New Curriculum" name='name' style="margin-bottom:5px" value="<?php echo $data['name']; ?>" required autofocus></p>
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
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/setCurriculum');    ?>">Save</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cancelCurriculum'); ?>">Cancel</button>
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
</div>
		<?php include_once('application/views/Templates/footer.php');?>

</body>
