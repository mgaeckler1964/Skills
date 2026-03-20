<?php 
	include_once( "includes/components/login.php" ); 
	$jobID = $_POST['jobID'];
	if( !is_numeric($jobID) )
		$jobID=0;

	$id = 0;

	$nextURL = "jobs.php?mode=browse";
	
	if( $jobID>0 )
	{
		$id = getNextID( $dbConnect, "application", "id" );

		$result = queryDatabase( $dbConnect,
			"insert into application (" .
				"id, job_id, user_id, appl_date " .
			")" .
			"values" .
			"(" .
				"$1, $2, $3, $4" .
			")",
			array( 
				$id, $jobID, $actUser['id'], time()
			)
		);
	}
	else
	{
		$result = false;
		$error = "Kein Job";
	}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Bewerbung Speichern";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( $result )
				echo "<p>Daten erfolgreich gespeichert. Viel Gl&uuml;ck</p>";
			else
				include "includes/components/error.php";
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
