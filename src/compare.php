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
	if($job['max_applicants']) {
		$otherApplicant = getApplicants($dbConnect, $jobID, $job['max_applicants']);
		if(count($otherApplicant)) {
			$otherApplicant =  current($otherApplicant);
			$otherScore = calculateXScore($dbConnect, $application['job_id'], $otherApplicant['user_id']);
		}
	}
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
					<td class="fieldLabel">Vergleich</td>
					<td>
						<?php 
							echo( "Total: " . $score['score'] );
							if(isset($otherScore))
								echo("&nbsp;".$otherScore['score']);
							echo("<br>");
							foreach($job['skills'] as $skill )
							{
								echo( htmlspecialchars($skill['path'], ENT_QUOTES, 'ISO-8859-1') );
								echo("&nbsp;");
								$skill_id = $skill['skill_id'];
								echo( array_key_exists($skill_id, $score['weight'] ) ? $score['weight'][$skill_id] : 0);
								if(isset($otherScore)) {
									echo("&nbsp;");
									echo( array_key_exists($skill_id, $otherScore['weight'] ) ? $otherScore['weight'][$skill_id] : 0);
								}
								echo("<br>");
							}
/*
							foreach($score['match'] as $skill )
							{
								$dbSkill = getSkill($dbConnect, $skill['skill_id']);
								echo($dbSkill['name']);
								echo("&nbsp;");
								echo($score['weight'][$skill['skill_id']]);
								echo("<br>");
							}
*/
							if(isset($otherScore)) {
								if( $otherScore['score'] > $score['score'] )
									echo("<br><b>Sie haben nicht genug Punkte.</b>");
								else
									echo("<br><b>Bis jetzt, haben Sie ausreichend Punkte.</b>");
							}
							else
								echo("<br><b>M&ouml;gliherweise haben Sie genug Punkte.</b>");
							
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
