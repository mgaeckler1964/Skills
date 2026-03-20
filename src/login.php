<?php 
	if( !array_key_exists("fromLogin", $_POST) )
		$ignoreCurrent=true;
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

			echo("<h2>".$title."</h2>");

			if( is_file( "templates/welcome.html" ) )
				include_once( "templates/welcome.html" );
			else
				echo("<p>Willkommen</p>");

			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
		
