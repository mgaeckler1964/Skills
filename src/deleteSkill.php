<?php
	require_once( "includes/components/login.php" ); 
	require_once( "includes/tools/tools.php" ); 
	$id = $_GET["id"];
	$parent_id = -1;

	if( !is_numeric($id) )		
		$id = 0;

	$result = true;
	$skill = getSkill($dbConnect, $id);
	if( !$skill )
	{
		$error = "Nicht gefunden";
		$result = false;
	}
	else if( !$actUser['administrator'] && $skill['user_id'] != $actUser['id'] )
	{
		$error = "Nicht erlaubt";
		$result = false;
	}
	if( $result )
	{
		$reason = canDeleteSkill($dbConnect, $id);
		if( $reason )
		{
			$error = "Dieser Eintrag wird noch verwendet und kann nicht gelöscht werden. " . $reason;
			$result = false;
		}
	}

	if( !isset($error) )
	{
		$parent_id = $skill['parent_id'];
		$result = queryDatabase( $dbConnect, "delete from skills where id = $1", array( $id ) );
	}

	$nextURL = "skills.php?id=".$parent_id;
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
			$title = "Bereich/Skill L&ouml;schen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			if( $result )
				echo "<p>Daten erfolgreich gel&ouml;scht.</p>";
			else
				include "includes/components/error.php";
		?>
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
