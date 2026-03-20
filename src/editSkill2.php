<?php
	require_once( "includes/components/login.php" ); 
	require_once( "includes/tools/tools.php" ); 
	$id = $_POST["id"];
	$parent_id = $_POST["parent_id"];
	$name = urlencode($_POST["name"]);

	if( !is_numeric($id) )		
		$id = 0;
	if( !is_numeric($parent_id) )		
		$parent_id = -1;
		
	if( $parent_id >= 0 )
	{
		$skill = getSkill($dbConnect, $parent_id);
		if( !$skill )
		{
			$parent_id = -1;
		}
	}

	if( !$id )
	{
		$id = getNextID( $dbConnect, "skills", "id" );

		$result = queryDatabase( $dbConnect,
			"insert into skills (" .
				"id, parent_id, user_id, name " .
			")" .
			"values" .
			"(" .
				"$1, $2, $3, $4" .
			")",
			array( 
				$id, $parent_id, $actUser['id'], $name
			)
		);
	}
	else
	{
		$skill = getSkill($dbConnect, $id);
		if( !$skill || (!$actUser['administrator']&&$skill['user_id'] != $actUser['id']) )
		{
			$error = "Nicht erlaubt";
			$result = false;
		}
		if( $parent_id <= 0 )
			$parent_id = $skill['parent_id'];
		if( !isset($error) )
			$result = queryDatabase( $dbConnect, "update skills set name = $1, user_id=$2 where id = $3", array( $name, $actUser['id'], $id ) );
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
			$title = "Bereich/Skill Speichern";
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
		<p><a href='<?php echo($nextURL); ?>'>Weiter</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
