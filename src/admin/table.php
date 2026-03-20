<?php include_once( "includes/components/login.php" ); ?>

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

			echo("<h2>information_schema.tables</h2>");

			createUserTableWarning($dbConnect);

			if( is_file( "templates/index.html" ) )
				include_once( "templates/index.html" );


			$tableOk = hasTable( $dbConnect, "DuMMY_table" );
			echo($tableOk);
			$tableOk = hasTable( $dbConnect, "usEr_Tab" );
			echo($tableOk);
			$queryResult = queryDatabase( 
				$dbConnect,
				"select * ".
				"from information_schema.tables "
//				"where start_time < $1 ".
//				"order by start_time",
//				array( time() )
			);
			if( isset( $queryResult ) && !is_object($queryResult) )
			{
				$i = 0;
				echo "<hr><table>\n";
/*
				echo "<tr><th>Nr.</th><th>Name</th>";
				echo "<th>__XXX__</th>";
				echo "<th>__XXX__</th>";
				echo "<th>__XXX__</th>";
				echo "</tr>\n";
*/
		
				while( $xxx = fetchQueryRow( $queryResult ) )
				{
					echo("<tr><td>");
					print_r($xxx);
					echo("</td></tr>");
				/*
					$code = $xxx['code'];
					if( !$code )
					{
						$xxx_id = $__XXX__['__XXX__'];
						$end_time = $['__XXX__'];

						echo "<tr class=\"".($i%2?"even":"odd")."\"><td>".($i+1)."</td><td>";
		
						echo "<a href='{$nextPage}?xxx_id={$vote_id}'>". htmlspecialchars(urldecode($vote['name']), ENT_QUOTES, 'ISO-8859-1') ."</a>";
						echo "</td>";
						$start = formatTimeStamp($vote['start_time']);
						$end = formatTimeStamp($end_time);
						echo "<td>{$start}</td>";
						echo "<td>{$end}</td>";
			
						echo "</tr>\n";
	
						$i++;
					}
				*/
				}
				echo "</table>\n";
			}

			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
		
