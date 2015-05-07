<html>
    <head>
        <title>Backup / Restore System</title>
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
			
			function loading()
			{
				$("#progress-wrapper").show();
			}
		
			$(document).ready(function() {
				
				var upload_files = false;
				
				Dropzone.autoDiscover = false;
				
				var dropZone = new Dropzone("#upload-dropzone", {
					maxFiles: 1,
					uploadMultiple: false,
					url: '<?php echo site_url('Backuprestore/submit'); ?>',
					method: 'post',
					paramName: 'boss_file',
					clickable: true,
					maxFilesize: 250,
					previewsContainer: '#previews',
					
					sending: function(file) {
						$("#progress-wrapper").show();
					},
					
					success: function(file, response) {
						$("#file-upload-progress-bar").attr("aria-valuenow", 0);
						location.reload();
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
							location.reload();
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
			
			.btn
			{
				width: auto !important;
				display: inline-block;
				color: white !important;
			}
			
		</style>
    </head>
    <body style="padding: 60px 0px;">
		<div id="progress-wrapper">
			<div id="progress-wrapper-table">
				<div id="progress-wrapper-container-wrapper">
					<div class="container">
						<div id="progress-dialog" class="col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5">
							<img src="<?php echo IMG . "/loading.gif"; ?>" class="img-responsive center-block"></img>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container">
			<div class="row">
				<div class="col-md-6">
					
					<div class="row">
						<div class="col-xs-8">
							<h3 style='text-align: left;'>Backup files on Server</h3>
						</div>
						<div class="col-xs-4">
							<a onclick='loading()' class='btn btn-success' href='<?php echo site_url('Backuprestore/backup'); ?>'>Create Backup</a>
						</div>
					</div>
					<hr />
					<ul class="list-group">
						<?php
							
							foreach($files as $file)
							{
								$title = $file['title'];
								$path = $file['file'];
								
								$restoreUrl = site_url('Backuprestore/restore/' . $path);
								
								$downloadUrl = site_url('Backuprestore/download/' . $path);
								
								$deleteUrl = site_url('Backuprestore/delete/' . $path);
								
								$class = ($file['valid']) ? "" : " color: red; font-weight: bold;";
								
								echo "<li style='text-align: left;' class='list-group-item'><h4 style='text-align: left; display: inline-block;$class'>$title</h4><div class='pull-right'><a onclick='loading()' href='$restoreUrl' class='btn btn-primary' style='color: white;'>Restore</a> <a href='$downloadUrl' class='btn btn-info'>Download</a> <a onclick='loading()' href='$deleteUrl' class='btn btn-danger'>Delete</a></div></li>";
							}
							
						?>
					</ul>
					
				</div>
				<div class="col-md-6">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<h2 style="text-align: left;">Upload a Backup Data file</h2>
							<div class="row">
								<div id="message-wrapper" class="col-xs-12">
									
								</div>
							</div>
							<hr />
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
							<form id="fallback-upload" action="<?php echo site_url('Backuprestore/submit'); ?>">
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
