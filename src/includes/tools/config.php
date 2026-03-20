<?php
	/*
		select the database type:
		MYSQL:	Connect to MySQL
		MYSQLi:	Connect to MySQL using MySQLi interface (PHP 7 or higher)
		PG:		Connect to PostgresSQL
		ORA:	Connect to Oracle
	*/
	$database = "ORA";
	$database = "MYSQL";
	$database = "MYSQLi";
	$database = "PG";
	
	
	/*
		connect strings for the databases:
	*/

	$postgresDB		= "dbname=xxxxx user=xxxxx password=xxxxx";
	$mysqlHost		= "xxxxx";
	$mysqlUser		= "xxxxx";
	$mysqlDB		= "xxxxx";
	$mysqlPassword	= "xxxxx";
	$oraUser		= "xxxxx";
	$oraPassword	= "xxxxx";
	$oraConnection	= "xxxxx";

	/*
		Mailer config
	*/
	$useNewMailer=false;
//	define( "MAILER_PATH", "xxx/" );
//	define( "MAILER_URL", "http://xxx/?file=yyy&nextUrl=zzz" );
//	define( "MAILER_FROM", "xxx@xxx.xx" );
//	define( "MAILER_NEXTURL", "https://xxx.xx/xxx" );

	$serverName = $_SERVER["SERVER_NAME"];

	$config = "includes/tools/config." . $serverName . ".php";
	if( is_file( $config ) )
		include_once( $config );

	$config = "../includes/tools/config." . $serverName . ".php";
	if( is_file( $config ) )
		include_once( $config );
	/*
		application specific constants
	*/
	define( "APPLICATION_NAME", "Skills" );
	define( "APPLICATION_COPYRIGHT", "&copy; 2026 by <a href='https://www.gaeckler.at/' target='_blank'>Martin G&auml;ckler</a>" );
	define( "NO_PERM", "Keine Berechtigung" );

	define( "SELF_REGISTER", 1 );

	$backupTables = array(
		"user_tab", "group_member", "user_login_prot",
		"skills", "regions", "neighbours", "const_values", "applicants", "appl_skills", "company", "jobs", "job_skills", "application"
	);

	date_default_timezone_set('Europe/Vienna');
?>
