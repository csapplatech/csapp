<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
		<link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
        <title>Confirm Remove User <?= $uID ?></title>
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
			<form action="<?php echo site_url('User/removeUser/'.$uID) ?>" method="POST">
				<input type="submit" Value="Remove" />
			</form>
			<form action="<?php redirect('User/index/'.$_SESSION['action']); ?>" >
				<input type="submit" value="Cancel" />
			</form>
		</div>
		<?php include_once('application/views/Templates/footer.php'); ?>
    </body>
</html>
