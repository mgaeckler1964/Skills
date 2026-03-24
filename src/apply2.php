<?php 
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	include_once( "includes/components/login.php" ); 
	$jobID = $_POST['jobID'];
	if( !is_numeric($jobID) )
		$jobID=0;
	
	include_once( "includes/tools/tools.php" ); 
	clrSessionJob($jobID);

	$id = checkField($_POST, "appl_id", 0);
	$func = checkField($_POST, "func", "save");

	$delCV = checkBoolField($_POST, $applCvInfo["uiDeleteName"]);
	$delMot = checkBoolField($_POST, $applMotInfo["uiDeleteName"]);

	$nextURL = "jobs.php";
	
	if( $jobID>0 )
	{
		$job=getJob($dbConnect, $id);
		if( jobOK($job, $id ) )
		{
			$open_date = $job['open_date'];
			$close_date = $job['close_date'];
			if($open_date > time() || $close_date < time )
			{
				$job = null;
				$jobID = 0;
			}
		}
		else
		{
			$job = null;
			$jobID = 0;
		}
	}
	
	if( $jobID>0 && $id )
	{
		$application = getApplication($dbConnect, $id );
		if( !$application )
		{
			$result = false;
			$jobID=0;
		}
		else if($application['user_id'] != $actUser['id'] )
		{
			$result = false;
			$jobID=0;
		}
	}
	
	if( $jobID>0 )
	{
		if( !$id )
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
			$result = true;
		}
	
		if($func == CANCEL_FUNC)
		{
			$result = queryDatabase($dbConnect, "delete from application where id=$1", array($id) );
			$delCV = true;
			$delMod = true;
		}
		
		if($delCV)
			deleteDocument( $dbConnect, getDocumentID( $dbConnect, $id, APPL_CV ) );
		if($delMot)
			deleteDocument( $dbConnect, getDocumentID( $dbConnect, $id, APPL_MOTIV ) );

		if($func != CANCEL_FUNC)
		{
			if($result && !is_object($result))
			{
	
				$fieldName = $applCvInfo['uiFieldName'];
				if( array_key_exists($fieldName, $_FILES) )
				{
					$tmpFile = $_FILES[$fieldName]['tmp_name'];
					if( $tmpFile && is_uploaded_file( $tmpFile ) )
					{
						$result = saveDocument( 
							$dbConnect, $actUser['id'], $id, APPL_CV, 
							$_FILES[$fieldName]['type'], 
							$tmpFile,
							getApplCV($id) 
						);
					}
				}
			}
	
			if($result && !is_object($result))
			{
	
				$fieldName = $applMotInfo['uiFieldName'];
				if( array_key_exists($fieldName, $_FILES) )
				{
					$tmpFile = $_FILES[$fieldName]['tmp_name'];
					if( $tmpFile && is_uploaded_file( $tmpFile ) )
					{
						$result = saveDocument( 
							$dbConnect, $actUser['id'], $id, APPL_MOTIV, 
							$_FILES[$fieldName]['type'], 
							$tmpFile,
							getApplMotivation($id) 
						);
					}
				}
			}
		}
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
			if($func == CANCEL_FUNC)
				$title = "Bewerbung l&ouml;schen";
			else
				$title = "Bewerbung Speichern";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
//	print_r($_FILES);
			include( "includes/components/headerlines.php" );
//	print_r($_POST);

			if( dbOK($result) )
				echo "<p>Daten erfolgreich gespeichert. Viel Gl&uuml;ck</p>";
			else
				include "includes/components/error.php";
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
