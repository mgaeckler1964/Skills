<?php

$jobFileInfo = array(
	"uiFieldName" => "jobDoc",
	"sessionRemoteName" => "remoteDoc",
	"uiDeleteName" => "delDoc",
	"idFieldName" => "docID",
	"typeFieldName" => "docType"
);

$companyFileInfo = array(
	"uiFieldName" => "companyDoc",
	"sessionRemoteName" => "remoteDoc",
	"uiDeleteName" => "delDoc",
	"idFieldName" => "docID",
	"typeFieldName" => "docType"
);

$applCvInfo = array(
	"uiFieldName" => "cvFile",
	"sessionRemoteName" => "remCVfile",
	"uiDeleteName" => "delCV",
	"idFieldName" => "cvID",
	"typeFieldName" => "cvType"
);

$applMotInfo = array(
	"uiFieldName" => "motFile",
	"sessionRemoteName" => "remMotFile",
	"uiDeleteName" => "delMot",
	"idFieldName" => "motID",
	"typeFieldName" => "motType"
);

// ==========================================================================================================================
// regions
// ==========================================================================================================================
	function fetchRegion( $queryResult )
	{
		$region = fetchQueryRow( $queryResult );
		if( $region )
		{
			$region['country'] = urldecode($region['country']);
			$region['symbol'] = urldecode($region['symbol']);
			$region['name'] = urldecode($region['name']);
		}
		
		return $region;
	}
	function getRegion( $dbConnect, $id )
	{
		$region = array();
		$queryResult = queryDatabase(
			$dbConnect,
			"select id, country, symbol, name ".
			"from regions ".
			"where id=$1",
			array( $id )
		);
		if( $queryResult && !is_object( $queryResult ) )
			$region = fetchRegion( $queryResult );

		return $region;
	}
	function getRegionID( $dbConnect, $country, $symbol )
	{
		$regionID = -1;
		$queryResult = queryDatabase(
			$dbConnect,
			"select id ".
			"from regions ".
			"where country=$1 and symbol=$2",
			array( $country, $symbol )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$region = fetchQueryRow( $queryResult );
			$regionID = $region['id'];
		}

		return $regionID;
	}
	function createRegion( $dbConnect, $country, $symbol, $name )
	{
		$id = getNextID( $dbConnect, "regions", "id" );
		if( !is_numeric($id) )
			return $id;

		$queryResult = queryDatabase( $dbConnect,
			"insert into regions (" .
				"id, country, symbol, name " .
			")" .
			"values" .
			"(" .
				"$1, $2, $3, $4" .
			")",
			array($id, $country, $symbol, $name) 
		);
		if( $queryResult && !is_object( $queryResult ) )
			return $id;
		return $queryResult;
	}

	function deleteRegion( $regionID )
	{
		global $dbConnect;

		$error = false;

		$queryResult = queryDatabase( $dbConnect, "delete from neighbours where id1 = $1 or id2 = $2", array( $regionID, $regionID ) );
		if( !is_object($queryResult)  )
			$queryResult = queryDatabase( $dbConnect, "delete from regions where Id = $1", array( $regionID ) );
		
		if( is_object($queryResult)  )
			$error = $queryResult;

		return $error;
	}
	
	function storeNeighbours( $regionID, $defCountry, $neighbours )
	{
		global $dbConnect;
		queryDatabase( $dbConnect, "delete from neighbours where id1 = $1 or id2 = $2", array( $regionID, $regionID ) );
		
		$neighbourList = explode( ",", trim($neighbours) );	
		foreach($neighbourList as $neigbour)
		{
			$elements = explode( "-", trim($neigbour) );
			if( count($elements) >= 2 )
			{
				$country = urlencode($elements[0]);
				$symbol = urlencode($elements[1]);
			}
			else
			{
				$country = $defCountry;
				$symbol = urlencode($elements[0]);
			}
			if( $country > "" & $symbol > "" )
			{
				$queryResult = queryDatabase( $dbConnect, "select id from regions where country = $1 and symbol = $2", array( $country, $symbol ) );
				if( $queryResult && !is_object( $queryResult ) )
				{
					$region = fetchQueryRow( $queryResult );
					if( $region && array_key_exists("id", $region) && $region['id'] > 0 )
						$id2 = $region['id'];
				}
				
				if( !isset($id2) )
					$id2 = createRegion($dbConnect, $country, $symbol, "-" );
					
				if( isset($id2) && is_numeric($id2) )
				{
					queryDatabase( $dbConnect, "insert into neighbours (id1, id2) values ($1, $2)", array( $regionID, $id2 ) );
				}
				unset($id2);
			}
		}
	}
	function getNeighbours( $regionID )
	{
		global $dbConnect;
		$queryResult = queryDatabase( $dbConnect, 
			"select country, symbol from regions where id in (" .
				"select id2 from neighbours where id1=$1" .
				" union ".
				"select id1 from neighbours where id2=$2" .
			") order by country, symbol",
			array( $regionID, $regionID )
		);
		while( $queryRecord = fetchQueryRow( $queryResult ) ) {
			if( isset( $neighbours ) )
				$neighbours = $neighbours . ", ";
			else
				$neighbours = "";
				
			$neighbours = $neighbours . urldecode($queryRecord['country']) . "-" . urldecode($queryRecord['symbol']);
		}
		if( !isset( $neighbours ) )
			$neighbours = "";
		return $neighbours;
	}

// ==========================================================================================================================
// constant values
// ==========================================================================================================================
 	
	define( "EDUCATION", 0 );
	define( "TITLE", 1 );
	define( "BRANCH", 2 );
	define( "POSITION", 3 );

	function getConstantTypes()
	{
		return array(
			"Schul-/Berufsabschluﬂ",
			"Titel",
			"Branche",
			"Position"
		);
	}
	function writeConstantTypeCombo($value, $name="", $id="")
	{
		$types = getConstantTypes();
		if( $name && $id )
		{
			echo( "<select name='{$name}' id='{$id}'>" );
			$i = 0;
			foreach( $types as $const )
			{
				if( $i == $value )
					$selected ="selected";
				else
					$selected ="";
				
				echo( "<option value='{$i}' {$selected}>{$types[$i]}</option>" );
				$i++;
			}
			echo( "</select>" );
		}
		else
		{
			echo( $types[$value] );
		}
	}

	function findConstantValue( $constantValues, $valueID )
	{
		foreach( $constantValues as $constVal )
		{
			if( $constVal["id"] == $valueID )
				return $constVal["cvalue"];
		}
		return "";
	}
	function getConstantValues( $dbConnect, $type )
	{
		$constantValues = array();

		$queryResult = queryDatabase( 
			$dbConnect,
			"select id, cvalue ".
			"from const_values ".
			"where ctype = $1 ".
			"order by corder, cvalue",
			array( $type )
		);
		if( isset( $queryResult ) && !is_object($queryResult) )
		{
			while( $constant = fetchQueryRow( $queryResult ) )
			{
				$constant["cvalue"] = urldecode($constant["cvalue"]);
				$constantValues[] = $constant;
			}
		}
		return $constantValues;
	}
	function writeConstantCombo($dbConnect, $type, $valueID, $name="", $id="", $required=true)
	{
		$values = getConstantValues($dbConnect, $type);
		if( $name && $id )
		{
			echo( "<select name='{$name}' id='{$id}'>" );
			if( !$required )
				echo "<option value=''>-</option>";
			foreach( $values as $const )
			{
				if( $valueID == $const['id'] )
					$selected ="selected";
				else
					$selected ="";
				
				echo(
					"<option value='{$const['id']}' {$selected}>".
					htmlspecialchars($const['cvalue'], ENT_QUOTES, 'ISO-8859-1')
					."</option>" 
				);
			}
			echo( "</select>" );
		}
		else
		{
			echo( findConstantValue($values, $valueID) );
		}
	}

	function deleteConstant( $constID )
	{
		global $dbConnect;

		$error = false;

		$queryResult = queryDatabase( $dbConnect, "delete from const_values where id = $1", array( $constID ) );
		
		if( is_object($queryResult)  )
			$error = $queryResult;

		return $error;
	}


// ==========================================================================================================================
// skills
// ==========================================================================================================================

	function cmp($a, $b)
	{
    	return strcmp($a["path"], $b["path"]);
	}

	function sortSkills( $skills )
	{
		usort($skills, "cmp");
		
		return $skills;
	}

	function getSkill( $dbConnect, $id )
	{
		global $skillCache;
		if( !isset($skillCache) ) $skillCache = array();
		
		
		if( !$id )
			$id=0;
			
		if( array_key_exists( $id, $skillCache ) )
			$skill = $skillCache[$id];
		else
		{
			$skill = array();
			$queryResult = queryDatabase(
				$dbConnect,
				"select id, parent_id, user_id, name ".
				"from skills ".
				"where id=$1 ".
				"order by name ",
				array( $id )
			);
			if( $queryResult && !is_object( $queryResult ) )
			{
				$skill = fetchQueryRow( $queryResult );
				$skill['name'] = urldecode($skill['name']);
				
				$skillCache[$id] = $skill;
			}
		}
		return $skill;
	}

	function canDeleteSkill( $dbConnect, $id )
	{
		$reason = "db fehler";
		$queryResult = queryDatabase(
			$dbConnect,
			"select count(*) ".
			"from appl_skills ".
			"where skill_id=$1 ",
			array( $id )
		);
		
		if( $queryResult && !is_object( $queryResult ) )
		{
			$skill = fetchQueryRow( $queryResult );
			$counter = current($skill);
			if( $counter > 0 )
				$reason = $counter . " Bewerber gefunden";
			else
				$reason = null;
		}

		if( !$reason )
		{
			$reason = "db fehler";
			$queryResult = queryDatabase(
				$dbConnect,
				"select count(*) ".
				"from job_skills ".
				"where skill_id=$1 ",
				array( $id )
			);
			
			if( $queryResult && !is_object( $queryResult ) )
			{
				$skill = fetchQueryRow( $queryResult );
				$counter = current($skill);
				if( $counter > 0 )
					$reason = $counter . " Job(s) gefunden";
				else
					$reason = null;
			}
	
		}

		if( !$reason )
		{
			$reason = "db fehler";
			$queryResult = queryDatabase(
				$dbConnect,
				"select count(*) ".
				"from skills ".
				"where parent_id=$1 ",
				array( $id )
			);
			
			if( $queryResult && !is_object( $queryResult ) )
			{
				$skill = fetchQueryRow( $queryResult );
				$counter = current($skill);
				if( $counter > 0 )
					$reason = $counter . " Element(e) gefunden";
				else
					$reason = null;
			}
		}

		return $reason;
	}

	function getSkillPath($dbConnect, $id)
	{
		$path = array();
		while( $id >= 0 )
		{
			$skill = getSkill($dbConnect, $id);
			if( !$skill )
				break;
			$path[] = $skill;
			$id = $skill['parent_id'];
		}
		return $path;
	}
	function getSkillPathStr($dbConnect, $id)
	{
		$path = getSkillPath($dbConnect, $id);
		foreach( $path as $entry )
		{
			if( isset( $pathStr ) )
				$pathStr = $entry['name'] . ":" . $pathStr;
			else
				$pathStr = $entry['name'];
		}

		return $pathStr;
	}

// ==========================================================================================================================
// applicant skills
// ==========================================================================================================================

	function getApplicantSkills( $dbConnect, $id, $withPath = true )
	{
		$queryResult = queryDatabase(
			$dbConnect,
			"select id, user_id, skill_id, start_y, end_y, part ".
			"from appl_skills ".
			"where user_id=$1 ".
			"order by skill_id",
			array( $id )
		);
		$skills = array();
		if( $queryResult && !is_object( $queryResult ) ) {
			while( $queryRecord = fetchQueryRow( $queryResult ) ) {
				if( $withPath )
					$queryRecord['path'] = getSkillPathStr($dbConnect, $queryRecord['skill_id']);
				$skills[] = $queryRecord;
			}
		}

		if( $withPath )
			return sortSkills( $skills );
		return $skills;
	}

// ==========================================================================================================================
// applicants
// ==========================================================================================================================

	function getApplicantCount( $dbConnect, $id )
	{
		$result = 0;
		
		$queryResult = queryDatabase(
			$dbConnect,
			"select count(*) ".
			"from applicants ".
			"where id=$1",
			array( $id )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$queryRecord = fetchQueryRow( $queryResult );
			
			if( $queryRecord )
			{
				$result = current($queryRecord);
			}
		}

		return $result;
	}

	function getApplicant( $dbConnect, $id )
	{
		global $applCvInfo;
		global $applMotInfo;

		$queryResult = queryDatabase(
			$dbConnect,
			"select a.id, a.education, a.position, a.title, a.description, a.birthday, a.planed_time, a.mobility, a.open, r.country, r.symbol ".
			"from applicants a ".
			"left outer join regions r on r.id = a.region ".
			"where a.id=$1",
			array( $id )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$applicant = fetchQueryRow( $queryResult );
			
			if( $applicant )
			{
				$applicant["description"] = urldecode($applicant["description"]);
				$applicant["birthday"] = urldecode($applicant["birthday"]);
				$applicant["country"] = urldecode($applicant["country"]);
				$applicant["symbol"] = urldecode($applicant["symbol"]);
				
				$applicant['skills'] = getApplicantSkills($dbConnect, $id);
				$applicant[$applCvInfo["idFieldName"]] = getDocumentID($dbConnect, $id, USER_CV);
				$applicant[$applMotInfo["idFieldName"]] = getDocumentID($dbConnect, $id, USER_MOTIV);
			}
			else
			{
				$applicant = array(
					"education" => null,
					"position" => null,
					"title" => null,
					"description" => "",
					"birthday" => "",
					"country" => "",
					"symbol" => "",
					"planed_time" => null,
					"mobility" => null,
					"open" => null,
					$applCvInfo["uiDeleteName"] => null,
					$applMotInfo["uiDeleteName"] => null,
					"skills" => array(),
					$applCvInfo["idFieldName"] => null,
					$applMotInfo["idFieldName"] => null
				);
			}
		}
		else
			$applicant = array();

		return $applicant;
	}
	function getSessionApplicant( $dbConnect, $id )
	{
		startSession();
		if( !array_key_exists( "applicant", $_SESSION ) )
			$_SESSION['applicant'] = getApplicant( $dbConnect, $id );
		return $_SESSION['applicant'];
	}
	function putSessionApplicant( $request )
	{
		global $applCvInfo;
		global $applMotInfo;

		startSession();
		if( array_key_exists( "applicant", $_SESSION ) )
			$applicant = $_SESSION['applicant'];
		else
		{
			$applicant = array();
			$applicant['skills'] = array();
		}

		$applicant['birthday'] = $request["birthday"];
		$applicant['country'] = $request["country"];
		$applicant['symbol'] = $request["symbol"];
		$applicant['education'] = $request["education"];
		$applicant['position'] = $request["position"];
		$applicant['title'] = $request["applTitle"];
		$applicant['description'] = $request["description"];
		$applicant['planed_time'] = $request["planed_time"];
		$applicant['mobility'] = $request["mobility"];
		$applicant['open'] = array_key_exists("open", $request ) ? 1 : 0;
		$tmp = $applCvInfo["uiDeleteName"];
		$applicant[$tmp] = checkBoolField($request, $tmp );
		$applicant[$applMotInfo["uiDeleteName"]] = checkBoolField($request, $applMotInfo["uiDeleteName"] );
		$newSkills = array();
		foreach($applicant['skills'] as $skill )
		{
			$skill['start_y'] = $request['start_y'.$skill['skill_id']];
			$skill['end_y'] = $request['end_y'.$skill['skill_id']];
			$skill['part'] = $request['part'.$skill['skill_id']];
			$newSkills[] = $skill;
		}
		$applicant['skills'] = $newSkills;
		
		$_SESSION['applicant'] = $applicant;
		return $applicant;
	}

	function putSessionApplFiles( $applicant, $reqFiles )
	{
		global $actUser;
		global $applCvInfo;
		global $applMotInfo;
	
		$tmpFile = $reqFiles[$applCvInfo["uiFieldName"]]['tmp_name'];
		if( $tmpFile && is_uploaded_file( $tmpFile ))
		{
			$destFile = STORAGE_PATH . $actUser['id'] . "cvFile.tmp";
			if( move_uploaded_file( $tmpFile, $destFile ) )
			{
				$applicant[$applCvInfo["uiFieldName"]] = $destFile;
				$applicant[$applCvInfo["sessionRemoteName"]] = $reqFiles[$applCvInfo["uiFieldName"]]['name'];
				$applicant[$applCvInfo["typeFieldName"]] = $reqFiles[$applCvInfo["uiFieldName"]]['type'];
			}
		}

		$tmpFile = $reqFiles[$applMotInfo["uiFieldName"]]['tmp_name'];
		if( $tmpFile && is_uploaded_file( $tmpFile ))
		{
			$destFile = STORAGE_PATH . $actUser['id'] . "motFile.tmp";
			if( move_uploaded_file( $tmpFile, $destFile ) )
			{
				$applicant[$applMotInfo["uiFieldName"]] = $destFile;
				$applicant[$applMotInfo["sessionRemoteName"]] = $reqFiles[$applMotInfo["uiFieldName"]]['name'];
				$applicant[$applMotInfo["typeFieldName"]] = $reqFiles[$applMotInfo["uiFieldName"]]['type'];
			}
		}
		$_SESSION['applicant'] = $applicant;
		return $applicant;
	}

	define( "SAVE_FUNC", "save" );
	define( "ADD_SKILL_FUNC", "addSkill" );
	define( "ADD_JOB_SKILL_FUNC", "addJobSkill" );
	define( "DEL_SKILL_FUNC", "deleteSkill" );
	define( "CANCEL_FUNC", "cancel" );

// ==========================================================================================================================
// company
// ==========================================================================================================================

	function getCompany( $dbConnect, $id )
	{
		$queryResult = queryDatabase(
			$dbConnect,
			"select c.id, c.name, c.branch, c.foundation, c.employees, c.address, c.description, r.country, r.symbol ".
			"from company c ".
			"left outer join regions r on r.id = c.region ".
			"where c.id=$1",
			array( $id )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$company = fetchQueryRow( $queryResult );
			
			if( $company )
			{
				$company["name"] = urldecode($company["name"]);
				$company["description"] = urldecode($company["description"]);
				$company["address"] = urldecode($company["address"]);
				$company["country"] = urldecode($company["country"]);
				$company["symbol"] = urldecode($company["symbol"]);
				$company['docID'] = getDocumentID($dbConnect, $id, COMPANY_DESCR);
			}
			else
			{
				$company = array(
					"name" => "",
					"branch" => null,
					"foundation" => null,
					"employees" => null,
					"address" => null,
					"description" => "",
					"country" => "",
					"symbol" => ""
				);
			}
		}
		else
			$company = array();

		return $company;
	}

// ==========================================================================================================================
// job skills
// ==========================================================================================================================

	function getJobSkills( $dbConnect, $id, $withPath = true )
	{
		$queryResult = queryDatabase(
			$dbConnect,
			"select id, job_id, skill_id, part ".
			"from job_skills ".
			"where job_id=$1 ".
			"order by skill_id",
			array( $id )
		);
		$skills = array();
		if( $queryResult && !is_object( $queryResult ) ) {
			while( $queryRecord = fetchQueryRow( $queryResult ) ) {
				if( $withPath )
					$queryRecord['path'] = getSkillPathStr($dbConnect, $queryRecord['skill_id']);
				$skills[] = $queryRecord;
			}
		}

		if( $withPath )
			return sortSkills( $skills );

		return $skills;
	}

// ==========================================================================================================================
// Jobs
// ==========================================================================================================================

	function createEmptyJob()
	{
		global $jobFileInfo;
		$job = array(
			"job_title" => "",
			"department" => "",
			"position" => null,
			"company_id" => null,
			"company_name" => null,
			"visible" => null,
			"open_date" => time(),
			"close_date" => time(),
			"description" => "",
			$jobFileInfo["idFieldName"] => null,
			"skills" => array()
		);
		return $job;
	}
	function getJob( $dbConnect, $id )
	{
		global $jobFileInfo;
		$queryResult = queryDatabase(
			$dbConnect,
			"select j.id, j.company_id, j.job_title, j.department, j.position, j.visible, j.open_date, j.close_date, j.description, ".
				"c.name as company_name " .
			"from jobs j ".
			"join company c on c.id = j.company_id " .
			"where j.id=$1",
			array( $id )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$job = fetchQueryRow( $queryResult );
			
			if( $job )
			{
				$job["job_title"] = urldecode($job["job_title"]);
				$job["department"] = urldecode($job["department"]);
				$job["description"] = urldecode($job["description"]);
				$job["company_name"] = urldecode($job["company_name"]);
				$job["skills"] = getJobSkills( $dbConnect, $id );
				$job[$jobFileInfo["idFieldName"]] = getDocumentID($dbConnect, $id, JOB_DESCR);
			}
			else
			{
				$job = createEmptyJob();
			}
		}
		else
			$job = array();

		return $job;
	}

	function jobOK( $job, $jobID )
	{
		return $jobID && $job && array_key_exists('id', $job ) && $job['id']==$jobID;
	}
	
	function getSessionJob( $dbConnect, $id )
	{
		startSession();
		$sessionJobKey = "job_" . $id;
		if( !array_key_exists( $sessionJobKey, $_SESSION ) )
			$_SESSION[$sessionJobKey] = getJob( $dbConnect, $id );


		return $_SESSION[$sessionJobKey];
	}

	function putSessionJob( $request )
	{
		global $jobFileInfo;

		startSession();
		$id = $request['id'];
		$sessionJobKey = "job_" . $id;
		
		if( array_key_exists( $sessionJobKey, $_SESSION ) )
			$job = $_SESSION[$sessionJobKey];
		else
			$job = createEmptyJob();

		if( !$job["company_id"] && array_key_exists("company_id",$request) && is_numeric($request["company_id"]) )
			$job['company_id'] = $request["company_id"];
		if( !$job["company_name"] && array_key_exists("company_name",$request) )
			$job['company_name'] = $request["company_name"];

		$job['job_title'] = $request["job_title"];
		$job['department'] = $request["department"];
		$job['position'] = $request["position"];
		$job['description'] = $request["description"];
		$job['visible'] = checkBoolField($request, "visible");
		$tmp = $jobFileInfo["uiDeleteName"];
		$job[$tmp] = checkBoolField($request, $tmp );

		$job['open_date'] = strtotime($request['open_date']);;
		$job['close_date'] = strtotime($request['close_date']);;

		$newSkills = array();
		foreach($job['skills'] as $skill )
		{
			$skill['part'] = $request['part'.$skill['skill_id']];
			$newSkills[] = $skill;
		}
		$job['skills'] = $newSkills;
		
		$_SESSION[$sessionJobKey] = $job;
		return $job;
	}

	function putSessionJobFile( $job, $request, $reqFiles)
	{
		global $actUser;
		global $jobFileInfo;
		$id = $request['id'];
		$sessionJobKey = "job_" . $id;

		$fieldName = $jobFileInfo['uiFieldName'];
		$tmpFile = $reqFiles[$fieldName]['tmp_name'];
		if( $tmpFile && is_uploaded_file( $tmpFile ))
		{
			$destFile = STORAGE_PATH . $actUser['id'] . $id . "jobFile.tmp";
			if( move_uploaded_file( $tmpFile, $destFile ) )
			{
				$job[$fieldName] = $destFile;
				$job[$jobFileInfo['sessionRemoteName']] = $reqFiles[$fieldName]['name'];
				$job[$jobFileInfo['typeFieldName']] = $reqFiles[$fieldName]['type'];
			}
		}
		$_SESSION[$sessionJobKey] = $job;
		return $job;
	}

	function fetchJob( $queryResult )
	{
		global $dbConnect;

		$job = fetchQueryRow( $queryResult );
		if( $job )
		{
			$job['job_title'] = urldecode($job['job_title']);
			if( array_key_exists( "department", $job ) )
				$job['department'] = urldecode($job['department']);
			if( array_key_exists( "company_name", $job ) )
				$job['company_name'] = urldecode($job['company_name']);
		}
		
		return $job;
	}
	
	define( "BROWSE_MODE", "browse" );			// find open jobs
	define( "SENT_APPL_MODE", "sentAppl" );		// find my applications sent
	define( "REC_APPL_MODE", "recAppl" );		// find applications received
	define( "EDIT_MODE", "edit" );				// find my open jobs
	
// ==========================================================================================================================
// Applications
// ==========================================================================================================================

	function hasApplication( $dbConnect, $jobID, $userID )
	{
		$queryResult = queryDatabase(
			$dbConnect,
			"select count(*) " .
			"from application ".
			"where job_id=$1 and user_id = $2",
			array( $jobID, $userID )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$rec = fetchQueryRow( $queryResult );
			return current($rec) > 0;
		}
		return false;
	}

	function calculateScore( $dbConnect, $jobID, $userID )
	{
		// find all skills and requirements
		$userSkills = getApplicantSkills( $dbConnect, $userID, false );
		$jobSkills = getJobSkills( $dbConnect, $jobID, false );
		

		// find the match
		$match = array();
		$ji = 0;
		$ui = 0;
		while( $ji<count($jobSkills) && $ui<count($userSkills) )
		{
			$jSkill = $jobSkills[$ji];
			$uSkill = $userSkills[$ui];
			
			if( $jSkill['skill_id'] < $uSkill['skill_id'] )
				$ji++;
			else if( $jSkill['skill_id'] > $uSkill['skill_id'] )
				$ui++;
			else
			{
				if( $uSkill['start_y'] > 1900 )
				{
					$uSkill['jobPart'] = $jSkill['part'];
					$match[] = $uSkill;
				}

				$ji++;
				$ui++;
			}
		}

		// find the user's max weight
		$maxWeight = 0;
		foreach( $userSkills as $skill )
		{
			if( $skill['part'] > $maxWeight ) 
				$maxWeight = $skill['part'];
		}
		
		// find the jobs's total weight
		$totalWeight = 0;
		foreach( $jobSkills as $skill )
		{
			$totalWeight += $skill['part'];
		}

		$score = 0;
		$thisYear = date('Y');
		$weightFactor = 1;
		foreach( $match as $skill )
		{
			$start_y = $skill['start_y'];
			$end_y = ($skill['end_y'] > 0 ? $skill['end_y'] : $thisYear) +1;
			if( $end_y > $start_y )
			{
				if($totalWeight > 0)
					$weightFactor = $skill['jobPart']/$totalWeight;
				$pastFactor = pow(0.9, $thisYear-$end_y);
				$usageFactor = $end_y - $start_y;
				$thisWeight = ($skill['part']*100/$maxWeight) * $usageFactor * $pastFactor * $weightFactor;
				$score += $thisWeight;
			}
		}

		return $score;
	}

// ==========================================================================================================================
// Documents
// ==========================================================================================================================

	define('USER_CV', 1);
	define('USER_MOTIV', 2);
	define('APPL_CV', 3);
	define('APPL_MOTIV', 4);
	define('COMPANY_DESCR', 5);
	define('JOB_DESCR', 6);

	function writeFileInput($record, $fieldInfo, $delFlag, $readOnly )
	{
		if( !$readOnly )
		{
			echo( "<input type='file' name='".$fieldInfo['uiFieldName']."' accept='application/pdf'>" );
			if( array_key_exists( $fieldInfo["sessionRemoteName"], $record) ) 
				echo($record[$fieldInfo["sessionRemoteName"]]." "); 
		}
		if( $record[$fieldInfo["idFieldName"]] ) 
		{
			echo("<a href='viewDoc.php?id=".$record[$fieldInfo["idFieldName"]]."'>Anzeigen</a> ");
			if(!$readOnly )
			{
				createCheckbox( $fieldInfo['uiDeleteName'], $delFlag, 1, $readOnly );
				echo " L&ouml;schen";
			}
		}
	}
	
	function getDocumentID( $dbConnect, $entityID, $kind )
	{
		$id = null;
		$queryResult = queryDatabase( $dbConnect, "select id from docs where entity_id = $1 and kind = $2", array($entityID, $kind) );
		if( $queryResult && !is_object( $queryResult ) )
		{
			$doc = fetchQueryRow( $queryResult );
			if( $doc )
				$id = $doc['id'];
		}
		
		return $id;
	}
	function getDocument( $dbConnect, $ID )
	{
		$doc = null;
		$queryResult = queryDatabase( $dbConnect, "select id, user_id, entity_id, kind, mimetype from docs where id = $1", array($ID) );
		if( $queryResult && !is_object( $queryResult ) )
		{
			$doc = fetchQueryRow( $queryResult );
			if( $doc )
				$doc['path'] = STORAGE_PATH . $doc['entity_id'] ."_" . $doc['kind'] . ".dat";
		}
		return $doc;
	}
	function deleteDocument( $dbConnect, $docID )
	{
		$doc = getDocument( $dbConnect, $docID );
		if( $doc )
		{
			queryDatabase( $dbConnect, "delete from docs where id = $1", array($docID) );
			unlink($doc['path']);
		}
	}

	function saveDocument( $dbConnect, $userID, $entityID, $kind, $mimeType, $source, $target )
	{
		$result = false;
		
		$docID = getDocumentID( $dbConnect, $entityID, $kind );
		if( $docID )
			queryDatabase( $dbConnect, "delete from docs where id = $1", array($docID) );
		else
			$docID = getNextID( $dbConnect, "docs", "id" );

		$queryResult = queryDatabase( 
			$dbConnect, 
			"insert into docs ( id, user_id, entity_id, kind, mimetype ) values ($1, $2, $3, $4, $5)",
			array($docID, $userID, $entityID, $kind, $mimeType )
		);
		if( $queryResult && !is_object( $queryResult ) )
		{
			$result = rename( $source, $target );
		}
		
		return $result;
	}
	function getUserCV( $userID )
	{
		return STORAGE_PATH . $userID ."_" . USER_CV . ".dat";
	}
	function getUserMotivation( $userID )
	{
		return STORAGE_PATH . $userID ."_" . USER_MOTIV . ".dat";
	}

	function getApplCV( $applicationID )
	{
		return STORAGE_PATH . $applicationID ."_" . APPL_CV . ".dat";
	}
	function getApplMotivation( $applicationID )
	{
		return STORAGE_PATH . $applicationID ."_" . APPL_MOTIV . ".dat";
	}

	function getCompanyDescr( $compayID )
	{
		return STORAGE_PATH . $compayID ."_" . COMPANY_DESCR . ".dat";
	}
	function getJobDescr( $jobID )
	{
		return STORAGE_PATH . $jobID ."_" . JOB_DESCR . ".dat";
	}

	function findUserCV($dbConnect, $userID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $userID, USER_CV );
		if( $id )
			$fileName = getUserCV( $userID );
		return $fileName;
	}	
	function findUserMotivation($dbConnect, $userID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $userID, USER_MOTIV );
		if( $id )
			$fileName = getUserMotivation( $userID );
		return $fileName;
	}	

	function findApplCV($dbConnect, $applID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $applID, APPL_CV );
		if( $id )
			$fileName = getApplCV( $applID );
		return $fileName;
	}	
	function findApplMotivation($dbConnect, $applID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $applID, APPL_MOTIV );
		if( $id )
			$fileName = getApplMotivation( $applID );
		return $fileName;
	}	

	function findCompanyDescr( $compayID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $compayID, COMPANY_DESCR );
		if( $id )
			$fileName = getCompanyDescr( $compayID );
		return $fileName;
	}
	function findJobDescr( $jobID )
	{
		$fileName = null;
		$id = getDocumentID( $dbConnect, $jobID, JOB_DESCR );
		if( $id )
			$fileName = getJobDescr( $jobID );
		return $fileName;
	}


// ==========================================================================================================================
?>

