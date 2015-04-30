<html>
    <head>
        <title>Change Your Password!</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
    </head>
    <body style="padding-top: 60px">
        <?php include_once('application/views/Templates/navbar.php'); ?>
        
        <div class="container">
            
            <?php
                if (isset($error))
                {
                    echo '<div class="alert alert-danger alert-dismissable role="alert">WRONG PASSWORD. PLEASE TRY AGAIN.</div>';
                }
                if (isset($error2))
                {
                    echo '<div class="alert alert-danger alert-dismissable role="alert">PASSWORDS DID NOT MATCH. PLEASE TRY AGAIN.</div>';
                }
                if (isset($error3))
                {
                    echo '<div class="alert alert-danger alert-dismissable role="alert">NEW PASSWORD MUST BE AT LEAST 8 CHARACTERS LONG AND CONTAIN ONE OR MORE UPPERCASE LETTER AND SYMBOL: ! @ # $ % & * - + = 1 2 3 4 5 6 7 8 9 0 '
                    . '<br>PLEASE TRY AGAIN.</div>';
                }
                if (isset($success))
                {
                    echo '<div class="alert alert-success alert-dismissable role="alert">PASSWORD CHANGE SUCCESSFUL!</div>';
                }
            ?>
            
            <form class="form-signin" action="<?php echo site_url('Changepassword/change'); ?>" method="POST">
                  <h2 class="form-signin-heading" style="margin-bottom:15px">Enter your old password and what you want to change it to.</h2>
                  <label for="inputPassword" class="sr-only">Current Password</label>
                  <input type="password" id="oldpw" name="oldpw" class="form-control" placeholder="Current Password" style="margin-bottom:5px" required autofocus>
                  <label for="inputPassword" class="sr-only">New Password</label>
                  <input type="password" id="newpw" name="newpw" class="form-control" placeholder="New Password" style="margin-bottom:5px" required>
                  <label for="inputPassword" class="sr-only">Repeat New Password</label>
                  <input type="password" id="newpw2" name="newpw2" class="form-control" placeholder="New Password" style="margin-bottom:25px" required>
                  <button class="btn btn-lg btn-primary btn-block" type="submit" style="margin-bottom:5px">Change Password</button>
            </form>
        </div>
        
        <p><img src= "<?php echo IMG."/tech_official_logo.jpg" ?>" alt="Tech Logo" style="width:300px;height:225px"></p>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        
        <?php include_once('application/views/Templates/footer.php'); ?>
        
    </body>
</html>
