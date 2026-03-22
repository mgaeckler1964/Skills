<?php
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" ); 
	$id = $actUser['id'];

	include_once( "includes/tools/tools.php" ); 
	$applicant = putSessionApplFiles( putSessionApplicant( $_POST ), $_FILES );

	if( $_POST['func'] == SAVE_FUNC )
	{
		// create the record if not yet exists
		if( !getApplicantCount($dbConnect,$id) )
		{
			queryDatabase( $dbConnect,
				"insert into applicants (id) values ($1)",
				array( $id )
			);
		}
		
		$country = urlencode(strtoupper($applicant['country']));
		$symbol = urlencode(strtoupper($applicant['symbol']));
		$regionID = getRegionID($dbConnect, $country, $symbol );
		$birthday = $applicant['birthday'];
		$education = $applicant['education'];
		if( $education == "" || !$education )
			$education = null;
		$position = $applicant['position'];
		if( $position == "" || !$position )
			$position = null;
		
		$title = $applicant['title'];
		if( $title == "" || !$title )
			$title = null;
		$description = $applicant['description'];

		$planed_time = $applicant['planed_time']!=="" ? $applicant['planed_time'] : null;
		$mobility = $applicant['mobility']!=="" ? $applicant['mobility'] : null;
		$open = $applicant['open']!=="" ? $applicant['open'] : null;
		
		$result = queryDatabase( $dbConnect, 
			"update applicants set birthday = $2, region = $3, education = $4, title = $5, description = $6, ".
				"planed_time=$7, mobility=$8, open=$9, position=$10 ".
			"where id = $1", 
			array( 
				$id, urlencode($birthday), $regionID, $education, $title, urlencode($description), 
				$planed_time, $mobility, $open, $position 
			)
		 );


		if( !is_object( $result ) )
		{
			$result = queryDatabase( $dbConnect, 
				"delete from appl_skills where user_id = $1", 
				array($id)
			 );
		}
		if( !is_object( $result ) )
		{
			foreach($applicant['skills'] as $skill )
			{
				$asID = getNextID( $dbConnect, "appl_skills", "id" );
				$start_y = $skill["start_y"] !== "" ? $skill["start_y"] : null;
				$end_y = $skill["end_y"] !== "" ? $skill["end_y"] : null;
				$part = $skill["part"] !== "" ? $skill["part"] : null;
				$result = queryDatabase( $dbConnect, 
					"insert into appl_skills (".
						"id, user_id, skill_id, start_y, end_y, part".
					") values (".
						"$1, $2, $3, $4, $5, $6" .
					")",
					array( $asID, $id, $skill["skill_id"], $start_y, $end_y, $part )
				 );
			}
		}

		if( $applicant[$applCvInfo['uiDeleteName']] )
			deleteDocument( $dbConnect, $applicant[$applCvInfo['idFieldName']] );
		if( !is_object( $result ) && array_key_exists($applCvInfo['uiFieldName'], $applicant) )
		{
			saveDocument( 
				$dbConnect, $id, $id, USER_CV, 
				$applicant[$applCvInfo['typeFieldName']], 
				$applicant[$applCvInfo['uiFieldName']], 
				getUserCV($id) 
			);
		}

		if($applicant[$applMotInfo['uiDeleteName']])
			deleteDocument( $dbConnect, $applicant[$applMotInfo['idFieldName']] );
		if( !is_object( $result ) && array_key_exists($applMotInfo['uiFieldName'], $applicant) )
		{
			saveDocument( 
				$dbConnect, $id, $id, USER_MOTIV, 
				$applicant[$applMotInfo['typeFieldName']], 
				$applicant[$applMotInfo['uiFieldName']],
				getUserMotivation($id) 
			);
		}

		unset($_SESSION['applicant']);
		$nextURL = "index.php";
	}
	else if( $_POST['func'] == ADD_SKILL_FUNC )
	{
		$nextURL = "skills.php?func=" . ADD_SKILL_FUNC;
		$result = true;
	}
	else if( $_POST['func'] == DEL_SKILL_FUNC )
	{
		$skill_id = $_POST['skill_id'];
		$newSkills = array();
		foreach($applicant['skills'] as $skill )
		{
			if( $skill["skill_id"] != $skill_id )
			{
				$newSkills[] = $skill;
			}
		}
		$applicant['skills'] = $newSkills;
		$_SESSION['applicant'] = $applicant;
		$nextURL = "applicant.php";
		$result = true;
	}
	else if( $_POST['func'] == CANCEL_FUNC )
	{
		unset($_SESSION['applicant']);
		$nextURL = "index.php";
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
			$title = "Bewerber Speichern";
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
				echo("<p>");
				print_r($_FILES);
				echo("</p>");
				echo("<p>");
				print_r($_POST);
				echo("</p>");
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
