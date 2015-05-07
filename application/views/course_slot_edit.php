<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<head>
    <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    <style>
        p
        {
            color: black;
        }
        h1
        {
            color: black;
        }
        body
        {
            padding-top: 60px;
            padding-bottom: 100px;
        }
        form
		{
			font-family: Courier;
		}
    </style>
	<script>
	
		function clearValidCourses()
		{
			$("#AvailCourseSelect option:selected").removeAttr("selected");
		}
		
		function clearPrereqs()
		{
			$("#AvailCourseSlotPreReqs option:selected").removeAttr("selected");
		}
		
		function clearCoreqs()
		{
			$("#AvailCourseSlotCoReqs option:selected").removeAttr("selected");
		}
	
	</script>
</head>
<body>
    <?php include_once('application/views/Templates/navbar.php'); ?>
    <div class="container">
<h1>Course Slot Edit</h1>

<form method="POST">
<p>Valid Classes:</p>
<input id="CourseSlotEditFilter" class='form-control' placeholder="Filter" style="margin-bottom:10px"/>
  <select multiple size='5' id="AvailCourseSelect" name='validCourseIDs[]'>
    <?php
      foreach($data['courses'] as $row)
      {
	echo "<option value=\"$row[id]\"";
	if (isset($row['selected']))
	   if ($row['selected'] == TRUE)
	      echo " selected";
	echo ">$row[name] $row[number]</option>"; 
      }
    ?>
  </select>
  <button type="button" class="btn btn-info btn-sm" onclick="clearValidCourses()">Clear</button>
<br /><br />
<p>Name:</p>
<input name='name' class='form-control' value="<?php echo $data['name'];?>" style="margin-bottom:10px"/>
<p>Title: <input name='notes' class='form-control' value="<?php if (isset($data['notes'])) echo $data['notes']; ?>" /></p>

<p>Recommended Quarter: </p>
<select size=4 name='recommendedQuarter'>
  <?php
    $quarters = array('Fall', 'Winter', 'Spring', 'Summer');
    foreach ($quarters as $quarter)
    {
  	echo '<option';
  	if (isset($data['recommendedQuarter']))
  	   if (strcmp($data['recommendedQuarter'], $quarter) == 0) 
  	       echo ' selected'; 
  	echo ">$quarter</option>";
    }
  ?>
</select>
<p>Recommended Year:</p> 
<select size=4 name='recommendedYear'>
  <?php
    $years = array('Freshman', 'Sophomore', 'Junior', 'Senior');
    foreach ($years as $year)
    {
  	echo '<option';
  	if (isset($data['recommendedYear']))
  	   if (strcmp($data['recommendedYear'], $year) == 0) 
  	       echo ' selected'; 
  	echo ">$year</option>";
    }
  ?>
</select>
<p>Minimum Grade:</p>
<select size=5 name='minimumGrade'>
  <?php
    $grades = array('A', 'B', 'C', 'D', 'F');
    foreach ($grades as $grade)
    {
  	echo '<option';
  	if (isset($data['minimumGrade']))
  	   if (strcmp($data['minimumGrade'], $grade) == 0) 
  	       echo ' selected'; 
  	echo ">$grade</option>";
    }
  ?>
</select>
<br /><br />
<?php  
echo "<input type='hidden' name='courseSlot'";
if (isset($data['index']))
  echo " value=$data[index]";
echo '>';
?>

<p>Prerequisites: </p>
<input class='form-control' id="CourseSlotPreReqsFilter" placeholder="Filter" style="margin-bottom:10px" />
<select multiple size='5' id="AvailCourseSlotPreReqs" name='prereqIDs[]'>
	<?php
	foreach($data['prereqs'] as $row)
	{
		echo "<option value='$row[index]'"; 
		if (isset($row['selected']))
		   if ($row['selected'] == TRUE)
			  echo " selected";
		echo ">$row[name]</option>";
	}
	?>
</select>
<button type="button" class="btn btn-info btn-sm" onclick="clearPrereqs()">Clear</button>
<br /><br />

<p>Corequisites: </p>
<input class='form-control' id="CourseSlotCoReqsFilter" placeholder="Filter" style="margin-bottom:10px" />
<select multiple size='5' id="AvailCourseSlotCoReqs" name='coreqIDs[]'>
	<?php
	foreach($data['coreqs'] as $row)
	{
		echo "<option value='$row[index]'"; 
		if (isset($row['selected']))
		   if ($row['selected'] == TRUE)
			  echo " selected";
		echo ">$row[name]</option>";
	}
	?>
</select>
<button type="button" class="btn btn-info btn-sm" onclick="clearCoreqs()">Clear</button>
<br /><br />

<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/setCurriculumCourseSlot'); ?>">Save</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cancelCurriculumCourseSlot'); ?>">Cancel</button>
</form>

<script type="text/javascript"> //Uses jQuery
var Filter1 = $("#CourseSlotEditFilter");
var Select1 = $("#AvailCourseSelect");
var Filter2 = $("#CourseSlotPreReqsFilter");
var Select2 = $("#AvailCourseSlotPreReqs");
var Filter3 = $("#CourseSlotCoReqsFilter");
var Select3 = $("#AvailCourseSlotCoReqs");

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

Filter1.on("keyup", function () 
{
  var userInput = Filter1.val();
  FilterSelect(Select1, userInput);
});
Filter2.on("keyup", function () 
{
  var userInput = Filter2.val();
  FilterSelect(Select2, userInput);
});
Filter3.on("keyup", function () 
{
  var userInput = Filter3.val();
  FilterSelect(Select3, userInput);
});
</script>
</div>
		<?php include_once('application/views/Templates/footer.php');?>
</body>
