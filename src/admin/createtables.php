<?php require_once( "includes/components/login.php" ); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Tabellen Erstellen";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php include( "includes/components/headerlines.php" ); ?>
		
		<?php
			require_once("includes/tools/createUserTables.php" );

			$dbConnect = openDatabase();
			if( !$dbConnect )
				echo "<p>Kann keine Verbindung zur Datenbank herstellen.</p>";
			else
			{
				echo "<p>Verbindung zur Datenbank OK.</p>";
				$error = createUserTables( $dbConnect, $database );
				if( $error > "" )
					echo( $error );

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table skills";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>skills konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table skills (".
					"id				int				not null 	primary key,".
					"parent_id		int				not null,".
					"user_id		int				not null,".
					"name			varchar(255)	not null".
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>skills konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table regions";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>regions konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table regions (".
					"id			int				not null 	primary key,".
					"country	varchar(8)		not null,".
					"symbol		varchar(8)		not null,".
					"name		varchar(255)	not null".
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>regions konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table neighbours";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>neighbours konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table neighbours ( ".
						"id1	int		not null,".
						"id2	int		not null".
					")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>neighbours konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table const_values";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>const_values konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table const_values (".
					"id		int				not null 	primary key,".
					"ctype	int				not null,".
					"corder	int				not null,".
					"cvalue	varchar(255)	not null".
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>const_values konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table applicants";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>applicants konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table applicants (".
					"id				int		not null 	primary key,".
					"education		int," .
					"title			int," .
					"position		int," .
					"description	varchar(1024)," .
					"birthday		varchar(16)," .
					"planed_time	int," .
					"region			int," .
					"mobility		int," .
					"open			int" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>applicants konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table appl_skills";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>appl_skills konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table appl_skills (".
					"id				int		not null 	primary key,".
					"user_id		int		not null," .
					"skill_id		int		not null," .
					"start_y		int," .
					"end_y			int," .
					"part			int" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>appl_skills konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table company";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>company konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table company (".
					"id				int				not null 	primary key,".
					"name			varchar(128)	not null," .
					"branch			int				not null," .
					"foundation		int				not null," .
					"employees		int				not null," .
					"region			int				not null," .
					"address		varchar(256)	not null," .
					"description	varchar(1024)" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>company konnte nicht erstellt werden.</p>\n";
				}
// -------------------------------------------------------------------------------------------
/*
				$query = "drop table jobs";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>jobs konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table jobs (".
					"id				int				not null 	primary key," .
					"company_id		int				not null," .
					"job_title		varchar(128)	not null," .
					"department		varchar(128)	not null," .
					"position		int				not null," .
					"visible		int				not null," .
					"status			int				not null," .
					"max_applicants	int				not null," .
					"open_date		int				not null," .
					"close_date		int				not null," .
					"description	varchar(1024)" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>jobs konnte nicht erstellt werden.</p>\n";
				}
// -------------------------------------------------------------------------------------------
/*
				$query = "drop table job_skills";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>job_skills konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table job_skills (".
					"id				int		not null 	primary key,".
					"job_id			int		not null," .
					"skill_id		int		not null," .
					"part			int" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>job_skills konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table application";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>application konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table application (".
					"id				int		not null 	primary key,".
					"user_id		int		not null,".
					"job_id			int		not null,".
					"appl_date		int		not null,".
					"score			real	not null,".
					"status			int		not null" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>application konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
/*
				$query = "drop table docs";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>docs konnte nicht gel&ouml;scht werden.</p>\n";
				}
*/
				$query = "create table docs (".
					"id				int				not null 	primary key,".
					"user_id		int				not null," .
					"entity_id		int				not null," .
					"kind			int				not null," .
					"mimetype		varchar(64)		not null" .
				")";
				
				$result = queryDatabase( $dbConnect, $query );
				if( !$result )
				{
					echo "<p>docs konnte nicht erstellt werden.</p>\n";
				}

				$query = "create unique index docKindIdx on docs (entity_id,kind)";
				$result = queryDatabase( $dbConnect, $query );
				if( !$result || is_object( $result ) )
				{
					$error .= "<p>docKindIdx konnte nicht erstellt werden.</p>\n";
				}

// -------------------------------------------------------------------------------------------
			}

		?>
		<p>Tabellenerstellung fertig.</p>

		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>