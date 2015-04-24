<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
    <head>
        <title><?= $_SESSION['action'] ?> User</title>
    </head>

    <body>
        <b>Select a user to <?= $_SESSION['action'] ?></b>
        <form action="<?php echo site_url('User/submitSelectUserForm'); ?>" method="POST">
            <label>Input User ID:</label>
            <input type="text" name="userID"><br />
            <input type="submit" value="Confirm"><br />
        </form>
    </body>
</html>
</body>
