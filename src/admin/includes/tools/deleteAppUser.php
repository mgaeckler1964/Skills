<?php
	require_once( __DIR__ . "/../../../includes/tools/tools.php" );
	
	function deleteAppUser( $dbConnect, $userID )
	{
		deleteDocument( $dbConnect, getDocumentID( $dbConnect, $userID, USER_CV ) );
		deleteDocument( $dbConnect, getDocumentID( $dbConnect, $userID, USER_MOTIV ) );
		deleteDocument( $dbConnect, getDocumentID( $dbConnect, $userID, COMPANY_DESCR ) );

		$queryResult = queryDatabase($dbConnect, 
			"select id from docs ".
			"where user_id = $1 or ".
			"(kind in ($2, $3) and entity_id in (".
				"select id from application where job_id in (".
					"select id from jobs where company_id=$4".
				")".
			"))",
			array($userID, APPL_CV, APPL_MOTIV, $userID)
		);
		if( dbOK($queryResult) )
		{
			while( $queryRecord = fetchQueryRow( $queryResult ) ) 
			{
				deleteDocument( $dbConnect, $queryRecord['id'] );
			}
		}

		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from job_skills where job_id in (select id from jobs where company_id=$1)", array($userID) );

		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from application where job_id in (select id from jobs where company_id=$1) or user_id=$2", array($userID,$userID) );

		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from appl_skills where user_id=$1", array($userID) );
			
		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from jobs where company_id=$1", array($userID) );

		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from applicants where id=$1", array($userID) );

		if( dbOK($queryResult) )
			$queryResult = queryDatabase($dbConnect, "delete from company where id=$1", array($userID) );

	}
?>