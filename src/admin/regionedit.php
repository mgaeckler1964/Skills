<?php 
	include_once( "../includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" );

	if( array_key_exists( "id", $_GET ) )
		$id = $_GET["id"];

	if( array_key_exists( "nextURL", $_GET ) )
		$nextURL = urlencode($_GET["nextURL"]);
	else
		$nextURL = "";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<link rel="stylesheet" type="text/css" href="../support/styles.css">
		<?php
			$title = "Bezirk erfassen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body class="center">
		<?php
			include( "includes/components/headerlines.php" );

			if( isset( $id ) )
			{
				$region = getRegion( $dbConnect, $id );
				$country = $region['country'];
				$symbol = $region['symbol'];
				$name = $region['name'];
				$neighbours = getNeighbours($id);
			}
			else
			{
				$id = "";
				$symbol = "";
				$name = "";
				$neighbours = "";
				$country = readRequestSetting( "country", "country", $_GET, "" );
			}
		?>

		<form action="regionedit2.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="id" value="<?php echo $id;?>">
			<input type="hidden" name="nextURL" value="<?php echo $nextURL;?>">

			<table>
				<tr><td class="fieldLabel">Land</td><td><input type="text" required="required" name="country" size=48 value="<?php echo htmlspecialchars($country, ENT_QUOTES, 'ISO-8859-1'); ?>"></td></tr>
				<tr><td class="fieldLabel">Kennzeichen</td><td><input type="text" required="required" name="symbol" size=48 value="<?php echo htmlspecialchars($symbol, ENT_QUOTES, 'ISO-8859-1'); ?>" autofocus></td></tr>
				<tr><td class="fieldLabel">Hauptort</td><td><input type="text" name="name" size=48 value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'ISO-8859-1'); ?>"></td></tr>
				<tr><td class="fieldLabel">Nachbarn</td><td><input type="text" name="neighbours" size=48 value="<?php echo htmlspecialchars($neighbours, ENT_QUOTES, 'ISO-8859-1'); ?>"></td></tr>

				<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
					<td class="fieldLabel">&nbsp;</td>
					<td>
						<input type="submit" value="Speichern">
						<?php
							echo "<input type='button' onClick='window.history.back();' value='Abbruch'>";
						?>
					</td>
				</tr>
			</table>
		</form>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
