<?php
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/tools.php" ); 
	$jobID = $_GET['id'];
	if( !is_numeric($jobID) )
		$jobID=0;
		
	if( $jobID )
	{
		clrSessionJob($jobID);
		$job = getJob( $dbConnect, $jobID );
	}
	
	$applID = checkField($_GET, "appl_id", 0);
	$application = getApplication( $dbConnect, $applID );
	if( !$application )
	{
		if($applID)
		{
			$jobID = null;
			$job = null;
		}
	}
	else if( $application['job_id']!=$jobID || $application['user_id'] != $actUser['id'] )
	{
		$jobID = null;
		$job = null;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Ihre Bewerbung";
			include_once( "includes/components/defhead.php" );
		?>
		<script>
			function cancelApplication()
			{
				if( confirm("Soll die Bewerbung zurückgezogen werden?") )
				{
					document.getElementById("fnc").value = "<?php echo CANCEL_FUNC; ?>";
					document.getElementById("frm").submit();
				}
			}
		</script>
	</head>
	<body>
		<?php 
			include( "includes/components/headerlines.php" ); 
		?>

		<?php if( jobOK($job, $jobID) ) { ?>
			<form action="apply2.php" method="post" enctype="multipart/form-data" id="frm">
				<input type="hidden" name="jobID" value="<?php echo $jobID; ?>">
				<input type="hidden" name="appl_id" value="<?php echo $applID; ?>">
				<input type="hidden" name="func" value="save" id="fnc">
	
				<table>
					<tr>
						<td class="fieldLabel">Name</td>
						<td><?php echo getFullname4Html($actUser); ?></td>
					</tr>
	
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">Firma</td>
						<td><?php echo htmlspecialchars($job['company_name'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
					</tr>
					<tr>
						<td class="fieldLabel">Abteilung</td>
						<td><?php echo htmlspecialchars($job['department'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
					</tr>
					<tr>
						<td class="fieldLabel">Jobbezeichnung</td>
						<td><?php echo htmlspecialchars($job['job_title'], ENT_QUOTES, 'ISO-8859-1'); ?></td>
					</tr>
					
					<tr>
						<td class="fieldLabel">Lebenslauf</td>
						<td><?php writeFileInput($application, $applCvInfo, false, false ); ?></td>
						<td>Wenn Sie hier keine Datei auswählen, wird Ihr allgemeiner Lebenslauf gezeigt.</td>
					</tr>
					<tr>
						<td class="fieldLabel">Motivation</td>
						<td><?php writeFileInput($application, $applMotInfo, false, false ); ?></td>
						<td>Wenn Sie hier keine Datei auswählen, wird Ihre allgemeine Motivation gezeigt.</td>
					</tr>

					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					
					<?php if($application) { ?>
						<tr>
							<td class="fieldLabel">Datum</td>
							<td><?php echo formatTimeStamp($application['appl_date']); ?></td>
						</tr>
					<?php } ?>

					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">&nbsp;</td>
						<td>
							<input type="submit" value="Abschicken">
							<?php if($application) { ?>
								<input type='button' onClick='cancelApplication();' value='Zur&uuml;ckziehen'>
							<?php } ?>
							<input type='button' onClick='window.history.back();' value='Abbruch'>
						</td>
					</tr>
				</table>
			</form>
		<?php } else { ?>
			Ung&uuml;ltige Jobid, Job oder Bewerbung nicht gefunden.
		<?php } ?>

		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
