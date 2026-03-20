<?php 
	$tryLogin = true;
	include_once( "includes/components/login.php" ); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = APPLICATION_NAME;
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );


			if( is_file( "templates/index.html" ) )
				include_once( "templates/index.html" );
			else
				echo("<h2>".$title."</h2>");


			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
		
