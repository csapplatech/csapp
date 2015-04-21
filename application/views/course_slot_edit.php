<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<h1>Course Slot Edit</h1>

<p>Filter: <input type="currfilter" id="CurrFilter" /></p>
<div class="scrollable" id="CurriculumSelectDiv">
  <select size='3' class="scrollableinside" id="CurriculumSelect">
    <option>test</option>
    <?php
/*      foreach($data as $row)
	echo "<option>$row</option>"; 
  */  ?>
  </select>
</div>

<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">Clone</button>
<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">New</button>
<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">Remove</button>
<button onclick="location.href='<?php echo site_url('Mainpage/index'); ?>'">Edit</button>

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
