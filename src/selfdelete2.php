<?php
	require_once( "includes/components/login.php" );

	$answer = checkField( $_POST, "answer", "Nein" );

	if( $answer != "Ja" )
		$error = "Ihre Antwort lautet " . $answer;
	else if($actUser['administrator'])
		$error = "Admins dürfen sich nicht aus dem Staub machen";
	else if($actUser['guest'])
		$error = "Permission denied";
	else
	{
		$error = deleteSelf();

		if( !$error )
		{
			$id = $actUser["id"];
		
			if( is_file("admin/includes/tools/deleteAppUser.php") )
			{
				include_once( "admin/includes/tools/deleteAppUser.php" );
				deleteAppUser($dbConnect, $id);
			}
			setcookie( "userID", "", 0, "/" );
			setcookie( "password", "", 0, "/" );
		}
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Profil Löschen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( !$error )
				echo "<p>Daten erfolgreich gel&ouml;scht.</p>";
			else 
				include "includes/components/error.php";
		?>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
