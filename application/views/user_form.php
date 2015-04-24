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
		</div>
		<?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>
</body>

