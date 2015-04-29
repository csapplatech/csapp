<!DOCTYPE html>
<html>
    <head>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
        <link rel="stylesheet" type="text/css" href="cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.6/animate.min.css" />
        <title>CSAPP Main Page</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
         <!--The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags--> 
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
        <script src="../../assets/js/ie-emulation-modes-warning.js"></script>
    </head>
    <body onload = "hideTables()">
         <nav class="navbar navbar-default navbar-fixed-top">
          <div class="container">
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="<?php echo site_url('Mainpage/index'); ?>">CSAPP</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
              <ul class="nav navbar-nav">
                <?php
                
                  if ($user->isAdvisor())
                      echo "<li><a href='" .site_url('Mainpage/advisor'). "'>Advisor Home</a></li>";
                  if ($user->isStudent())
                      echo "<li class='active'><a href='" .site_url('Mainpage/student'). "'>Student Home</a></li>";
                  if ($user->isAdmin())
                      echo "<li><a href='" .site_url('Mainpage/admin'). "'>Admin Home</a></li>";
                  if ($user->isProgramChair())
                      echo "<li><a href='" .site_url('Mainpage/programChair'). "'>Program Chair Home</a></li>";
                ?>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('Login/logout'); ?>">Logout</a></li>
              </ul>
            </div><!--/.nav-collapse -->
           </div>
        </nav>
        <br>
       
       
<?php

if($user->isAdvisor())
    {
        echo "<p style='z-index: 1000; color:black; background-color: yellow; margin-top:30px;'>Select Available Office Hours</p>". "<br>";
    }
else if($user->isStudent())
    { //if there is a schedule ID for this student's advisor
        echo "<p style='z-index: 1000; color:black; background-color: yellow; margin-top:30px;'>Hello ".$user->getName(). "</p><br>";
    }

else
    {
     echo "IDK WHO THIS IS.....". "<br>";
    }


//if appointment_array is null make new calendar if it is populated modify the calendar
if($app_Times==null)
    {
      echo "app_Times was null. This is a new Schedule";
    }

if(isset($_POST['submit']))
    {//to run PHP script on submit
        if(!empty($_POST['appointments']))
        {
            redirect('appointment_controller/fill');
        }
    }


else
    {
        echo $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4),'' ,(isset($_GET['interval']))? $_GET['interval'] : 20);
    }
?>
        <style>    
            table.scroll tbody,
            table.scroll thead { display: block; }
            
            thead tr th { 
                /* text-align: left; */
                width: 41px;
                border: 1px transparent solid;
            }
            thead #col {
                width: 43px;
            }
            thead #col0 {
                width: 63px;
            }
            table.scroll tbody {
                height: 380px;
                overflow-y: auto;
                overflow-x: hidden;
            }
        </style>
        <style>
            #calwrap {
                height: 550px;
                width: 425px;
                margin: 0 auto;
            }
            p {
                text-shadow: none;
                color: black;
            }
            body {
                background-color: white;
            }
            table th{
                text-align: center
            }
            #top td {
                background-color: #dbdcd6;
                padding: 0px 1px;
            }
            #top td #startT, #top td #endT {
                width: 60px;
                background: transparent;
                border: none;
                text-align: center;
            }
            #top #submitwrap input {
                width: 100%;
                padding-top: 11px;
                padding-bottom: 11px;
                background: #fa7d67;
                border: none;
                color: white;
                font-weight: bold;
                text-transform: uppercase;
            }
            #top #submitwrap input:hover {
                background: #f83614;
            }
            #col0{
                color: black;
                text-shadow: none;
                font-size: 90%;
                width: 80px;
            }
            table{
                text-shadow: none;
                color: black;
            }
            table a {
                color: black;
            }
            #table0 td, #table1 td,#table2 td,#table3 td,#table4 td,#table5 td {
                border: 1px solid white;
                text-align: center;
                background-color: #f2f2f2;
                font-weight: lighter;
            }
            #table0 .tableeven,#table1 .tableeven,#table2 .tableeven,#table3 .tableeven,#table4 .tableeven,#table5 .tableeven {
                background-color: white;
            }
            #pad
            {
                    width: 72px;
            }
            #nextprevious ,#previousnext ,#nextweek ,#prevweek {
                text-decoration: none;
            }
            #nextprevious div,#previousnext div,#nextweek div,#prevweek div{
                height: 20px;
                width: 30px;
                background-color: #dbdcd6;
                color: #8f8f8f;
                text-align: center;
                border-radius: 30%;
                -webkit-box-shadow: inset 1px 1px 5px 1px rgba(97, 97, 97, 0.41));
                -moz-box-shadow: inset 1px 1px 5px 1px rgba(97, 97, 97, 0.41);
                box-shadow: inset 1px 1px 5px 1px rgba(97, 97, 97, 0.41);
            }
            #nextprevious div:hover,#previousnext div:hover,#nextweek div:hover,#prevweek div:hover{
                background-color: #c2c4b9;
                color: #f4f4f2;
            }
            
            #col {
                font-size: 200%;
                vertical-align: middle;
                font-weight: bold;
                color: #e4481b;
                padding: 0 7px;
            }
            input[type=checkbox] {
                visibility: hidden;
            }
            /* creates box size for label to sit in*/
            .My_Schedule
            {
                 
               
                width: 40px;
                height: 20px;
                
                position: relative;
            }
            
            /* replaces orginal checkbox for styled checkbox*/
            .My_Schedule label
            {
                display: block;
                width: 40px;
                height: 20px;
                
                -webkit-transition: all .8s ease;
                -moz-transition: all .8s ease;
                -o-transition: all .8s ease;
                -ms-transition: all .8s ease;
                transition: all .8s ease;
                cursor: pointer;
                position: absolute;
                top: 0px;
                left: 0px;
                z-index: 1;
                
                background-color: #ff4a45;
            }
            .My_Schedule label:hover {
                background-color:#fff414 ;
            }
            /* Create the checked state*/
            .My_Schedule input[type=checkbox]:checked + label {
                    background-color: black;
            }
            
            
             .Scheduled
            {
                width: 40px;
                height: 20px;
                
                position: relative;
            }
            /* replaces orginal checkbox for styled checkbox*/
            .Scheduled label
            {
                display: block;
                width: 40px;
                height: 20px;
                
                -webkit-transition: all .8s ease;
                -moz-transition: all .8s ease;
                -o-transition: all .8s ease;
                -ms-transition: all .8s ease;
                transition: all .8s ease;
                cursor: pointer;
                position: absolute;
                top: 0px;
                left: 0px;
                z-index: 1;
                
                background-color: #ff482b;
            }
            .Scheduled label:hover {
                background-color: #ff2522;
            }
            /* Create the checked state*/
            .Scheduled input[type=checkbox]:checked + label {
                    background-color: black;
            }
            
            
             .Open
            {
                width: 40px;
                height: 20px;
                
                position: relative;
            }
            /* replaces orginal checkbox for styled checkbox*/
            .Open label
            {
                display: block;
                width: 40px;
                height: 20px;
                
                -webkit-transition: all .8s ease;
                -moz-transition: all .8s ease;
                -o-transition: all .8s ease;
                -ms-transition: all .8s ease;
                transition: all .8s ease;
                cursor: pointer;
                position: absolute;
                top: 0px;
                left: 0px;
                z-index: 1;
                
                background-color: #F1EA9B;
            }
            .Open label:hover {
                background-color: #f1cc73;
            }
            /* Create the checked state*/
            .Open input[type=checkbox]:checked + label {
                    background-color: black;
            }
            
            
            
            
        .cboxwrapper
            {
                width: 40px;
                height: 20px;
                
                position: relative;
            }
            /* replaces orginal checkbox for styled checkbox*/
            .cboxwrapper label
            {
                display: block;
                width: 40px;
                height: 20px;
                
                -webkit-transition: all .8s ease;
                -moz-transition: all .8s ease;
                -o-transition: all .8s ease;
                -ms-transition: all .8s ease;
                transition: all .8s ease;
                cursor: pointer;
                position: absolute;
                top: 0px;
                left: 0px;
                z-index: 1;
                
                background-color: #aac2dd;
            }
            .cboxwrapper label:hover {
                background-color: #608dbf;
            }
            /* Create the checked state*/
            .cboxwrapper input[type=checkbox]:checked + label {
                    background-color: #9fec37;
            }
            #tabletop {
                margin-left: 40px;
            }

            .animated {
              -webkit-animation-duration: 1s;
              animation-duration: 1s;
              -webkit-animation-fill-mode: both;
              animation-fill-mode: both;
            }            
            @-webkit-keyframes fadeIn {
              0% {
                opacity: 0;
              }
            
              100% {
                opacity: 1;
              }
            }
            
            @keyframes fadeIn {
              0% {
                opacity: 0;
              }
            
              100% {
                opacity: 1;
              }
            }
            
            .fadeIn {
              -webkit-animation-name: fadeIn;
              animation-name: fadeIn;
            }
            @-webkit-keyframes fadeOut {
              0% {
                opacity: 1;
              }
            
              100% {
                opacity: 0;
              }
            }
            
            @keyframes fadeOut {
              0% {
                opacity: 1;
              }
            
              100% {
                opacity: 0;
              }
            }
            
            .fadeOut {
              -webkit-animation-name: fadeOut;
              animation-name: fadeOut;
            }
            @keyframes fadeOutDown {
              0% {
                opacity: 1;
              }
            
              100% {
                opacity: 0;
                -webkit-transform: translate3d(0, 100%, 0);
                transform: translate3d(0, 100%, 0);
              }
            }
            
            .fadeOutDown {
              -webkit-animation-name: fadeOutDown;
              animation-name: fadeOutDown;
            }
            @keyframes fadeInUp {
              0% {
                opacity: 0;
                -webkit-transform: translate3d(0, 100%, 0);
                transform: translate3d(0, 100%, 0);
              }
            
              100% {
                opacity: 1;
                -webkit-transform: none;
                transform: none;
              }
            }
            
            .fadeInUp {
              -webkit-animation-name: fadeInUp;
              animation-name: fadeInUp;
            }
            @-webkit-keyframes bounceInUp {
              0%, 60%, 75%, 90%, 100% {
                -webkit-transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
                transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
              }
            
              0% {
                opacity: 0;
                -webkit-transform: translate3d(0, 3000px, 0);
                transform: translate3d(0, 3000px, 0);
              }
            
              60% {
                opacity: 1;
                -webkit-transform: translate3d(0, -20px, 0);
                transform: translate3d(0, -20px, 0);
              }
            
              75% {
                -webkit-transform: translate3d(0, 10px, 0);
                transform: translate3d(0, 10px, 0);
              }
            
              90% {
                -webkit-transform: translate3d(0, -5px, 0);
                transform: translate3d(0, -5px, 0);
              }
            
              100% {
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
              }
            }
            @keyframes bounceInUp {
              0%, 60%, 75%, 90%, 100% {
                -webkit-transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
                transition-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
              }
            
              0% {
                opacity: 0;
                -webkit-transform: translate3d(0, 3000px, 0);
                transform: translate3d(0, 3000px, 0);
              }
            
              60% {
                opacity: 1;
                -webkit-transform: translate3d(0, -20px, 0);
                transform: translate3d(0, -20px, 0);
              }
            
              75% {
                -webkit-transform: translate3d(0, 10px, 0);
                transform: translate3d(0, 10px, 0);
              }
            
              90% {
                -webkit-transform: translate3d(0, -5px, 0);
                transform: translate3d(0, -5px, 0);
              }
            
              100% {
                -webkit-transform: translate3d(0, 0, 0);
                transform: translate3d(0, 0, 0);
              }
            }
            .bounceInUp {
              -webkit-animation-name: bounceInUp;
              animation-name: bounceInUp;
            }
        </style>
        <style>
		.container{
			width: 800px;
			margin: 0 auto;
                        color: black;
		}



		ul.tabs{
			margin: 0px;
			padding: 0px;
			list-style: none;
                        color: green;
                        
		}
		ul.tabs li{
			background: none;
			color: #222;
			display: inline-block;
			padding: 10px 15px;
			cursor: pointer;
		}

		ul.tabs li.current{
			background: #ededed;
			color: #222;
		}

		.tab-content{
			display: none;
			background: #ededed;
			padding: 15px;
                        color: red;
		}

		.tab-content.current{
			display: inherit;
                        color: blue;
		}
        </style>
        <script>
            $(document).ready(function(){

                    $('ul.tabs li').click(function(){
                            var tab_id = $(this).attr('data-tab');

                            $('ul.tabs li').removeClass('current');
                            $('.tab-content').removeClass('current');

                            $(this).addClass('current');
                            $("#"+tab_id).addClass('current');
                    })

            }) 
        </script>
        <script>
            // Change the selector if needed
            var $table = $('table.scroll'),
                $bodyCells = $table.find('tbody tr:first').children(),
                colWidth;
            
            // Adjust the width of thead cells when window resizes
            $(window).resize(function() {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function() {
                    return $(this).width();
                }).get();
                
                // Set the width of thead columns
                $table.find('thead tr').children().each(function(i, v) {
                    $(v).width(colWidth[i]);
                });    
            }).resize(); // Trigger resize handler
        </script>
        <script>
            function selectAll(e, i)
            {
                if (e.shiftKey) //select
                {
                    var temp = i.id;
                    temp = temp.substring(0, temp.length - 1);
                    document.getElementById(temp).checked = true;
                }
                else if (e.ctrlKey) //deselect
                {
                    var temp = i.id;
                    temp = temp.substring(0, temp.length - 1);
                    document.getElementById(temp).checked = false;
                }
                else{
                    
                }
            }
            var pageNum = 1;
            function hideTables() {
                    var top_level_div = document.getElementById('weekwrapper'); //grabs table container
                    var count = top_level_div.getElementsByTagName('table').length; //gets number of tables in container
                    for (i = 0; i < count; i++) {
                        tempcount = i;
                        tempcount = tempcount.toString();
                        tablename = "table".concat(tempcount);
                        table = document.getElementById(tablename);
                        if ((pageNum == 1)&&(i+1 == 1)) //start off showing the first table
                        {
                            table.style.position =  "absolute";
                            table.className = "scroll animated bounceInUp";
                            table.style.visibility = "visible";
                        }
                        else //hide other tables
                        {
                            table.style.position =  "absolute";
                            table.className = "scroll animated fadeOutDown";
                            table.style.visibility = "hidden";
                        }
                    }
            }
            function nextOrPrev(inp) {
                var table;
                var tablename;
                var tempcount;
                if (inp.id =="nextweek") {
                    ++pageNum;
                    var top_level_div = document.getElementById('weekwrapper'); //grabs table container
                    var count = top_level_div.getElementsByTagName('table').length; //gets number of tables in container

                    for (i = 0; i < count; i++) //show the right week
                    {
                        if (pageNum== i+1) //show the next week
                        {
                            tempcount = i;
                            tempcount = tempcount.toString();
                            tablename = "table".concat(tempcount);
                            table = document.getElementById(tablename);
                            table.className = "scroll animated bounceInUp";
                            table.style.visibility = "visible";
                        }
                        else if (pageNum == count+1) //show beginning of next month
                        {
                            location.replace(document.getElementById("nextprevious").href);
                        }
                        else //hide any other tables
                        {
                            tempcount = i;
                            tempcount = tempcount.toString();
                            tablename = "table".concat(tempcount);
                            table = document.getElementById(tablename);
                            table.className = "scroll animated fadeOutDown";
                            table.style.visibility = "hidden";  
                        }
                    }
                }
                else if (inp.id=="prevweek") {
                    --pageNum;
                    var top_level_div = document.getElementById('weekwrapper'); //grabs table container
                    var count = top_level_div.getElementsByTagName('table').length; //gets number of tables in container

                    for (i = 0; i < count; i++) //show the right week
                    {
                        if (pageNum== 0) //show beginning of previous month
                        {
                            location.replace(document.getElementById("previousnext").href);

                        }
                        else if (pageNum == i+1) //show previous week
                        {
                            tempcount = i;
                            tempcount = tempcount.toString();
                            tablename = "table".concat(tempcount);
                            table = document.getElementById(tablename);
                            table.className = "scroll animated bounceInUp";
                            table.style.visibility = "visible";
                        }
                        else //hide any other tables
                        {
                            tempcount = i;
                            tempcount = tempcount.toString();
                            tablename = "table".concat(tempcount);
                            table = document.getElementById(tablename);
                            table.className = "scroll animated fadeOutDown";
                            table.style.visibility = "hidden";  
                        }
                    }
                }
                else{}
            }
        </script>
        <p> <img src= <?php echo IMG."/tech_official_logo.jpg" ?> alt="Tech Logo" style="width:300px;height:225px"> </p>
        <footer>
            <font color ="white"</font>
            <p>Ruston, LA 2015</p> 
            <p>Louisiana Tech University</p>
            <p>Created by: Louisiana Tech's CSC 404 Class</p>
        </footer>
    </body>
</html>