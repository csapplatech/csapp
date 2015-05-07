<html>
    <head>
        <title>Manage Program Chair</title>
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
    </head>
    <body style="padding-top: 60px">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div id="message-wrapper" class="col-xs-12">
				
				</div>
			</div>
			<div id="lists-container" class="row" style="position: relative;">
				<div class="col-md-6">
					<h2>Available Curriculums</h2>
					<p style="color: black;">Click on a curriculum to select it</p>
					<hr />
					<ul class="list-group">
						<?php
						
							foreach($unselectedCurriculums as $curriculum)
							{
								$name = $curriculum->getName();
								
								$url = site_url('Selectcurriculum/add/' . $curriculum->getCurriculumID());
								
								echo "<a class='list-group-item' href='$url' style='color: black;'>$name</a>";
							}
						
						?>
					</ul>
				</div>
				<div class="col-md-6">
					<h2>Selected Curriculum</h2>
					<p style="color: black;">Click on a curriculum to remove it</p>
					<hr />
					<ul class="list-group">
						<?php
						
							foreach($selectedCurriculums as $curriculum)
							{
								$name = $curriculum->getName();
								
								$url = site_url('Selectcurriculum/remove/' . $curriculum->getCurriculumID());
								
								echo "<a class='list-group-item' href='$url' style='color: black;'>$name</a>";
							}
						
						?>
					</ul>
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
