<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Edit User</title>
    </head>

    <body>
            <table>
                <tr><p>New Email Address: <input type="text" name="<?=$emailAddress ?>" value="<?=$emailAddress ?>"></p></tr>
                <tr><p>New Password: <input type="password" name="<?=$password?>" value="<?=$password ?>"></p></tr>
            <tr><p>Confirm New Password: <input type="password" name="<?=$password?>" value="<?=$password ?>"></p></tr>
                <tr><p>New First Name: <input type="text" name="<?=$fName?> "value="<?=$fName ?>"></p></tr>
                <tr><p>New Last Name: <input type="text" name="<?=$lName?> "value="<?=$lName ?>"></p></tr>
            </table>
            <p><b>Please select user Roles.</b></p><br/>
            <!--fix these checkboxes-->
            <!--fix these checkboxes-->
            <!--fix these checkboxes-->
            <input type="checkbox" name="<?=$role4 ?>" id="<?=$role4 ?>"/>Student<br />
            <input type="checkbox" name="Advisor" id="3"/>Advisor<br />
            <input type="checkbox" name="<?=$role2 ?>" id="<?=$role2 ?>"/>Program Chair<br />
            <input type="checkbox" name="<?=$role1 ?>" id="<?=$role1 ?>"/>Administrator<br />
            <br />
            <tr><input type="submit" value="NEXT"></tr>

        </form>
    </body>
</html>
</body>

