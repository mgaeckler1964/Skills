<?php
	require_once( "includes/components/login.php" ); 
	$userID = $actUser['id'];
	$id = $_POST['id'];

	if($id=="" || $id == $userID )
	{
		require_once( "includes/tools/tools.php" ); 

		$name = urlencode($_POST['name']);
		$branch = $_POST['branch'];
		$foundation = $_POST['foundation'];
		$employees = $_POST['employees'];
		$address = urlencode($_POST['address']);
		$description = urlencode($_POST['description']);
		$country = urlencode(strtoupper($_POST['country']));
		$symbol = urlencode(strtoupper($_POST['symbol']));
		$regionID = getRegionID($dbConnect, $country, $symbol );
	
		if( !$regionID )
		{
			$result = false;
			$error = "Region nicht gefunden.";
		}
		else
		{
			if( $id=="" )
			{
				// create the record 
				$result = queryDatabase( $dbConnect,
					"insert into company (id, name, branch, foundation, employees, address, description, region) ".
					"values" .
					"($1, $2, $3, $4, $5, $6, $7, $8)",
					array( $userID, $name, $branch, $foundation, $employees, $address, $description, $regionID )
				);
				$id = $userID;
			}
			else
			{
				// update the record 
				$result = queryDatabase( $dbConnect, 
					"update company set name = $2, branch = $3, foundation = $4, employees = $5, address = $6, ".
						"description=$7, region=$8 ".
					"where id = $1", 
					array( $id, $name, $branch, $foundation, $employees, $address, $description, $regionID )
				);
			}
		}
	}
	else
	{
		$result = false;
		$error = "Nicht erlaubt";
	}

	$nextURL = "index.php";

	if( is_object( $result ) )
	{
		$error = $result;
		$result = false;
	}
	
	if( $result )
	{
		header( "Location: " . $nextURL );
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Firmenprofil Speichern";
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
		?>
		<p><a href='<?php echo($nextURL); ?>' onClick = "window.history.back();">Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
