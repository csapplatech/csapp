<html>
    <head>
        <title>Import BOSS Data</title>
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
				
				Dropzone.autoDiscover = false;
				
				var dropZone = new Dropzone("#upload-dropzone", {
					maxFiles: 1,
					uploadMultiple: false,
					url: '<?php echo site_url('Bossimport/submit'); ?>',
					method: 'post',
					paramName: 'boss_file',
					clickable: true,
					maxFilesize: 250,
					previewsContainer: '#previews',
					
					sending: function(file) {
						$("#progress-wrapper").show();
						console.log("SENDING");
					},
					
					success: function(file, response) {
						$("#progress-wrapper").hide();
						$("#message-wrapper").append("<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + response + "</div>");
					},
					
					error: function(file, errorMessage) {
						$("#progress-wrapper").hide();
						
						this.removeAllFiles();
						
						$("#message-wrapper").append("<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>" + errorMessage + "</div>");
					},
					
					canceled: function(file) {
						$("#progress-wrapper").hide();
					},
					
					uploadProgress: function(file, progress, bytesSent) {
						console.log("UPLOAD PROGRESS");
					}
					
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
				height: 250px;
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
			
		</style>
    </head>
    <body style="padding-top: 60px">
		<div id="progress-wrapper">
			<div id="progress-wrapper-table">
				<div id="progress-dialog">
					
				</div>
			</div>
		</div>
        <?php include_once('application/views/Templates/navbar.php'); ?>
		<div id="body" class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<h2 style="text-align: left;">Please upload the BOSS Data file</h2>
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
					<div class="row">
						<div class="col-md-8 col-md-offset-2">
							<form id="fallback-upload" action="<?php echo site_url('Bossimport/submit'); ?>">
								<div class="form-group" style="text-align: left;">
									<label>If the above form doesn't work, try uploading here</label>
									<input class="input"type="file" name="boss_file" />
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
