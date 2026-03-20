<?php 
	require_once( "includes/components/login.php" ); 

	$ctype = $_GET["ctype"];
	$corder = $_GET["corder"];
	$cvalue = urlencode($_GET["cvalue"]);
	$nextURL = "constant.php?ctype=".$ctype;
	
	$id = getNextID( $dbConnect, "const_values", "id" );

	$result = queryDatabase( $dbConnect,
		"insert into const_values (" .
			"id, ctype, corder, cvalue " .
		")" .
		"values" .
		"(" .
			"$1, $2, $3, $4" .
		")",
		array($id, $ctype, $corder, $cvalue) 
	);

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
			$title = "Konstante speichern";
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
		<p><a href="<?php echo $nextURL?>">Konstante</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
