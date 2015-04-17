<html>
    <head>
        <title>CSAPP Main Page</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
        <script src="../../assets/js/ie-emulation-modes-warning.js"></script>
    </head>
    <body style="padding-top: 60px">
        
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
                      echo "<li class='active'><a href='" .site_url('Mainpage/advisor'). "'>Advisor Home</a></li>";
                  if ($user->isStudent())
                      echo "<li><a href='" .site_url('Mainpage/student'). "'>Student Home</a></li>";
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

        <h3> Welcome to the Advisor Main Page, <?php echo $user->getName(); ?>!</h3>
        
         <a class="btn btn-sm btn-primary" href="<?php echo site_url('Login/logout') ;?>">
            Activate New Advisee
        </a>
         <a class="btn btn-sm btn-primary" href="<?php echo site_url('Login/logout') ;?>">
            Change Password
        </a>
         <a class="btn btn-sm btn-primary" href="<?php echo site_url('Login/logout') ;?>">
            View Classes
        </a>
         <a class="btn btn-sm btn-primary" href="<?php echo site_url('Login/logout') ;?>">
            View Advisees
        </a>
        <a class="btn btn-sm btn-primary" href="<?php echo site_url('Login/logout') ;?>">
            Advising Appointment Schedule
        </a>
        
        <p><img src= <?php echo IMG."/tech_official_logo.jpg" ?> alt="Tech Logo" style="width:300px;height:225px"</p>
        
        <footer>
            <font color ="white"</font>
            <p>Ruston, LA 2015</p> 
            <p>Louisiana Tech University</p>
            <p>Created by: Louisiana Tech's CSC 404 Class</p>
        </footer>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
        
    </body>
</html>
