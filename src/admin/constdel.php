<?php require_once( "includes/components/login.php" ); ?>
<?php
	$id = $_GET["id"];
	$nextURL = "constant.php";
		
	$error = deleteConstant( $id );
	if( !$error && isset($nextURL) )
		header( "Location: " . $nextURL ); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Konstante L&ouml;schen";
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
		<p><a href="<?php echo $nextURL; ?>">&gt;&gt;&nbsp;Konstante</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
