<?php 
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" ); 

	include_once( "includes/tools/tools.php" ); 
	$job = putSessionJobFile( putSessionJob( $_POST ), $_POST, $_FILES );

	$id = $_POST['id'];
	$sessionJobKey = "job_" . $id;

	if( $_POST['func'] == SAVE_FUNC )
	{
		$job_title = urlencode($job["job_title"]);
		$department = urlencode($job["department"]);
		$position = $job["position"];
		$description = urlencode($job["description"]);
		$company_id = $job["company_id"];
		$visible = $job["visible"];
		$open_date = $job["open_date"];
		$close_date = $job["close_date"];
		$db_close_date = $job["db_close_date"];
		$max_applicants = $job["max_applicants"];

		$hasApplicants = $id ? hasApplicants($dbConnect, $id) : false;
		
		if( $open_date < time() )
			$visible = 1;
		if( $hasApplicants )
		{
			$visible = 1;
			if( $open_date > time() )
				$open_date = time();
			if( $close_date < $db_close_date )
				$close_date = $db_close_date;
		}

		if( !$company_id )
			$company_id = $actUser['id'];

		if( $actUser['id'] != $company_id && !$actUser['administrator'] )
		{
			$error = "Keine Berechtigung";
			$result = false;
		}
		else if( !$id )
		{
			$id = getNextID( $dbConnect, "jobs", "id" );
			$result = queryDatabase( $dbConnect,
				"insert into jobs (" .
					"id, job_title, position, description, visible, status, company_id, open_date, close_date, max_applicants, department " .
				")" .
				"values" .
				"(" .
					"$1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11" .
				")",
				array( 
					$id, $job_title, $position, $description, $visible, 0, $company_id, $open_date, $close_date, $max_applicants, $department
				)
			);
		}
		else
		{
			$result = queryDatabase( $dbConnect,
				"update jobs " .
				"set job_title = $3, " .
					"description = $4, " .
					"position = $5, " .
					"visible = $6, " .
					"open_date = $7, " .
					"close_date = $8, " .
					"max_applicants = $9, " .
					"department = $10 " .
				"where id = $1 and company_id = $2 ",
				array( 
					$id, $company_id, $job_title, $description, $position, 
					$visible, $open_date, $close_date, $max_applicants, $department
				)
			);
		}

		if($job[$jobFileInfo['uiDeleteName']])
			deleteDocument( $dbConnect, $job[$jobFileInfo['idFieldName']] );
		if( !is_object( $result ) && array_key_exists($jobFileInfo['uiFieldName'], $job) )
		{
			saveDocument( 
				$dbConnect, $actUser['id'], $id, JOB_DESCR, 
				$job[$jobFileInfo['typeFieldName']], 
				$job[$jobFileInfo['uiFieldName']],
				getJobDescr($id) 
			);
		}

		if( !is_object( $result ) )
		{
			$result = queryDatabase( $dbConnect, 
				"delete from job_skills where job_id = $1", 
				array($id)
			 );
		}
		if( !is_object( $result ) )
		{
			foreach($job['skills'] as $skill )
			{
				$jsID = getNextID( $dbConnect, "job_skills", "id" );
				$part = $skill["part"] !== "" ? $skill["part"] : null;
				$result = queryDatabase( $dbConnect, 
					"insert into job_skills (".
						"id, job_id, skill_id, part".
					") values (".
						"$1, $2, $3, $4" .
					")",
					array( $jsID, $id, $skill["skill_id"], $part )
				 );
			}
		}
		$nextURL = "jobs.php";
		unset($_SESSION[$sessionJobKey]);
		unset($_SESSION['job_id']);
	}
	else if( $_POST['func'] == ADD_JOB_SKILL_FUNC )
	{
		$_SESSION['job_id'] = $id;
		$nextURL = "skills.php?func=" . ADD_JOB_SKILL_FUNC ;
		$result = true;
	}
	else if( $_POST['func'] == DEL_SKILL_FUNC )
	{
		$sessionJobKey = "job_" . $id;

		$skill_id = $_POST['skill_id'];
		$newSkills = array();
		foreach($job['skills'] as $skill )
		{
			if( $skill["skill_id"] != $skill_id )
			{
				$newSkills[] = $skill;
			}
		}
		$job['skills'] = $newSkills;
		$_SESSION[$sessionJobKey] = $job;
		$nextURL = "jobedit.php?id=".$id;
		$result = true;
	}
	else if( $_POST['func'] == CANCEL_FUNC )
	{
		unset($_SESSION[$sessionJobKey]);
		unset($_SESSION['job_id']);
		$nextURL = "jobs.php";
		$result = true;
	}


	if( is_object( $result ) )
	{
		$error = $result;
		$result = false;
	}
	else
	{
		header( "Location: " . $nextURL );
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Jobangebot Speichern";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( $result )
				echo "<p>Daten erfolgreich gespeichert.</p>";
			else
				include "includes/components/error.php";
			echo("<p>files<br>");
			print_r($_FILES);
			echo("</p>");
			echo("<p>post<br>");
			print_r($_POST);
			echo("</p>");
			echo("<p>Job<br>");
			print_r($job);
			echo("</p>");
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
