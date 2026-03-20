<?php
	require_once( "includes/components/login.php" ); 
	$skill_id = $_GET['id'];
	$func =  $_GET['func'];
	
	include_once( "includes/tools/tools.php" ); 

	if( $func == ADD_SKILL_FUNC )
	{
		$user_id = $actUser['id'];
		$applicant = getSessionApplicant( $dbConnect, $user_id );
		if( !array_key_exists( "skills", $applicant ) )
			$applicant['skills'] = array();
	
		$skills = $applicant['skills'];
	}
	else if($func == ADD_JOB_SKILL_FUNC )
	{
		startSession();
		$job_id = $_SESSION['job_id'];
		$job = getSessionJob( $dbConnect, $job_id );
		if( !array_key_exists( "skills", $job ) )
			$job['skills'] = array();
	
		$skills = $job['skills'];
	}
	
	$found = false;
	foreach( $skills as $skill )
	{
		if( $skill["skill_id"] == $skill_id )
		{
			$found = true;
			break;
		}
	}
	if( !$found )
	{
		$skills[] = array(
			"id" => $skill_id,
			"skill_id" => $skill_id,
			"start_y" => "",
			"end_y" => "",
			"part" => "",
			"path" => getSkillPathStr($dbConnect, $skill_id)
		);
		


		if( $func == ADD_SKILL_FUNC )
		{
			$applicant['skills'] = sortSkills( $skills );
			$_SESSION['applicant'] = $applicant;
		}
		else if($func == ADD_JOB_SKILL_FUNC )
		{
			$job['skills'] = sortSkills( $skills );
			$sessionJobKey = "job_" . $job_id;
			$_SESSION[$sessionJobKey] = $job;
		}
	}
	
	$nextURL = $_SESSION['backURL'];
	header( "Location: " . $nextURL );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Skill Speichern";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			echo "<p>Daten erfolgreich gespeichert.</p>";
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
