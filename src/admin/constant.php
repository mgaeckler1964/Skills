<?php 
	require_once( "includes/components/login.php" ); 
	if( array_key_exists( "ctype", $_GET ) )
		$ctype = $_GET["ctype"];
	else
		$ctype = 0;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Konstante Zeichenketten";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php 
			include( "includes/components/headerlines.php" );

			$queryResult = queryDatabase( 
				$dbConnect,
				"select id, ctype, corder, cvalue ".
				"from const_values ".
				"order by ctype, corder"
			);
			if( isset( $queryResult ) && !is_object($queryResult) )
			{
				echo("<table>");
				while( $constant = fetchQueryRow( $queryResult ) )
				{
					$constant['cvalue'] = urldecode($constant['cvalue']);
					echo("<tr>");
						echo("<td>{$constant['id']}</td>");
						echo("<td>");
							writeConstantTypeCombo($constant['ctype']);
						echo("</td>");
						echo("<td>{$constant['corder']}</td>");
						echo("<td>{$constant['cvalue']}</td>");
						echo("<td><a href='constdel.php?id={$constant['id']}'>L&ouml;schen</a></td>");
					echo("</tr>");
				}
				echo("</table>");
			}
		?>
		<form action="constant2.php">
			<?php writeConstantTypeCombo($ctype, "ctype", "ctype");  ?>
			<input type="number" name="corder" required autofocus>
			<input type="text" name="cvalue" required>
			<input type="submit">
		</form>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
			
