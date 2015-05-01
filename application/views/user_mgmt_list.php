<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>User Management List</title>
    </head>
    
    
    <body>
        <form action="<?php echo site_url('User/submitUserListQuery') ?>" method="POST" >
            <input type="text" name="searchStr" class="form-control" placeholder="Search by Name..." style="margin-bottom:5px" autofocus /> 
            <input type="submit" value="Search" />
        </form>    
        <?php
        echo '<a href="' . site_url('/User/index/create') . '">Create New User</a></br></br>';
        echo '<table cellPadding="10" allignment="center" >';
        echo '<tr><th>UID</th><th>User Name</th><th>User Email</th><th>Role (1-4)</th><th>Delete User</th></tr>';
        foreach ($allUsers as $user) {
            echo '<tr><td> ' . $user->getUserID() . '</td> '
            . '<td><a href="' . site_url('/User/index/modify/' . $user->getUserID()) . '">' . $user->getName() . '</a></td>'
            . '<td> ' . $user->getEmailAddress() . '</td> '
            . '<td> ';
            if ($user->isAdmin()) {
                echo '4';
            }
            if ($user->isProgramChair()) {
                echo '3';
            }
            if ($user->isAdvisor()) {
                echo '2';
            }
            if ($user->isStudent()) {
                echo '1';
            }
            echo ' </td> '
            . '<td><a href="' . site_url('/User/index/remove/' . $user->getUserID()) . '"> Delete</a></td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </body>
</html>
