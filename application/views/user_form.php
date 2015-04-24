<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?= $_SESSION['action'] ?> User</title>

    </head>

    <body>
        <p>Selected user with id: <?= $uID ?></p>
        <form action="<?php echo site_url('User/submitUserForm/' . $uID); ?>" method="POST" >
            <p><b>Please fill out user info.</b></p><br/>
            <table>
                <tr><p>Email Address: <input type="text" name="email" value="<?= $email ?>"></p></tr>
                <tr><p>Password: <input type="password" name="pass" ></p></tr>
                <tr><p>Confirm New Password: <input type="password" name="confPass" ></p></tr>
                <tr><p>First Name (Middle Name opt.): <input type="text" name="fName" value="<?= $fName ?>" ></p></tr>
                <!--<tr><p>Middle Name: <input type="text" name="mName" value="<?= $mName ?>" ></p></tr>-->
                <tr><p>Last Name: <input type="text" name="lName" value="<?= $lName ?>" ></p></tr>
            </table>
            <p><b>Please select user Roles.</b></p><br/>
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
            <tr><input type="submit" value="NEXT"></tr>

        </form>
    </body>
</html>
</body>

