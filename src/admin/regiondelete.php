<?php
	require_once( "includes/components/login.php" );
	include_once( "../includes/tools/tools.php" ); 

	$id = $_GET["id"];
	if( array_key_exists( "nextURL", $_GET ) )
		$nextURL = $_GET["nextURL"];
		
	$error = deleteRegion( $id );
	if( !$error && isset($nextURL) )
		header( "Location: " . $nextURL ); 

	if( !isset($nextURL) )
		$nextURL = "region.php";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Bezirk/Landkreis L&ouml;schen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( !$error )
				echo "<p>Daten erfolgreich gel&ouml;scht.</p>";
			else 
				include "../includes/components/error.php";
		?>
		<p><a href="<?php echo $nextURL; ?>">&gt;&gt;&nbsp;Bezirke</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
