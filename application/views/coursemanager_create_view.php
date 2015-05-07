<html>
    <head>
        <title>Create Course</title>
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
    <body style="padding: 60px 0px;">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container" style='text-align: left;'>
			<div class="row">
				<div class="col-md-8 col-md-offset-2" style='color: black;'>
					
					<?php
					
						if(isset($_SESSION['coursemanager.create.errormessage']))
						{
							echo "<div class='alert alert-danger'><strong>" . $_SESSION['coursemanager.create.errormessage'] . "</strong></div>";
							
							unset($_SESSION['coursemanager.create.errormessage']);
						}
					
					?>
					
					<h3>Create New Course</h3>
					<p style='text-align: left;'>* Required Fields</p>
					<hr />
					
					<form id="create-course-form" action="<?php echo site_url('Coursemanager/create_submit'); ?>" method="POST">
						<div class="form-group">
							<label>* Course Category</label>
							<select class="form-control" name="courseName">
								
								<?php
								
									foreach($categories as $row)
									{
										$cat = $row['CategoryName'];
										$cor = $row['CourseName'];
										
										echo "<option value='$cor'>$cat</option>";
									}
								
								?>
								
							</select>
						</div>
						<div class="form-group">
							<label>* Course Type</label>
							<select class="form-control" name="courseType">
								
								<?php
								
									foreach($courseTypes as $row)
									{
										$slotName = $row['Name'];
										$value = $row['CourseTypeID'];
										
										echo "<option value='$value'>$slotName</option>";
									}
								
								?>
								
							</select>
						</div>
						<div class="form-group">
							<label>* Course Number</label>
							<input type="text" class="form-control" name="courseNumber" placeholder="e.g. 100">
						</div>
						<div class="form-group">
							<label>* Course Title</label>
							<input type="text" class="form-control" name="courseTitle" placeholder="Enter Course Title">
						</div>
						<div class="form-group">
							<label>Course Description</label>
							<input type="text" class="form-control" name="courseDescription" placeholder="Enter Course Description">
						</div>
						<input class='btn btn-primary' type='submit' name="Submit" />
					</form>
					
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
