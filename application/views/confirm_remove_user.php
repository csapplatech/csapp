<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Confirm Remove User <?= $uID ?></title>
    </head>
    <body>
        <form action="<?php echo site_url('User/removeUser/'.$uID) ?>" method="POST">
            <input type="submit" Value="Remove" />
        </form>
        <form action="<?php redirect('User/index/'.$_SESSION['action']); ?>" >
            <inpuut type="submit" value="Cancel" />
        </form>
    </body>
</html>
