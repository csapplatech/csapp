<html>
    <head>
        <title>CSAPP Main Page</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <link href="magic-bootstrapV2.css" rel="stylesheet">
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
                <li class="active"><a href="<?php echo site_url('Account Info'); ?>">Account Info</a></li>
                <li class="active"><a href="<?php echo site_url('Curriculum'); ?>">Curriculum</a></li>
                <li class="active"><a href="<?php echo site_url('Courses'); ?>">Courses</a></li>
                <li class="active"><a href="<?php echo site_url('Bug Reports'); ?>">Bug Reports</a></li>
                <li class="active"><a href="<?php echo site_url('Manage Users'); ?>">Manage Users</a></li>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li><a href="<?php echo site_url('Login/logout'); ?>">Logout</a></li>
              </ul>
            </div><!--/.nav-collapse -->
           </div>
        </nav>

        <body>
          <div id="wrapper">
            <div id="footer"></div>
          </div>
        </body>

        <footer>
          <p>Louisiana Tech University</p>
          <p>Ruston, LA</p>
          <p>(318)257-3036</p>
        </footer>

        <p> Welcome to the Admin Main Page, <?php echo $user->getName(); ?>!</p>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
        
    </body>
</html>
