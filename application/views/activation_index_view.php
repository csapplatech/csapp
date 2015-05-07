<html>
    <head>
        <title>Activate Student User</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="<?php echo IMG.'/icon.ico'; ?>">
        <link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		<script src="<?php echo JS . '/bootstrap.min.js'; ?>"></script>
		<style>
		
			li.list-group-item {
				text-align: left;
				color: black;
				padding: 8px;
			}
			
			li.list-group-item h4 {
				display: inline-block;
			}
			
			li.list-group-item div.pull-right a {
				display: inline-block;
				color: white;
			}
		
		</style>
		<script>
			
			function activate(id)
			{
				var url = "<?php echo site_url('Activation/send'); ?>/" + id;
				
				window.location = url;
			}
			
		</script>
    </head>
    <body style="padding: 60px 0px;">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container">
			<div class="row">
				<div class="col-xs-12">
					
					<?php
					
						if(isset($_SESSION['activation.message']))
						{
							$msg = $_SESSION['activation.message'];
							unset($_SESSION['activation.message']);
							
							echo "<div class='alert alert-success' role='alert'>$msg</div>";
						}
						else if(isset($_SESSION['activation.error']))
						{
							$msg = $_SESSION['activation.error'];
							unset($_SESSION['activation.error']);
							
							echo "<div class='alert alert-danger' role='alert'>$msg</div>";
						}
					
					?>
					
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					
					<h2>Advisees</h2>
					<hr />
					<ul class="list-group">
					<?php
						
						$advisees = $user->getAdvisees();
						
						foreach($advisees as $advisee)
						{
							$slotName = $advisee->getName();
							
							$email = $advisee->getEmailAddress();
							
							$id = $advisee->getUserID();
							
							$advisingFormUrl = site_url('Advisingform/index/' . $id);
							
							echo "<li class='list-group-item'><h4>$slotName <small>$email</small></h4> <button type='button' class='btn btn-info pull-right' onclick='activate($id)'>Activate</button></li>";
						}
						
					?>
					</ul>
				</div>
			</div>
		</div>
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
