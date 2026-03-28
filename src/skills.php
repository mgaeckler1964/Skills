<?php 
	include_once( "includes/tools/commontools.php" );
	startSession();
	$tryLogin = true;
	include_once( "includes/components/login.php" ); 

	include_once( "includes/tools/database.php" );
	include_once( "includes/tools/tools.php" );

	$id = readRequestSetting( "id", "lastSkillId", $_GET, -1 );

	if( array_key_exists( "func", $_GET ) )
		$func = $_GET["func"];

	$parent_id = false;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = APPLICATION_NAME;
			include_once( "includes/components/defhead.php" );
		?>
		<script>
			function selectSkill(id, name)
			{
				if( window.opener )
				{
					window.opener.selectSkill( id, name );
				}
				close();
			}
		</script>
	</head>
	<body>
		<?php
			if( !isset($func) )
				include( "includes/components/headerlines.php" );

			$dbConnect = openDatabase();
			if( $id > 0 )
			{
				$skill = getSkill($dbConnect, $id);
				$skillPath = getSkillPath($dbConnect, $id);
			}
			else
			{
				$skill = array();
				$skillPath = array();
			}
			if( $skill && array_key_exists( "parent_id", $skill ) )
			{
				$name = $skill["name"];
				$parent_id = $skill["parent_id"];
			}
			else
				$name = "";
			
			echo("<h2>Skills {$name}</h2>");
			
			foreach( array_reverse($skillPath) as $parent )
			{
				$url = 'skills.php?id=' . $parent['id'];
				if( isset( $func ) )
					$url = $url . "&func=" . $func;
				echo "<a href='{$url}'>&gt;&gt; ". htmlspecialchars($parent['name'], ENT_QUOTES, 'ISO-8859-1') ."</a>&nbsp;&nbsp;&nbsp;";
			}

			$queryResult = queryDatabase( 
				$dbConnect,
				"select id, name, user_id ".
				"from skills ".
				"where parent_id = $1 ".
				"order by name",
				array( $id )
			);
			if( isset( $queryResult ) && !is_object($queryResult) )
			{
				$i = 0;
				echo "<hr><table>\n";
				echo "<tr><th>Nr</th><th>Name</th>";
				echo "</tr>\n";
		
				while( $skill = fetchQueryRow( $queryResult ) )
				{
					$subId = $skill['id'];
					$name = $skill['name'];

					echo "<tr class=\"".($i%2?"even":"odd")."\"><td>".($i+1)."</td><td>";
		
					$url = 'skills.php?id=' . $subId;
					if( isset( $func ) )
						$url = $url . "&func=" . $func;

					echo "<a href='{$url}'>". htmlspecialchars(urldecode($name), ENT_QUOTES, 'ISO-8859-1') ."</a>";
					echo "</td>";
					if( isset($actUser) && $actUser['administrator'] )
						echo "<td>{$subId}</td>";
		
					if( !isset($func) )
					{
						if( isset($actUser) && ($actUser['administrator'] || $skill['user_id']==$actUser['id']) )
						{
							echo("<td><a href='editSkill.php?id={$subId}'>Bearbeiten</a></td>");
							$reason = canDeleteSkill($dbConnect, $subId);
							if( $reason )
								echo("<td>{$reason}</td>");
							else
								echo("<td><a href='deleteSkill.php?id={$subId}'>L&ouml;schen</a></td>");
						}
					}
					else
					{
						$onClick = "";
						$url = "selectSkill.php?id=".$subId."&func=".$func;
						if( $func == SEARCH_JOB_SKILL_FUNC )
						{
							$jsName = htmlspecialchars( urldecode($name), ENT_QUOTES, 'ISO-8859-1');
							$url = '#';
							$onClick=" onClick=\"selectSkill( {$subId}, '{$jsName}' );\" ";
						}

						echo "<td><a href='{$url}' {$onClick}>Ausw&auml;hlen</a></td>";
					}
	
					echo "</tr>\n";
					$i++;
				}
				echo "</table>\n";
				echo "<hr><p>";
				if( !isset($func) )
				{
					if( isset($actUser) )
						echo "<a href='editSkill.php?&parent_id={$id}'>&gt;&gt; Skill/Berreich erstellen</a>&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				else if( isset($_SESSION) && isset($_SESSION['backURL']) )
				{
					$url = $_SESSION['backURL'];
					echo "<a href='{$url}'>&gt;&gt; Zur&uuml;ck</a>&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				if( $parent_id )
				{
					$url = 'skills.php?id=' . $parent_id;
					if( isset( $func ) )
						$url = $url . "&func=" . $func;
					echo "<a href='{$url}'>&gt;&gt; Eine Stufe h&ouml;her</a>";
				}
				echo "</p>";
			}

			include( "includes/components/footerlines.php" );
		?>
	</body>
</html>
		
