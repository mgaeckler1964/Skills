<?php 
	include_once( "includes/tools/tools.php" );
	include_once( "includes/tools/database.php" );

	$id = $_GET['id'];
	$dbConnect = openDatabase();
	$doc = getDocument( $dbConnect, $id );
	if( $doc )
	{
		$fileName = $doc['path'];
		
		header('Content-Type: ' . $doc['mimetype'] );
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize($fileName));
			
		readFile( $fileName );
		exit();
	}
	else
		$error = "Document nicht gefunden";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">
<html>
	<head>
		<?php
			$title = APPLICATION_NAME . " - Fehler";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php 
			include( "includes/components/headerlines.php" ); 
			include( "includes/components/error.php" ); 
			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
