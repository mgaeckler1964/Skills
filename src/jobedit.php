<?php 
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	$tryLogin = true;
	require_once( "includes/components/login.php" );
	include_once( "includes/tools/tools.php" ); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			$title = "Jobangebot";
			include_once( "includes/components/defhead.php" );

			$backURL = "jobedit.php";
			if( array_key_exists( "id", $_GET ) )
			{
				$id = $_GET["id"];
				$backURL = $backURL . "?id=" . $id;
			}
			else
			{
				$backURL = $backURL . "?id=" . 0;
			}
			$_SESSION['backURL'] = $backURL;
		?>
		<script>
			function addSkill()
			{
				document.getElementById("fnc").value = "<?php echo ADD_JOB_SKILL_FUNC; ?>";
				document.getElementById("frm").submit();
			}
			function deleteSkill(skill_id)
			{
				document.getElementById("fnc").value = "<?php echo DEL_SKILL_FUNC; ?>";
				document.getElementById("skill_id").value = skill_id;
				document.getElementById("frm").submit();
			}
			function cancelEdit()
			{
				document.getElementById("fnc").value = "<?php echo CANCEL_FUNC; ?>";
				document.getElementById("frm").submit();
			}
		</script>
	</head>
	<body class="center">
		<?php
			include( "includes/components/headerlines.php" );

			$delDoc = false;

			if( isset($id) )
			{
				$job = getSessionJob( $dbConnect, $id );

				if( $id == 0 || array_key_exists('id', $job ) )
				{
					$job_title = $job['job_title'];
					$department = $job['department'];
					$position = $job['position'];
					$description = $job['description'];
					$company_id = $job['company_id'];
					$company_name = $job['company_name'];
					$visible = $job['visible'];
					$open_date = $job['open_date'];
					$close_date = $job['close_date'];
					if( !isset($actUser) || $company_id != $actUser['id'] )
						$readOnly = true;
					$delDoc = checkBoolField( $job, $jobFileInfo['uiDeleteName'] );
				}
				else
					$error = "Jobangebot nicht gefunden";
			}
			else if( isset( $actUser ) )
			{				
				$company = getCompany( $dbConnect, $actUser['id'] );

				if( array_key_exists('id', $company ) && $company['id']>0 )
				{
					$job = createEmptyJob();
					$id = 0;
					$job_title = "";
					$department = "";
					$position = 0;
					$description = "";
					$visible = 0;
					$company_id = $company['id'];
					$company_name = $company['name'];
					$open_date = time();
					$close_date = time();
				}
				else
				{
					$error = "Sie m&uuml;ssen erst Ihre Firma einrichten.";
					$nextURL = "company.php";
				}
			}
			else
				$error = "Sie m&uuml;ssen sich erst anmelden.";
		?>

		<?php if( !isset( $error ) ) { ?>
			<form action="jobedit2.php" method="post" enctype="multipart/form-data" id="frm">
				<input type="hidden" name="func" id="fnc" value="<?php echo SAVE_FUNC; ?>">
				<input type="hidden" name="skill_id" id="skill_id" value="">
				<input type="hidden" name="company_id" value="<?php echo $company_id; ?>">
				<input type="hidden" name="company_name" value="<?php echo htmlspecialchars($company_name, ENT_QUOTES, 'ISO-8859-1'); ?>">
				<input type="hidden" name="id" value="<?php echo $id;?>">
	
				<table>
					<tr>
						<td class="fieldLabel">Jobtitel *</td>
						<td>
							<?php createField("job_title", "text", $job_title, isset( $readOnly ), false, true, TEXT_UI_WIDTH, 128 ); ?>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">Firma</td>
						<td><a href="company.php?id=<?php echo($company_id); ?>"> <?php echo htmlspecialchars($company_name, ENT_QUOTES, 'ISO-8859-1'); ?></a></td>
					</tr>
					<tr>
						<td class="fieldLabel">Abteilung *</td>
						<td>
							<?php createField("department", "text", $department, isset( $readOnly ), false ); ?>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">Position *</td>
						<td><?php 
							if( isset( $readOnly ) )
								writeConstantCombo($dbConnect, POSITION, $position); 
							else
								writeConstantCombo($dbConnect, POSITION, $position, "position", "pos"); 
						?></td>
					</tr>
					<tr>
						<td class="fieldLabel">Kurzbeschreibung *</td>
						<td>
							<?php createMemo("description", $description, isset( $readOnly ), false, false, TEXT_UI_WIDTH, TEXT_UI_HEIGHT ); ?>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">Beschreibung</td>
						<td>
							<?php 
								writeFileInput($job, $jobFileInfo, $delDoc, isset( $readOnly ) ); 
							?>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">Anforderungen</td>
						<td><?php
							if( isset($job) && array_key_exists('skills', $job ) )
							{
								foreach($job['skills'] as $skill )
								{
									echo(htmlspecialchars($skill['path'], ENT_QUOTES, 'ISO-8859-1'));
									if( array_key_exists('part', $skill ) )
										$part = $skill['part'];
									else
										$part = "";
								
									if(isset( $readOnly )) {
										echo " " . $part ."<br>";
									}
									else
									{
										
										echo( "<br>Anteil: <input name='part{$skill['skill_id']}' type='number' value='".htmlspecialchars($part, ENT_QUOTES, 'ISO-8859-1')."' required>\n" );
										echo( "<input type='button' onClick=\"deleteSkill('{$skill['skill_id']}');\" value='Del'>" );
										echo("<HR>\n");
									}
								}
							}
						?></td>
					</tr>
					<?php if( !isset( $readOnly ) ) { ?>
						<tr>
							<td class="fieldLabel">Sichtbar</td>
							<td><input type="checkbox" name="visible" value="1" <?php echo ($visible ? "checked" : ""); ?>></td>
						</tr>
						<tr>
							<td class="fieldLabel">Offen ab</td>
							<td><input type="datetime-local" step="60" required="required" name="open_date" value="<?php echo htmlspecialchars(formatHtmlTimeStamp($open_date)); ?>"></td>
						</tr>
					<?php } ?>
					<tr>
						<td class="fieldLabel">Bewerbungsschlu&szlig;</td>
						<td>
							<input type="datetime-local" step="60" required="required" name="close_date" value="<?php 
								echo htmlspecialchars(formatHtmlTimeStamp($close_date)); 
							?>" <?php 
								if( isset( $readOnly ) ) echo"readonly"; ?>							
							>
						</td>
					</tr>
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">&nbsp;</td>
						<td>
							<?php if( !isset( $readOnly ) ) { ?>
								<input type="submit" value="Speichern">
								<input type='button' onClick='addSkill();' value='SKill'>
								<input type='button' onClick='cancelEdit();' value='Abbruch'>
							<?php } else if( isset($actUser) && !hasApplication($dbConnect, $id, $actUser['id']) && $open_date < time() && $close_date > time() ){ ?>
								<a href="apply.php?id=<?php echo $id;?>">Bewerben</a>
							<?php } else if( $open_date > time() ){ ?>
								Bewerbung noch nicht offen.
							<?php } else if( $close_date < time() ){ ?>
								Bewerbungsfrist abgelaufen.
							<?php } ?>
						</td>
					</tr>
				</table>
			</form><br>
		<?php } else {
			echo( "<p><b>{$error}</b></p>" );
			if( isset( $nextURL ) )
				echo("<p><a href='{$nextURL}'>Weiter</a></p>");
		}
		include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
