<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">

    <title>CSAPP Login</title>
    
    <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>
  </head>
  
  <body style="padding-top:60px">

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
              </ul>
              <ul class="nav navbar-nav navbar-right">
              </ul>
            </div>
           </div>
        </nav>
      
        <div class="container">

            <?php
              if (isset($error))
              {
                  echo '<div class="alert alert-danger alert-dismissable role="alert">WRONG USERNAME OR PASSWORD. PLEASE TRY AGAIN.</div>';
              }
            ?>
            <form class="form-signin" action="<?php echo site_url('Login/auth'); ?>" method="POST">
              <h2 class="form-signin-heading" style="margin-bottom:15px">Please sign in</h2>
              <label for="inputUsername" class="sr-only">Username</label>
              <input type="text" id="username" name="username" class="form-control" placeholder="Username" style="margin-bottom:5px" required autofocus>
              <label for="inputPassword" class="sr-only">Password</label>
              <input type="password" id="password" name="password" class="form-control" placeholder="Password" style="margin-bottom:25px" required>
              <button class="btn btn-lg btn-primary btn-block" type="submit" style="margin-bottom:5px">Sign in</button>
            </form>
            <a class="btn btn-lg btn-primary btn-block" href="<?php echo site_url('Login/guestLogin'); ?>">Proceed as Guest</a>
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    
    <?php include 'Keen-Hjorth-Test.php';?>
    
    <footer>
            <font color ="white"</font>
            <p>Ruston, LA 2015</p> 
            <p>Louisiana Tech University</p>
            <p>Created by: Caleb Baze, Azriel Richardson, Ryan Gardiner and Zachary Behnke</p>
    </footer>
  </body>
</html>
