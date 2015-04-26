<html>
    <head>
        <title>Manage Courses</title>
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
				<div class="col-xs-12">
					
					<button id="categories-menu" class='btn btn-info pull-left' type="button" data-toggle="dropdown" aria-expanded="false">
						Categories
					</button>
					<ul class="dropdown-menu" role="menu" aria-labelledby="categories-menu" style='max-height: 300px; overflow-y: auto;'>
					
						<?php
							
							foreach($categories as $row)
							{
								echo "<li role='presentation'><a role='menuitem' href='" . site_url('Coursemanager/index/' . $row['CourseName']) . "'>" . $row['CategoryName'] . "</a></li>";
							}
						
						?>
					
					</ul>
					
					<a class='btn btn-success pull-right' href='<?php echo site_url('Coursemanager/create'); ?>'>Create New</a>
					
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					
					<h3>All Courses <?php if($category != null) { echo "in " . $category; } ?></h3>
					<hr />
					<ul class='list-group'>
					<?php 
						
						if(count($courses) > 0)
						{
							foreach($courses as $course)
							{
								echo "<a class='list-group-item' href='" . site_url('Coursemanager/edit/' . $course->getCourseID()) . "'>" . $course->getCourseName() . " " . $course->getCourseNumber() . (($course->getCourseTitle() != null) ? " - " . $course->getCourseTitle() : "" ) . "</a>";
							}
						}
						else
						{
							echo "<div class='alert alert-warning'><strong>No courses found for this category</strong></div>";
						}
						
					?>
					</ul>
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
