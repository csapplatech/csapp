<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html>
    <head>
        <meta charset="utf-8">
        <link rel="icon" href="<?php echo IMG . '/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS . '/magic-bootstrapV2_1.css'; ?>" type="text/css">
        <title>User Management List</title>
        <style>
            body{
                padding-top: 50px;
                padding-bottom: 50px;
                color: black;
            }
            td{
                padding-right: 10px;
                padding-left: 50px;
                text-align: center;
                padding-bottom: 5px;
            }
            th{
                padding-right: 10px;
                padding-left: 50px;
                text-align: center;
                padding-bottom: 5px;
            }
            a{
                color: blue;
            }
        </style>
    </head>

    <body>
        <?php include_once('application/views/Templates/navbar.php'); ?>
        <div class='container'>
            <form action="<?php echo site_url('User/submitUserListQuery') ?>" method="POST" >
                <br/>
                <input type="text" name="searchStr" class="form-control" placeholder="Search by Name..." style="margin-bottom: 5px;" /> 
                <input type="submit" class="btn btn-primary btn" value="Search" />
            </form>
            <br/>
            <?php
            echo '<form action="' . site_url('/User/index/create') . '">';
            echo '<input type="submit" class="btn btn-primary btn" value="Create New User" /></br></br> </form>';
            echo '<table cellPadding="50" border="3" allignment="center" >';
            echo '<tr><th>UID</th><th>User Name</th><th>User Email</th><th>Role (1-4)</th><th>Delete User</th></tr>';
            foreach ($allUsers as $user) {
                echo '<tr><td> ' . $user->getUserID() . '</td> '
                . '<td><a href="' . site_url('/User/index/modify/' . $user->getUserID()) . '">' . $user->getName() . '</a></td>'
                . '<td> ' . $user->getEmailAddress() . '</td> '
                . '<td> ';
                if ($user->isStudent()) {
                    echo 'Student';
                }
                if ($user->isAdmin()) {
                    echo 'Admin, ';
                }
                if ($user->isProgramChair()) {
                    echo 'Program Chair, ';
                }
                if ($user->isAdvisor()) {
                    echo 'Advisor, ';
                }
                echo ' </td> '
                . '<td><a href="' . site_url('/User/index/remove/' . $user->getUserID()) . '"> Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            ?>
            <?php include_once('application/views/Templates/footer.php'); ?>
        </div>
    </body>
</html>
