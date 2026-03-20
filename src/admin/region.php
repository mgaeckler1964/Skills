<?php 
	include_once( "../includes/tools/commontools.php" ); 
	startSession();
	require_once( "includes/components/login.php" );

	$country = readRequestSetting( "country", "country", $_GET, "" );
	$symbol = readRequestSetting( "symbol", "symbol", $_GET, "" );
	$regname = readRequestSetting( "regname", "regname", $_GET, "" );

	if( array_key_exists( "page", $_GET ) )
		$page = $_GET["page"];
	else
		$page = 0;

	$nextURL=urlencode("region.php?page=" . $page);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Bezirke/Landkreise";
			include_once( "includes/components/defhead.php" );
		?>

		<script language="JavaScript">
			var page = <?php echo $page; ?>;
			var country = "<?php echo $country; ?>";
			var symbol = "<?php echo $symbol; ?>";
			var regname = "<?php echo $regname; ?>";
			

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
				xmlhttp.open("GET","region2.php?page="+page+"&country="+country+"&symbol="+symbol+"&regname="+regname,true);
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
	<body class="personen">
		<?php include( "includes/components/headerlines.php" ); ?>

		<form name="searchForm" action="region.php" method="get">
			<table>
				<tr><td class="fieldLabel">Land</td><td><input type="text" name="country" value="<?php if( isset( $country ) ) echo $country; ?>" autofocus></td></tr>
				<tr><td class="fieldLabel">Kennzeichen</td><td><input type="text" name="symbol" value="<?php if( isset( $symbol ) ) echo $symbol; ?>"></td></tr>
				<tr><td class="fieldLabel">Hauptort</td><td><input type="text" name="regname" value="<?php if( isset( $regname ) ) echo $regname; ?>"></td></tr>
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
			<?php include( "region2.php" );  ?>
		</div>

		<p><a href="regionedit.php?country=<?php echo $country;?>&nextURL=<?php echo $nextURL;?>">&gt;&gt; Neuer Bezirk/Landkreis</a></p>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
			
