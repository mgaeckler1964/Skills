<?php 
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/tools.php" ); 
	$jobID = $_GET['id'];
	if( !is_numeric($jobID) )
		$jobID=0;
		
	if( $jobID )
	{
		$job = getJob( $dbConnect, $jobID );
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Ihre Bewerbung";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php include( "includes/components/headerlines.php" ); ?>

		<?php if( jobOK($job, $jobID) ) { ?>
			<form action="apply2.php" method="post" enctype="multipart/form-data" id="frm">
				<input type="hidden" name="jobID" value="<?php echo $jobID; ?>">
	
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
					
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">Lebenslauf</td>
						<td><input type="file" name="cv"></td>
						<td>Wenn Sie hier keine Datei auswählen, wird Ihr allgemeiner Lebenslauf gezeigt.</td>
					</tr>
					<tr>
						<td class="fieldLabel">Motivation</td>
						<td><input type="file" name="motivation"></td>
						<td>Wenn Sie hier keine Datei auswählen, wird Ihre allgemeine Motivation gezeigt.</td>
					</tr>
					
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">&nbsp;</td>
						<td>
							<input type="submit" value="Abschicken">
							<input type='button' onClick='window.history.back();' value='Abbruch'>
						</td>
					</tr>
				</table>
			</form>
		<?php } else { ?>
			Ung&uuml;ltige Jobid oder Job nicht gefunden.
		<?php } ?>

		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
