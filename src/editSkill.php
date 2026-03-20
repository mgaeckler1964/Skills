<?php 
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/tools.php" );

	if( array_key_exists( "id", $_GET ) )
		$id = $_GET["id"];
	else if( array_key_exists( "parent_id", $_GET ) )
		$parent_id = $_GET["parent_id"];
	else
		$parent_id = -1;

	if( isset($id) )
	{
		$skill = getSkill($dbConnect, $id);
		$id = $skill['id'];
		$name = $skill['name'];
		$parent_id = $skill['parent_id'];
	}
	else
	{
		$id = "";
		$name="";
	}
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
		?>
		<h2>Bereich/Skill erstellen/bearbeiten</h2>
		<hr><form action="editSkill2.php" method="POST">
			<input type='hidden' name='id' value='<?php echo $id; ?>'>
			<input type='hidden' name='parent_id' value='<?php echo htmlspecialchars($parent_id, ENT_QUOTES, 'ISO-8859-1'); ?>'>
			Name:&nbsp;<input type='text' name='name' required value='<?php echo htmlspecialchars($name, ENT_QUOTES, 'ISO-8859-1'); ?>' autofocus>
			<input type="submit">
		</form>
		<?php
			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
		
