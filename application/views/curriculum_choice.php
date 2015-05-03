<head>
    <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    <style>

button
{
	display: inline-block;
}

body
{
	padding-top: 50px;
}
form
{
	font-family: Courier;
}
	</style>
</head>

<body>
    <?php include_once('application/views/Templates/navbar.php'); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Curriculum Choice</h1>

<form method="POST">
	<div class = "container">
<p><input type="currfilter" id="CurrFilter" class="form-control" placeholder="Filter" /></p>
	<select size='10' id="CurriculumSelect" name='curriculum'>
	<?php
	$largestLen = 1;
	foreach($data as $row)
	{	
		$len = strlen($row['name']);
		if ($len > $largestLen)
			$largestLen = $len;
	}
		
	foreach($data as $row)
	{	
		$name = $row['name'];
		for ($i = strlen($name); $i < $largestLen + 2; $i++)
			$name = $name."&nbsp;";
		
		$string = $name.$row['date'];
		echo "<option value=\"$row[id]\">$string</option>"; 
	}	
	?>
	</select>
<br /><br />
<button type="sumbit" class="btn btn-primary btn" formaction="<?php echo site_url('Curriculumcreator/editCurriculum'); ?>">Edit</button>
<button type="sumbit" class="btn btn-primary btn" formaction="<?php echo site_url('Curriculumcreator/newCurriculum'); ?>">New</button>
<button type="sumbit" class="btn btn-primary btn" formaction="<?php echo site_url('Curriculumcreator/cloneCurriculum'); ?>">Clone</button>
<button type="sumbit" class="btn btn-primary btn" formaction="<?php echo site_url('Curriculumcreator/deleteCurriculum'); ?>">Delete</button>

</form>

<script type="text/javascript"> //Uses jQuery
// ID of <input> filter
var Filter = $("#CurrFilter");
// ID of <select> to filter
var Select = $("#CurriculumSelect");

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
		<?php include_once('application/views/Templates/footer.php');?>
		</div>
</body>
