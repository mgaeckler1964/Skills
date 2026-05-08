<?php 
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" ); 

	include_once( "includes/tools/tools.php" ); 

	$id = checkField( $_GET, 'id', 0, true );
	$job = getSessionJob( $dbConnect, $id );
	$company_id = $job['company_id'];

	$sessionJobKey = "job_" . $id;

	$hasApplicants = hasApplicants($dbConnect, $id);

	if( $actUser['id'] != $company_id )
		$result = new errorClass( "Keine Berechtigung" );
	else if( $hasApplicants )
		$result = new errorClass( "Es gibt Bewerber:innen" );
	else
		$result = queryDatabase( $dbConnect, "delete from jobs where id = $1 ", array( $id) );

	if(dbOK( $result ))
		deleteDocument( $dbConnect, $job[$jobFileInfo['idFieldName']] );

	if( dbOK( $result ) )
		$result = queryDatabase( $dbConnect, 
			"delete from job_skills where job_id = $1", 
			array($id)
		 );

	if( dbOK( $result ) ) {
		$nextURL = "jobs.php";
		unset($_SESSION[$sessionJobKey]);
		unset($_SESSION['job_id']);
		header( "Location: " . $nextURL );
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Jobangebot L&ouml;schen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( dbOK( $result ) )
				echo "<p>Daten erfolgreich gel&ouml;scht.</p>";
			else
				include "includes/components/error.php";
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
