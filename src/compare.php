<?php
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/tools.php" ); 
	$id = checkField($_GET, "id", 0);
	if( !is_numeric($id) )
		$id=0;
		
	$application = getApplication( $dbConnect, $id );

	$jobID = $application['job_id'];
	$job = getJob( $dbConnect, $jobID );
	
	$score = calculateXScore($dbConnect, $application['job_id'], $application['user_id']);
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
		<?php 
			include( "includes/components/headerlines.php" ); 
		?>

		<?php if( jobOK($job, $jobID) ) { ?>

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
				
				<?php if($application) { ?>
					<tr>
						<td class="fieldLabel">Datum</td>
						<td><?php echo formatTimeStamp($application['appl_date']); ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td class="fieldLabel">Score</td>
					<td>
						<?php 
							echo( "Total: " . $score['score']."<br>" );
							foreach($score['match'] as $skill )
							{
								$dbSkill = getSkill($dbConnect, $skill['skill_id']);
								echo($dbSkill['name']);
								echo("&nbsp;");
								echo($score['weight'][$skill['skill_id']]);
								echo("<br>");
							}
						?>
					</td>
				</tr>
			</table>

		<?php } else { ?>
			Ung&uuml;ltige Jobid, Job oder Bewerbung nicht gefunden.
		<?php } ?>

		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
