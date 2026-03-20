<?php 
	include_once( "../includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" ); 

	$id = $_POST["id"];
	$country = strtoupper($_POST["country"]);
	$_SESSION["country"] = $country;
	$country = urlencode($country);
	$symbol = urlencode(strtoupper($_POST["symbol"]));
	$neighbours = strtoupper($_POST["neighbours"]);
	$name = urlencode($_POST["name"]);
	$nextURL = urldecode($_POST["nextURL"]);
	
	if( !$id )
	{
		$id = createRegion( $dbConnect, $country, $symbol, $name );
		if( !is_numeric($id) )
			$result = $id;
	}
	else
	{
		$result = queryDatabase( $dbConnect,
			"update regions " .
			"set country = $1," .
				"symbol = $2," .
				"name = $3 " .
			"where id = $4",
			array($country, $symbol, $name, $id) 
		);
	}
	if( is_numeric( $id ) )
	{
		storeNeighbours( $id, $country, $neighbours );
		// currently we're ignoring error
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
			$title = "Bezirk speichern";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( $result )
				echo "<p>Daten erfolgreich gespeichert.</p>";
			else
				include "../includes/components/error.php";
		?>
		<p><a href="<?php echo $nextURL?>">Bezirke/Landkreise</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
