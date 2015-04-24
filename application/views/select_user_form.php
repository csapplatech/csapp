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
		<link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
		<link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
		<style>
	
			body {
				padding-top: 60px;
			}
	
			.container {
				color: black;
				text-align: left;
			}

			.container * {
				text-align: left;
			}
			
		</style>
    </head>

    <body>
		<?php include_once('application/views/Templates/navbar.php'); ?>
		<div class="container">
			<b>Select a user to <?= $_SESSION['action'] ?></b>
			<form action="<?php echo site_url('User/submitSelectUserForm'); ?>" method="POST">
				<label>Input User ID:</label>
				<input type="text" name="userID"><br />
				<input type="submit" value="Confirm"><br />
			</form>
		</div>
		<?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>
</body>
