<?php
	if( !headers_sent() )
		header('Content-Type: text/html; charset=ISO-8859-1');

	include_once( "includes/components/login.php" ); 
	include_once( "../includes/tools/commontools.php" ); 

	if( !isset( $page ) )
	{
		if( array_key_exists( "page", $_GET ) )
			$page = $_GET["page"];
		if( !isset($page) )
			$page=0;
	}

	if( !isset( $country ) )
	{
		if( array_key_exists( "country", $_GET ) )
			$country = $_GET["country"];
	}
	if( !isset( $symbol ) )
	{
		if( array_key_exists( "symbol", $_GET ) )
			$symbol = $_GET["symbol"];
	}
	if( !isset( $regname ) )
	{
		if( array_key_exists( "regname", $_GET ) )
			$regname = $_GET["regname"];
	}


	if( !isset($nextURL) )
		$nextURL=urlencode("region.php?country=" . $country . "&symbol=" . $symbol . "&regname=" . $regname . "&page=" . $page);

	$hitsPerPage = isMobileClient() ? 3 : 20;

	if( isset($country) && isset($symbol) && isset($regname))
	{
		$queryResult = queryDatabase( 
			$dbConnect,
			"select id, country, symbol, name ".
			"from regions ".
			"where upper(country) like upper($1) ".
			"and upper(symbol) like upper($2) ".
			"and upper(name) like upper($3) ".
			"order by name, symbol, country",
			array( urlencode($country)."%", urlencode($symbol)."%", urlencode($regname)."%")
		);
		if( isset( $queryResult ) && !is_object($queryResult) )
		{
			$i = 0;
			echo "<hr><table>\n";
			echo "<tr><th>Nr.</th><th>Land</th>";
			echo "<th>Kennzeichen</th>";
			echo "<th>Hauptort</th>";
			echo "</tr>\n";
			while( $region = fetchQueryRow( $queryResult ) )
			{
				if( $i >= $page*$hitsPerPage && $i<($page+1)*$hitsPerPage )
				{
					echo "<tr class=\"".($i%2?"even":"odd")."\"><td>".($i+1)."</td>";
					echo "<td>".htmlspecialchars(urldecode($region['country']), ENT_QUOTES, 'ISO-8859-1')."</td>";
					echo "<td>".htmlspecialchars(urldecode($region['symbol']), ENT_QUOTES, 'ISO-8859-1')."</td>";
				
					echo "<td nowrap><a href='regionedit.php?id={$region['id']}&nextURL={$nextURL}'>". htmlspecialchars(urldecode($region['name']), ENT_QUOTES, 'ISO-8859-1') ."</a></td>\n";
						
					echo "<td><a href='regiondelete.php?id={$region['id']}&nextURL={$nextURL}' onClick='if( confirm( \"Wirklich?\" ) ) return true; else return false;'>L&ouml;schen</a></td>";

					echo "</tr>\n";
				}
				$i++;
			}
			echo "</table>\n";
			echo "<p class='pager'>";
			if( $page )
				echo "<a href='javascript:prevPage();'>&lt;&lt;</a> ";
			echo "Seite " .($page+1). " von " . floor(($i-1)/$hitsPerPage+1) . ". - $i Bezirke gefunden. ";
			if( ($page+1) <= floor(($i-1)/$hitsPerPage) )
				echo "<a href='javascript:nextPage();'>&gt;&gt;</a>";
			echo "</p>\n";
		}
	}
?>