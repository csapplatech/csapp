<html>
    <head>
        <title>View Advisor Info</title>
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
        <?php include_once($_SERVER['DOCUMENT_ROOT'].'/application/views/Templates/navbar.php'); ?>
        
        <?php 
        if (isset($advisor))
        {
            echo "<h3> Your Advisor's name is: ".$advisor->getName().".</h3>".
                 "<h3> Your Advisor's email address is: ".$advisor->getEmailAddress().".</h3>";
        }
        else
        {
            echo "<h3> Your Advisor is not set! Please contact the student success office.</h3>";
        }
        ?>
        
        
        <p><img src= "<?php echo IMG."/tech_official_logo.jpg" ?>" alt="Tech Logo" style="width:300px;height:225px"></p>
        
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        
        <footer>
            <font color ="white"</font>
            <p>Created by: Louisiana Tech's CSC 404 Class</p>
            <p>2015 Louisiana Tech University Ruston, LA</p>
        </footer>
        
    </body>
</html>
