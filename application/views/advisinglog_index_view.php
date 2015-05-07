<html>
    <head>
        <title>Advising Log</title>
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
		<script type="text/javascript">

			function PrintElem(elem)
			{
				Popup($(elem).html());
			}

			function Popup(data) 
			{
				var mywindow = window.open('', 'Advising Log', '');
				mywindow.document.write('<html><head><title>Advising Log</title>');
				mywindow.document.write('<link rel="stylesheet" href="<?php echo CSS.'/magic-bootstrapV2_1.css'; ?>" type="text/css" />');
				mywindow.document.write('</head><body >');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

				mywindow.document.close(); // necessary for IE >= 10
				mywindow.focus(); // necessary for IE >= 10

				mywindow.print();
				mywindow.close();

				return true;
			}

		</script>
		<style>
		
			.dropdown-menu li
			{
				text-align: left;
			}
		
		</style>
    </head>
    <body style="padding-top: 60px">
        <?php include_once('application/views/Templates/navbar.php'); ?>
		
		<div class="container">
			<div class="row">
				<div class="col-sm-3">
					<div class="btn-group">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Advisors <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?php echo site_url('Advisinglog/index/all/' . $studentUserID . "/" . $advisingLogEntryType) ?>">All</a></li>
							<?php
								
								foreach($advisors as $advisor)
								{
									$url = site_url('Advisinglog/index/' . $advisor->getUserID() . "/" . $studentUserID . "/" . $advisingLogEntryType);
									
									$name = $advisor->getName();
									
									echo "<li><a href='$url'>$name</a></li>";
								}
							?>
						</ul>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="btn-group">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Students <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?php echo site_url('Advisinglog/index/' . $advisorUserID . "/all/" . $advisingLogEntryType) ?>">All</a></li>
							<?php
								
								foreach($students as $student)
								{
									$url = site_url('Advisinglog/index/' . $advisorUserID . "/" . $student->getUserID() . "/" . $advisingLogEntryType);
									
									$name = $student->getName();
									
									echo "<li><a href='$url'>$name</a></li>";
								}
							?>
						</ul>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="btn-group">
						<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							Log Entry Types <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="<?php echo site_url('Advisinglog/index/' . $advisorUserID . "/" . $studentUserID . "/all") ?>">All</a></li>
							<?php
								
								foreach($types as $type)
								{
									$url = site_url('Advisinglog/index/' . $advisorUserID . "/" . $studentUserID . "/" . $type['AdvisingLogEntryTypeID']);
									
									$name = $type['Name'];
									
									echo "<li><a href='$url'>$name</a></li>";
								}
							?>
						</ul>
					</div>
				</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-info" onclick="PrintElem('#entryLogWrapper')">Print</button>
				</div>
			</div>
			<hr />
			<div class="row">
				<div id="message-wrapper" class="col-xs-12">
					<?php
						if(count($logEntries) < 1)
						{
							echo "<div class='alert alert-warning'><strong>No Advising Log Entries Found</strong></div>";
						}
					?>
				</div>
			</div>
			<div class="row" style="position: relative;">
				<div id="entryLogWrapper" class="col-xs-12">
					<ul class="list-group" style="color: black;">
						<?php
						
							foreach($logEntries as $entry)
							{
								$advisor = new User_model;
								
								$advisor->loadPropertiesFromPrimaryKey($entry->getAdvisorUserID());
								
								$student = new User_model;
								
								$student->loadPropertiesFromPrimaryKey($entry->getStudentUserID());
								
								$timestamp = $entry->getTimestamp();
								
								$content = $timestamp . " - ";
								
								switch($entry->getAdvisingLogEntryType())
								{
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_APPOINTMENT_COMPLETE:
										
										$content .= $student->getName() . " advising appt with " . $advisor->getName() . " complete";
										break;
										
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_APPOINTMENT_CANCELED_BY_STUDENT:
									
										$content .= $student->getName() . " canceled advising appt with " . $advisor->getName();
										break;
										
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_APPOINTMENT_CANCELED_BY_ADVISOR:
									
										$content .= $student->getName() . " advising appt canceled by " . $advisor->getName();
										break;
										
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_FORM_SAVED_BY_STUDENT:
										
										$content .= $student->getName() . " saved advising form";
										break;
										
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_FORM_SAVED_BY_ADVISOR:
									
										$content .= $student->getName() . " advising form saved by " . $advisor->getName();
										break;
										
									case Advising_log_entry_model::ENTRY_TYPE_ADVISING_APPOINTMENT_SIGNED_UP_BY_STUDENT:
										
										$content .= $student->getName() . " signed up for advising appt with " . $advisor->getName();
										break;
								}
								
								echo "<li class='list-group-item' style='text-align: left;'>$content</li>";
							}
						
						?>
					</ul>
				</div>
			</div>
		</div>
        
        <?php include_once('application/views/Templates/footer.php'); ?>	
    </body>
</html>
