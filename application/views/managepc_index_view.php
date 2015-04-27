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
		<script>
			
			var advisors = [];
			var programChairs = [];
			
			function refreshLists()
			{
				$("#advisors-select-box").empty();
				$("#programchairs-select-box").empty();
				
				$.each(advisors, function(index, elem) {
					$("#advisors-select-box").append("<a id='" + elem.userid + "' class='list-group-item advisor-item' href='#'>" + elem.name + "</li>");
				});
				
				$.each(programChairs, function(index, elem) {
					$("#programchairs-select-box").append("<a id='" + elem.userid + "' class='list-group-item programchair-item' href='#'>" + elem.name + "</li>");
				});
			}
			
			$(document).ready(function() {
				
				$.ajax({
					url: '<?php echo site_url('Managepc/getLists'); ?>',
					method: 'GET',
					beforeSend: function() {
						$("#loading-wrapper").show();
					},
					success: function(data, textStatus, jqXHR) {
						
						advisors = data.advisors;
						programChairs = data.programChairs;
						
						refreshLists();
					},
					error: function(jqXHR, textStatus, errorThrown) {
						$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorThrown + "</div>");
					},
					complete: function(jqXHR, textStatus) {
						$("#loading-wrapper").hide();
					}
				});
				
				$("#lists-container").on('click', '.advisor-item', function(event) {
					event.preventDefault();
					
					var userid = event.target.id;
					
					var data = { userid: userid };
					
					$.ajax({
						url: '<?php echo site_url('Managepc/addProgramChair'); ?>',
						method: 'POST',
						data: data,
						beforeSend: function() {
							$("#loading-wrapper").show();
						},
						success: function(data, textStatus, jqXHR) {
							
							$.each(advisors, function(index, elem){
								if(typeof elem !== 'undefined' && elem.userid == userid)
								{
									advisors.splice(index, 1);
									programChairs.push(elem);
								}
							});
							
							refreshLists();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							
							$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorThrown + "</div>");
						},
						complete: function(jqXHR, textStatus) {
							$("#loading-wrapper").hide();
						}
					});
				});
				
				$("#lists-container").on('click', '.programchair-item', function(event) {
					event.preventDefault();
					
					var userid = event.target.id;
					
					var data = { userid: userid };
					
					$.ajax({
						url: '<?php echo site_url('Managepc/removeProgramChair'); ?>',
						method: 'POST',
						data: data,
						beforeSend: function() {
							$("#loading-wrapper").show();
						},
						success: function(data, textStatus, jqXHR) {
							
							$.each(programChairs, function(index, elem){
								if(typeof elem !== 'undefined' && elem.userid == userid)
								{
									advisors.push(elem);
									programChairs.splice(index, 1);
								}
							});
							
							refreshLists();
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorThrown + "</div>");
						},
						complete: function(jqXHR, textStatus) {
							$("#loading-wrapper").hide();
						}
					});
				});
				
			});
			
		</script>
		<style>
			
			#loading-wrapper
			{
				position: absolute;
				display: none;
				width: 100%;
				height: 100%;
				z-index: 1050;
			}
			
			#loading-wrapper #loading-wrapper-table
			{
				display: table;
				width: 100%;
				height: 100%;
			}
			
			#loading-wrapper #loading-wrapper-table #loading-wrapper-table-cell
			{
				display: table-cell;
				vertical-align: middle;
				text-align: center;
			}
			
		</style>
    </head>
    <body style="padding-top: 60px">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div id="message-wrapper" class="col-xs-12">
				
				</div>
			</div>
			<div id="lists-container" class="row" style="position: relative;">
				<div id="loading-wrapper">
					<div id="loading-wrapper-table">
						<div id="loading-wrapper-table-cell">
							<img class="center-block" style="width: 150px; height: auto;" src="<?php echo IMG . "/loading.gif"; ?>" />
							<h3>Loading...</h3>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<h2>Active Advisors</h2>
					<p style="color: black;">Click on an Advisor to add to Program Chairs</p>
					<hr />
					<div id="advisors-select-box" class="list-group">
						
					</div>
				</div>
				<div class="col-md-6">
					<h2>Program Chairs</h2>
					<p style="color: black;">Click on a Program Chair to remove from Program Chairs</p>
					<hr />
					<div id="programchairs-select-box" class="list-group">
					
					</div>
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
