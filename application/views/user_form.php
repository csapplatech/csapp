<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $_SESSION['action'] ?> User</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo IMG . '/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS . '/magic-bootstrapV2_1.css'; ?>" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
        <style>

            body {
                padding-top: 60px;
            }

            .container {
                color: black;
                text-align: center;
            }

            .container * {
                text-align: left;
            }

            br{
                text-align: center;
                padding-top: 5px;
            }

        </style>
    </head>

    <body>
        <?php include_once('application/views/Templates/navbar.php'); ?>
        <div class="container">
            <p>Selected user with id: <?= $uID ?></p>
            <form action="<?php echo site_url('User/submitUserForm/' . $uID); ?>" method="POST" >
                <p><b>Please fill out user info.</b></p><br/>
                <table>
                    <tr><p><input type="text" name="userID" value="<?= $uID ?>" class="form-control" placeholder="userID" style="margin-bottom:5px" autofocus 
                        <?php if($_SESSION['action'] != 'create' ) {echo 'readonly'; }?>></tr></p>
                    <tr><p><input type="text" name="email" value="<?= $email ?>" class="form-control" placeholder="Email Address" style="margin-bottom:5px" required autofocus></tr></p>
                    <tr><p><input type="text" name="pass" class="form-control" placeholder="Password" style="margin-bottom:5px" autofocus
                                  <?php if($_SESSION['action'] == 'create' ) {echo 'required'; }?>></p></tr>
                    <tr><p><input type="text" name="confPass" class="form-control" placeholder="Confirm New Password" style="margin-bottom:5px" autofocus
                                  <?php if($_SESSION['action'] == 'create' ) {echo 'required'; }?>></p></tr>
                    <tr><p><input type="text" name="fName" class="form-control" placeholder="First Name (Middle Name Optional)" value="<?= $fName ?>" style="margin-bottom:5px" required autofocus></tr></p>
                    <tr><p><input type="text" name="lName" class="form-control" placeholder="Last Name" value="<?= $lName ?>" style="margin-bottom:5px" required autofocus></tr></p>
                </table>
                <p2><b>Please select user Roles.</b></p2><br/>
                <?php
                $roleNames = array(NULL, 'Administrator', 'Program Chair', 'Advisor', 'Student');
                for ($i = 1; $i <= 4; $i++) {
                    echo '<input type="checkbox" name="' . $i . '" value="true" ';
                    if ($roles[$i]) {
                        echo 'checked';
                    }
                    echo ' />' . $roleNames[$i] . '<br />';
                }
                ?>
                <br />
                <button class="btn btn-primary btn" type="submit" style="margin-bottom:5px">NEXT</button>
            </form>
        </div>
        <?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>
</body>

