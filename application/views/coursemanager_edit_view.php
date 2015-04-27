<html>
    <head>
        <title>Edit Course</title>
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
		<script>
		
			var deleteURL = '<?php echo site_url('Coursemanager/delete/' . $course->getCourseID()); ?>';
		
			function showDeletePopup()
			{
				$("#popup-wrapper").append('<div class="alert alert-danger alert-dismissible" style="padding-bottom: 10px;" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>Are you sure you want to delete this course?</strong><br /> This can not be undone. Once deleted, all course sections and records of students taking this course will be gone forever.<a href="' + deleteURL + '" class="btn btn-danger">Delete Anyway</a></div>');
			}
			
		</script>
    </head>
    <body style="padding: 60px 0px;">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container" style='text-align: left;'>
			<div class="row">
				<div class="col-md-8 col-md-offset-2" style='color: black;'>
					
					<?php
					
						if(isset($_SESSION['coursemanager.create.errormessage']))
						{
							echo "<div class='alert alert-danger'><strong>" . $_SESSION['coursemanager.edit.errormessage'] . "</strong></div>";
							
							unset($_SESSION['coursemanager.edit.errormessage']);
						}
					
					?>
					
					<button onclick='showDeletePopup()' class='btn btn-danger pull-right'>Delete Course</button>
					
					<h3>Edit Course</h3>
					<p style='text-align: left;'>* Required Fields</p>
					<hr />
					<div id="popup-wrapper">
					
					</div>
					
					
					<form id="create-course-form" action="<?php echo site_url('Coursemanager/edit_submit'); ?>" method="POST">
						<input style='display: none;' name='courseID' value='<?php echo $course->getCourseID(); ?>' />
						<div class="form-group">
							<label>* Course Category</label>
							<select class="form-control" name="courseName">
								
								<?php
								
									foreach($categories as $row)
									{
										$cat = $row['CategoryName'];
										$cor = $row['CourseName'];
										
										if($cor == $course->getCourseName())
										{
											echo "<option value='$cor' selected='selected'>$cat</option>";
										}
										else
										{
											echo "<option value='$cor'>$cat</option>";
										}
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
										$name = $row['Name'];
										$value = $row['CourseTypeID'];
										
										if($courseTypeID == $course->getCourseType())
										{
											echo "<option value='$value' selected='selected'>$name</option>";
										}
										else
										{
											echo "<option value='$value'>$name</option>";
										}
									}
								
								?>
								
							</select>
						</div>
						<div class="form-group">
							<label>* Course Number</label>
							<input type="text" class="form-control" name="courseNumber" placeholder="e.g. 100" value='<?php echo $course->getCourseNumber(); ?>'>
						</div>
						<div class="form-group">
							<label>* Course Title</label>
							<input type="text" class="form-control" name="courseTitle" placeholder="Enter Course Title" value='<?php echo $course->getCourseTitle(); ?>'>
						</div>
						<div class="form-group">
							<label>Course Description</label>
							<input type="text" class="form-control" name="courseDescription" placeholder="Enter Course Description" value='<?php echo $course->getCourseDescription(); ?>'>
						</div>
						<h3>Prerequisites</h3>
						<hr />
						
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Course Corequisites</label>
									<select multiple class="form-control" name="courseCoreqs[]" style='height: 300px;'>
										
										<?php
										
											$coreqs = $course->getCorequisiteCourses();
											
											$coreq_ids = array();
											
											foreach($coreqs as $coreq)
											{
												array_push($coreq_ids, $coreq->getCourseID());
											}
										
											foreach($courses as $row)
											{
												$id = $row->getCourseID();
												
												$name = $row->getCourseName() . " " . $row->getCourseNumber();
												
												if(in_array($row->getCourseID(), $coreq_ids))
												{
													echo "<option value='$id' selected='selected'>$name</option>";
												}
												else
												{
													echo "<option value='$id'>$name</option>";
												}
											}
										
										?>
										
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Course Prerequisites</label>
									<select multiple class="form-control" name="coursePrereqs[]" style='height: 300px;'>
										
										<?php
										
											$prereqs = $course->getPrerequisiteCourses();
											
											$prereqs_ids = array();
											
											foreach($prereqs as $prereq)
											{
												array_push($prereqs_ids, $prereq->getCourseID());
											}
										
											foreach($courses as $row)
											{
												$id = $row->getCourseID();
												
												$name = $row->getCourseName() . " " . $row->getCourseNumber();
												
												if(in_array($row->getCourseID(), $prereqs_ids))
												{
													echo "<option value='$id' selected='selected'>$name</option>";
												}
												else
												{
													echo "<option value='$id'>$name</option>";
												}
											}
										
										?>
										
									</select>
								</div>
							</div>
						</div>
						
						<input class='btn btn-primary' type='submit' name="Submit" />
					</form>
					
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
