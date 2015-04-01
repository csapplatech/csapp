<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>CS App Login</title>

    <!-- Bootstrap core CSS -->
    <link href="magic-bootstrapV2.css" rel="stylesheet">
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>
  </head>

  <body style="padding-top:60px">
<<<<<<< HEAD
=======

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
>>>>>>> login

    <div class="container">

      <?php
        if(isset($error))
        {
          echo 'div class="alert alert-danger alert-dismissbale" role="alert">WRONG USERNAME OR PASSWORD. PLEASE TRY AGAIN.,<div>';
        }
      ?>


      <header>
        <p style="font-size:250%"><font color="white">Welcome to LA Tech's CS Advising Application</p>
      </header>

      <headings>
        <img src="tech_official_logo.jpg" alt="Tech Logo" style="width:333px;heigh283px">
      </headings>

      <form class="form-signin">
        <h2 class="form-signin-heading">Please sign in</h2>
        <label for="inputUsername" class="sr-only">Username</label>
        <input type="text" id="username" class="form-control" placeholder="Username" style="margin-bottom:5px" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" style="margin-bottom:25px" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" style="margin-bottom:5px">Sign in</button>
      </form>

      <a class="btn btn-lg btn-primary btn-block" href="<?php echo site_url('login/guestLogin'); ?>">Guest</a>

      <footer>
        <p>Louisiana Tech University</p>
        <p>Ruston, LA</p>
        <p>(318)257-3036</p>
      </footer>

    </div> <!-- /container -->
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
