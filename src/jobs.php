<?php
	include_once( "includes/tools/commontools.php" ); 
	include_once( "includes/tools/tools.php" ); 
	startSession();

	$mode = readRequestSetting( "mode", "jobsMode", $_GET, BROWSE_MODE );

	if($mode == BROWSE_MODE)
		$tryLogin = true;
	require_once( "includes/components/login.php" ); 

	$jobTitle = readRequestSetting( "jobTitle", "jobTitle", $_POST, null );
	$jobCompany = readRequestSetting( "jobCompany", "jobCompany", $_POST, null );

	$jobCountry = readRequestSetting( "jobCountry", "jobCountry", $_POST, null );
	$jobRegSym = readRequestSetting( "jobRegSym", "jobRegSym", $_POST, null );

	$page=0;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			if( $mode == REC_APPL_MODE )
				$title = "Empfangene Bewerbungen";
			else if( $mode == SENT_APPL_MODE )
				$title = "Gesendete Bewerbungen";
			else
				$title = "Jobangebote";
				
			$hideCompany = ( $mode == REC_APPL_MODE || $mode == EDIT_MODE );
				
			include_once( "includes/components/defhead.php" );
		?>

		<script language="JavaScript">
			var page = <?php echo $page; ?>;
			var jobTitle = "<?php echo $jobTitle; ?>";
			var jobCompany = "<?php echo $jobCompany; ?>";
			var jobCountry = "<?php echo $jobCountry; ?>";
			var jobRegSym = "<?php echo $jobRegSym; ?>";
			
			function showPage()
			{
				var xmlhttp;    

				if (window.XMLHttpRequest)
				{// code for IE7+, Firefox, Chrome, Opera, Safari
					xmlhttp=new XMLHttpRequest();
				}
				else
				{// code for IE6, IE5
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange=function()
				{
					if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
						document.getElementById("searchResult").innerHTML=xmlhttp.responseText;
					}
				}
				xmlhttp.open("GET","jobs2.php?page="+page+"&jobTitle="+jobTitle+"&jobCompany="+jobCompany+"&jobCountry="+jobCountry+"&jobRegSym="+jobRegSym,true);  

				xmlhttp.send();
			}
			function prevPage()
			{
				if( page > 0 )
				{
					page--;
					showPage();
				}
			}
			function nextPage()
			{
				page++;
				showPage();
			}
		</script>
	</head>
	<body class="jobs">
		<?php include( "includes/components/headerlines.php" ); ?>

		<form name="searchForm" action="jobs.php" method="post">
			<table>
				<tr><td class="fieldLabel">Jobbezeichnung</td><td><input type="text" name="jobTitle" size=32 value="<?php if( isset( $jobTitle ) ) echo $jobTitle; ?>"></td></tr>

				<?php if( !$hideCompany ) { ?>
					<tr><td class="fieldLabel">Firma</td><td><input type="text" name="jobCompany" size=32 value="<?php if( isset( $jobCompany ) ) echo $jobCompany; ?>"></td></tr>
				<?php } ?>

				<?php if($mode == BROWSE_MODE) { ?>
					<tr>
						<td class="fieldLabel">Region</td>
						<td>
							<input type="text" name="jobCountry" size=3 maxLength=3 value="<?php if( isset( $jobCountry ) ) echo $jobCountry; ?>"> - 
							<input type="text" name="jobRegSym"  size=3 maxLength=3 value="<?php if( isset( $jobRegSym ) ) echo $jobRegSym; ?>">
							<i>Verwenden Sie Autokennzeichen z.B. A-L oder D-M</i>
						</td>
					</tr>
				<?php } ?>

				<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
				<tr>
					<td class="fieldLabel"></td>
					<td>
						<input type="submit" value="Suche">
					</td>
				</tr>
			</table>
		</form>
		
		<div id="searchResult">
			<?php include( "jobs2.php" ); ?>
		</div>

		<?php if( $mode == EDIT_MODE ) { ?>
			<p><a href="jobedit.php">&gt;&gt; Neues Jobangebot</a></p>
		<?php } ?>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
			
