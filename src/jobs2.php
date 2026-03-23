<?php
	if( !isset($_SESSION) )
	{
		include_once( "includes/tools/commontools.php" ); 
		startSession();
	}

	if( !headers_sent() )
		header('Content-Type: text/html; charset=ISO-8859-1');

	$tryLogin = true;
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/commontools.php" ); 
	include_once( "includes/tools/tools.php" ); 

	if( !isset( $page ) )
	{
		if( array_key_exists( "page", $_GET ) )
			$page = $_GET["page"];
		if( !isset($page) )
			$page=0;
	}

	if( !isset( $jobTitle ) )
	{
		if( array_key_exists( "jobTitle", $_GET ) )
			$jobTitle = $_GET["jobTitle"];
	}
	if( !isset( $jobCompany ) )
	{
		if( array_key_exists( "jobCompany", $_GET ) )
			$jobCompany = $_GET["jobCompany"];
	}

	if( !isset($mode))
	{
		if( array_key_exists( "jobsMode", $_SESSION ) )
			$mode = $_SESSION['mode'];
		else
			$mode = BROWSE_MODE;
	}
	$hideCompany = ( $mode == REC_APPL_MODE || $mode == EDIT_MODE );
	
	$hitsPerPage = isMobileClient() ? 3 : 20;

	if( isset( $jobTitle ) || isset( $jobCompany ) )
	{
		if( !isset( $jobTitle ) )
			$jobTitle = "";
		if( !isset( $jobCompany ) )
			$jobCompany = "";
			
		if( $mode == EDIT_MODE )
		{
			$queryResult = queryDatabase( 
				$dbConnect,
				"select j.id, c.name as company_name, j.job_title, j.department, j.close_date ".
				"from jobs j ".
				"join company c on j.company_id = c.id " .
				"where upper(j.job_title) like upper($1) ".
				"and j.company_id = $2  ".
				"order by j.job_title, j.id",
				array( urlencode($jobTitle)."%", $actUser['id'] )
			);
		}
		else if( $mode == SENT_APPL_MODE )
		{
			$queryResult = queryDatabase( 
				$dbConnect,
				"select j.id, c.name as company_name, j.job_title, j.department, j.close_date, a.id as appl_id, a.appl_date ".
				"from jobs j ".
				"join company c on j.company_id = c.id " .
				"join application a on a.job_id = j.id " .
				"where upper(j.job_title) like upper($1) ".
				"and a.user_id = $2 ".
				"and upper(c.name) like upper($3) ".
				"order by c.name, j.job_title",
				array( urlencode($jobTitle)."%", $actUser['id'], urlencode($jobCompany)."%" )
			);
		}
		else if( $mode == REC_APPL_MODE )
		{
			$queryResult = queryDatabase( 
				$dbConnect,
				"select j.id, c.name as company_name, j.job_title, j.department, j.close_date, a.id as appl_id, a.appl_date, a.user_id, u.nachname as appl_name ".
				"from jobs j ".
				"join company c on j.company_id = c.id " .
				"join application a on a.job_id = j.id " .
				"join user_tab u on a.user_id = u.id " .
				"where upper(j.job_title) like upper($1) ".
				"and j.company_id = $2  ".
				"order by j.job_title, u.nachname",
				array( urlencode($jobTitle)."%", $actUser['id'] )
			);
		}
		else
		{
			$queryResult = queryDatabase( 
				$dbConnect,
				"select j.id, c.name as company_name, j.job_title, j.department, j.close_date ".
				"from jobs j ".
				"join company c on j.company_id = c.id " .
				"where upper(j.job_title) like upper($1) ".
				"and upper(c.name) like upper($2) ".
				"order by c.name, j.job_title",
				array( urlencode($jobTitle)."%", urlencode($jobCompany)."%" )
			);
		}
		
		if( isset( $queryResult ) && !is_object($queryResult) )
		{
			$i = 0;
			echo "<hr><table>\n";
			echo "<tr><th>Nr.</th>";
			
			if( !$hideCompany )
				echo "<th>Firma</th>";
			
			echo "<th>Abteilung</th><th>Jobezeichnung</th><th>Bewerbungsschlu&szlig;</th>";
			if( $mode == EDIT_MODE )
				echo "<th>Funktion</th>";
			else if( $mode == REC_APPL_MODE )
			{
				echo "<th>Bewerber:in</th>";
				echo "<th>Bewerbungsdatum</th>";
				echo "<th>Score</th>";
			}
			else if( $mode == SENT_APPL_MODE )
			{
				echo "<th>Bewerbungsdatum</th>";
				echo "<th>Score</th>";
			}
			echo "</tr>\n";
			while( $job = fetchJob( $queryResult ) )
			{
				if( $i >= $page*$hitsPerPage && $i<($page+1)*$hitsPerPage )
				{
					echo "<tr class=\"".($i%2?"even":"odd")."\"><td>".($i+1)."</td><td>";
					if( !$hideCompany )
						echo( htmlspecialchars($job['company_name'], ENT_QUOTES, 'ISO-8859-1') . "</td><td>" );
					echo( htmlspecialchars($job['department'], ENT_QUOTES, 'ISO-8859-1') . "</td><td>" );
					echo "<a href='jobedit.php?id={$job['id']}'>". htmlspecialchars($job['job_title'], ENT_QUOTES, 'ISO-8859-1') ."</a>";
					echo "</td><TD>" . formatTimeStamp($job['close_date']) . "</td>";
					
					if( $mode == EDIT_MODE )
						echo "<td><a href='deleteJob.php?id={$job['id']}' onClick='if( confirm( \"Wirklich?\" ) ) return true; else return false;'>Löschen</a></td>";
					else if( $mode == SENT_APPL_MODE )
					{
						echo "<td><a href='apply.php?id={$job['id']}&appl_id={$job['appl_id']}'>" .
								formatTimeStamp($job['appl_date']) .
							"</a></td>" .
							"<td>".
								 calculateScore( $dbConnect, $job['id'], $actUser['id'] ) .
							"</td>";
					}
					else if( $mode == REC_APPL_MODE )
					{
						echo "<td>".
								"<a href='applicant.php?id={$job['user_id']}&appl_id={$job['appl_id']}'>". 
									htmlspecialchars(urldecode($job['appl_name']), ENT_QUOTES, 'ISO-8859-1') .
								"</a> ".
							"</td><td>" .
								formatTimeStamp($job['appl_date']) .
							"</td><td>" ,
								calculateScore( $dbConnect, $job['id'], $job['user_id'] ) .
							"</td>";
					}
					echo "</tr>\n";
				}
				$i++;
			}
			echo "</table>\n";
			echo "<p class='pager'>";
			if( $page )
				echo "<a href='javascript:prevPage();'>&lt;&lt;</a> ";
			echo "Seite " .($page+1). " von " . floor(($i-1)/$hitsPerPage+1) . ". - $i Jobs gefunden. ";
			if( ($page+1) <= floor(($i-1)/$hitsPerPage) )
				echo "<a href='javascript:nextPage();'>&gt;&gt;</a>";
			echo "</p>\n";
		}
	}
?>