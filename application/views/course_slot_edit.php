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
    </style>
</head>
<body>
    <?php include_once('application/views/Templates/navbar.php'); ?>
    <div class="container">
<h1>Course Slot Edit</h1>

<form method="POST">
<p>Valid Classes:</p>
<input id="CourseSlotEditFilter" class='form-control' placeholder="Filter" value="<?php echo $data['name'];  ?>" style="margin-bottom:10px" required autofocus />
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
<br /><br />
<input name='name' class='form-control' placeholder="Name" value="<?php echo $data['name'];?>" style="margin-bottom:10px" required autofocus />

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

<!-- TEST CODE -->
<p>Prerequisites: </p>
<input name='name' class='form-control' placeholder="Filter" value="<?php echo $data['name'];?>" style="margin-bottom:10px" required autofocus />
<select multiple size='5' id="AvailCourseSlotPreReqs" name='prereqIDs[]'>
	<?php
	foreach($data['prereqs'] as $row)
		echo "<option value='$row[index]'>$row[name]</option>"; 
		if (isset($row['selected']))
		   if ($row['selected'] == TRUE)
			  echo " selected";
	?>
</select>
<br /><br />

<p>Corequisites: </p>
<input name='name' class='form-control' placeholder="Filter" value="<?php echo $data['name'];?>" style="margin-bottom:10px" required autofocus />
<select multiple size='5' id="AvailCourseSlotCoReqs" name='coreqIDs[]'>
	<?php
	foreach($data['coreqs'] as $row)
		echo "<option value='$row[index]'>$row[name]</option>"; 
		if (isset($row['selected']))
		   if ($row['selected'] == TRUE)
			  echo " selected";
	?>
</select>
<br /><br />



<!-- TEST CODE -->


<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/setCurriculumCourseSlot'); ?>">Save</button>
<button class="btn btn-primary btn" type="sumbit" formaction="<?php echo site_url('Curriculumcreator/cancelCurriculumCourseSlot'); ?>">Cancel</button>
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

<script type="text/javascript"> //Uses jQuery
var Filter = $("#CourseSlotPreReqsFilter");
var Select = $("#AvailCourseSlotPreReqs");

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

<script type="text/javascript"> //Uses jQuery
var Filter = $("#CourseSlotCoReqsFilter");
var Select = $("#AvailCourseSlotCoReqs");

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
