<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang ="en">
    <head>
        <title>Transfer Credit Mapping</title>
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
	<link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
	<script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
        <style>
            body{
                padding-top: 50px;
                padding-bottom: 50px;
                color: black;
            }
        </style>
    </head>
    
    <body>
    	<?php include_once('application/views/Templates/navbar.php'); ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<h1>Transfer Mapping for 
<?php
    echo($t_user->getName());
?>

 <a class="btn btn-sm btn-primary" href="<?php echo site_url('Transfer_controller/index') ;?>">
    Change User
        </a></h1>

<form method="POST">
    <div class="container">
<p><input type="currfilter" id="CurrFilter" class="form-control" placeholder="Filter" /></p>
  <select multiple size='5' id="AvailCourseSelect" name='transferCourseID'>
    <?php
    for($i = 0; $i < count($t_courses); $i++)
    {
      $string2 = $t_courses[$i]->getStudentTransferCourseID();
      $string = $t_courses[$i]->getCourseName();
      if ($t_courses[$i]->getAllEquivilentCourses())
        echo("<option style= \"color:blue\" value=\"$string2\">$string</option>");
      else
        echo("<option value=\"$string2\">$string</option>");
    }
?>
  </select>
<br /><br />

<button type="submit" class="btn btn-primary btn-sm" formaction="<?php echo site_url('Transfer_controller/addTransferCredit'); ?>">Add Mapping</button>
<button type="submit" class="btn btn-primary btn-sm" formaction="<?php echo site_url('Transfer_controller/remove'); ?>">View Mapping</button>


</form>

<script type="text/javascript"> //Uses jQuery
// ID of <input> filter
var Filter = $("#CurrFilter");
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
</div>
        <?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>