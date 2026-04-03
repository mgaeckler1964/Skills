<?php 
	include_once(__DIR__ . "/../tools/tools.php"); 

	include("commonheaderlines.php"); 

	$menuLeft = array (
		array( "href" => "index.php", "label" => "Start" ),
		array( "href" => "skills.php", "label" => "Skills" ),
		array( "label" => "Profile", "submenu" => array(
			array( "href" => "applicant.php", "label" => "Bewerber" ),
			array( "href" => "jobs.php?mode=".SENT_APPL_MODE, "label" => "Meine Bewerbungen" ),
			array( "label" => "-" ),
			array( "href" => "selfdelete.html", "label" => "Löschen" ),
			array( "label" => "-" ),
			array( "href" => "company.php", "label" => "Firma" ),
			array( "href" => "jobs.php?mode=".EDIT_MODE, "label" => "Jobs" ),
			array( "href" => "jobs.php?mode=".REC_APPL_MODE, "label" => "Meine Bewerber:innen" )
		) ),
		array( "href" => "jobs.php?mode=".BROWSE_MODE, "label" => "Stellenangebote" ),
		array( "href" => "impressum.php", "label" => "Impressum" )
	);

	$menu = array( "left" => $menuLeft );
	include("commonmenu.php" );
?>
