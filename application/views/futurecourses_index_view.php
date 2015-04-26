<html>
    <head>
        <title>Import Future Course Offerings</title>
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
		<script src="<?php echo JS . '/dropzone.js'; ?>"></script>
		<script>
			$(document).ready(function() {
				
				$("#year-selector").val(new Date().getFullYear());
				
				var upload_files = false;
				
				Dropzone.autoDiscover = false;
				
				var dropZone = new Dropzone("#upload-dropzone", {
					maxFiles: 1,
					uploadMultiple: false,
					url: '<?php echo site_url('Futurecourses/submit'); ?>',
					method: 'post',
					paramName: 'boss_file',
					clickable: true,
					maxFilesize: 250,
					previewsContainer: '#previews',
					
					sending: function(file, xhr, formData) {
						
						formData.append('year', $("#year-selector").val());
					
						formData.append('quarter', $("#quarter-selector").val());
						
						$("#progress-wrapper").show();
					},
					
					success: function(file, response) {
						$("#progress-wrapper").hide();
						$("#file-upload-progress-bar").attr("aria-valuenow", 0);
						$("#message-wrapper").append("<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + response + "</div>");
						
						this.removeAllFiles();
					},
					
					error: function(file, errorMessage) {
						$("#progress-wrapper").hide();
						$("#file-upload-progress-bar").attr("aria-valuenow", 0);
						$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorMessage + "</div>");
						
						this.removeAllFiles();
					},
					
					canceled: function(file) {
						$("#progress-wrapper").hide();
						$("#file-upload-progress-bar").attr("aria-valuenow", 0);
						
						this.removeAllFiles();
					},
					
					uploadProgress: function(file, progress, bytesSent) {
						console.log("PROGRESS: " + progress);
						$("#file-upload-progress-bar").attr("aria-valuenow", progress);
						$("#file-upload-progress-bar").html(progress + "%");
					}
					
				});
				
				$('input[type=file]').on('change', function(event) {
					upload_files = event.target.files;
				});
				
				$("#fallback-upload").submit(function(event) {
					event.preventDefault();
					
					if(upload_files == false && upload_files.length > 0)
						return;
					
					var data = new FormData();
					
					data.append('year', $("#year-selector").val());
					
					data.append('quarter', $("#quarter-selector").val());
					
					data.append("boss_file", upload_files[0]);
					
					$("#progress-wrapper").show();
					
					$.ajax({
						url: '<?php echo site_url('Bossimport/submit'); ?>',
						method: 'POST',
						data: data,
						cache: false,
						processData: false, 
						contentType: false,
						success: function(data, textStatus, jqXHR) {
							$("#progress-wrapper").hide();
							$("#file-upload-progress-bar").attr("aria-valuenow", 0);
							$("#message-wrapper").append("<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + data + "</div>");
						},
						error: function(jqXHR, textStatus, errorThrown) {
							$("#progress-wrapper").hide();
							$("#file-upload-progress-bar").attr("aria-valuenow", 0);
							$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorThrown + "</div>");
						}
					});
				});
			});
			
		</script>
		<style>
			
			#upload-dropzone {
				cursor: pointer;
				background-image: url('<?php echo IMG . '/dropzone-img.png'; ?>');
				background-position: center center;
				background-size: 80% auto;
				background-repeat: no-repeat no-repeat;
				min-height: 300px;
			}
			
			div#body *
			{
				color: #000;
			}
			
			#progress-wrapper
			{
				position: fixed;
				background-color: rgba(0,0,0,0.3);
				z-index: 10000;
				width: 100%;
				height: 100%;
				display: none;
				top: 0px;
			}
			
			#progress-wrapper #progress-wrapper-table
			{
				display: table;
				width: 100%;
				height: 100%;
			}
			
			#progress-wrapper #progress-wrapper-table #progress-wrapper-container-wrapper
			{
				display: table-cell;
				vertical-align: middle;
				height: 100%;
			}
			
		</style>
    </head>
    <body style="padding-top: 60px">
		<div id="progress-wrapper">
			<div id="progress-wrapper-table">
				<div id="progress-wrapper-container-wrapper">
					<div class="container">
						<div id="progress-dialog" class="col-sm-10 col-sm-offset-1 alert alert-info" style="padding: 12px;">
							<h3>File upload progress</h3>
							<div class="progress" role="alert">
								<div id="file-upload-progress-bar" class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-value="100" style="width: 100%; color: #FFF;">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<h2 style="text-align: left;">Please upload the Future Course Offerings Data file</h2>
							<div class="row">
								<div id="message-wrapper" class="col-xs-12">
									
								</div>
							</div>
							<hr />
							<h4 style='text-align: left;'><strong style='color: red;'>Be sure to enter the correct Academic Quarter information before uploading the file</strong></h4>
							<div class="row">
								<div class="col-md-8">
									<div class="form-group" style='text-align: left;'>
										<label>Academic Quarter</label>
										<select id="quarter-selector" class="form-control" name="year">
											<option value="Fall">Fall</option>
											<option value="Winter">Winter</option>
											<option value="Spring">Spring</option>
											<option value="Summer">Summer</option>
										</select>
									</div>
									<div class="form-group" style='text-align: left;'>
										<label>Academic Year</label>
										<input id="year-selector" class="form-control" name="year" />
									</div>
								</div>
							</div>
							<div id="upload-dropzone" class="jumbotron">
								<div id="previews" class="hide">
								
								</div>
							</div>
						</div>
					</div>
					<br />
					<hr />
					<br />
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<form id="fallback-upload" action="<?php echo site_url('Futurecourses/submit'); ?>">
								<div class="form-group" style="text-align: left;">
									<label>If the above form doesn't work, try uploading here</label>
									<input class="input" type="file" name="boss_file" />
									<input class="btn btn-primary pull-left" type="submit" name="Submit" style="color: #FFF;" />
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
