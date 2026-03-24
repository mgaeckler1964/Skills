<?php 
	include_once( "includes/tools/commontools.php" ); 
	startSession();
	include_once( "includes/components/login.php" ); 
	include_once( "includes/tools/tools.php" ); 

	if( array_key_exists("id", $_GET ) ) {
		unset($_SESSION['applicant']);
		$appl_id = checkField( $_GET, "appl_id", null );

		$id = $_GET['id'];
		$readOnly = true;
		$theUser = getUser( $dbConnect, $id );
		$title = "Bewerberprofil";
	} else {
		$appl_id = checkField( $_GET, "appl_id", null );
		$readOnly = false;
		$id = $actUser['id'];
		$theUser = $actUser;
		$title = "Ihr Bewerberprofil";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			include_once( "includes/components/defhead.php" );
		?>
		<script>
			function addSkill()
			{
				document.getElementById("fnc").value = "<?php echo ADD_SKILL_FUNC; ?>";
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
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			$nachname = $theUser['nachname'];
			$vorname = $theUser['vorname'];
			$email = $theUser['email'];

			$applicant = getSessionApplicant( $dbConnect, $id, $appl_id );

			$birthday = $applicant['birthday'];
			$country = $applicant['country'];
			$symbol = $applicant['symbol'];
			$education = $applicant['education'];
			$position = $applicant['position'];
			$applTitle = $applicant['title'];
			$planed_time = $applicant['planed_time'];
			$mobility = $applicant['mobility'];
			$open = $applicant['open'];
			$delCV = checkBoolField( $applicant, $applCvInfo['uiDeleteName'] );
			$delMOT = checkBoolField( $applicant, $applMotInfo['uiDeleteName'] );
			$description = $applicant['description'];

			$_SESSION['backURL'] = "applicant.php";
		?>

		<form action="applicant2.php" method="post" enctype="multipart/form-data" id="frm">
			<input type="hidden" name="func" id="fnc" value="<?php echo SAVE_FUNC; ?>">
			<input type="hidden" name="skill_id" id="skill_id" value="">

			<table>
				<tr>
					<td class="fieldLabel">Name</td>
					<td><?php echo htmlspecialchars($nachname ." " . $vorname, ENT_QUOTES, 'ISO-8859-1'); ?></td>
				</tr>
				<tr><td class="fieldLabel">Geburtstag</td><td>
					<?php createField( "birthday", "text", $birthday, $readOnly, true, true, 12, 12 ); ?>
				</td></tr>
				
				<tr><td class="fieldLabel">Land-Bezirk</td><td>
					<?php 
						createField( "country", "text", $country, $readOnly, true, false, 3, 3 ); 
						echo " - ";
						createField( "symbol", "text", $symbol, $readOnly, true, false, 3, 3 ); 
						if( !$readOnly )
							echo("<i>Verwenden Sie Autokenneichen z.B. A-L oder D-M</i>");
					?>
				</td></tr>
				<tr><td class="fieldLabel">E-Mail</td><td><?php echo htmlspecialchars($email, ENT_QUOTES, 'ISO-8859-1'); ?></td></tr>
				<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>

				<tr>
					<td class="fieldLabel">H&ouml;chster Schulabschlu&szlig;</td>
					<td><?php writeConstantCombo($dbConnect, EDUCATION, $education, "education", $readOnly ? "" : "edu", false); ?></td>
				</tr>
				<tr>
					<td class="fieldLabel">Angestrebte Position</td>
					<td><?php writeConstantCombo($dbConnect, POSITION, $position, "position", $readOnly ? "" : "pos", false); ?></td>
				</tr>
				<tr>
					<td class="fieldLabel">Titel</td>
					<td><?php writeConstantCombo($dbConnect, TITLE, $applTitle, "applTitle", $readOnly ? "" : "aplT", false); ?></td>
				</tr>
				<tr><td class="fieldLabel">Geplante Zeit</td><td>
					<?php 
						createField( "planed_time", "number", $planed_time, $readOnly, true );
						if( !$readOnly )
							echo("<i>Tragen Sie hier ein, f&uuml;r wie viele Monate Sie eine Stelle suchen. </i>");
						else
							echo(" Monate");
					?>
				</td></tr>
				<?php if ( !$readOnly ) { ?>
					<tr><td class="fieldLabel">Mobilit&auml;t</td><td>
						<?php createField( "mobility", "number", $mobility, $readOnly, true ); ?> <i>..., wieviele Bezirke Sie zur Erreichung Ihres Arbeitsplatz befahren wollen.</i>
					</td></tr>
					<tr><td class="fieldLabel">&Ouml;ffentlich</td><td>
						<?php createCheckbox( "open", $open, 1, $readOnly ); ?>
						<i>Hier legen Sie fest, ob Sie in einer Profilsuche sichtbar sein wollen. 
						Es hat keinen Einflu&szlig; f&uuml;r Firmen, bei denen Sie sich beworben haben.</i>
					</td></tr>
				<?php } ?>
				<tr>
					<td class="fieldLabel">Kurzbeschreibung</td>
					<td><?php createMemo("description", $description, $readOnly, true, 1024, TEXT_UI_WIDTH, TEXT_UI_HEIGHT );?></td>
				</tr>
				<tr>
					<td class="fieldLabel">Lebenslauf</td>
					<td><?php writeFileInput($applicant, $applCvInfo, $delCV, $readOnly ); ?></td>
				</tr>
				<tr>
					<td class="fieldLabel">Motivation</td>
					<td><?php writeFileInput($applicant, $applMotInfo, $delMOT, $readOnly ); ?></td>
				</tr>
				<tr>
					<td class="fieldLabel">Skills</td>
					<td><?php
						if( array_key_exists('skills', $applicant ) )
						{
							foreach($applicant['skills'] as $skill )
							{
								echo(htmlspecialchars($skill['path'], ENT_QUOTES, 'ISO-8859-1'));
								if( array_key_exists('start_y', $skill ) )
									$start_y = $skill['start_y'];
								else
									$start_y = "";
								if( array_key_exists('end_y', $skill ) )
									$end_y = $skill['end_y'];
								else
									$end_y = "";
								if( array_key_exists('part', $skill ) )
									$part = $skill['part'];
								else
									$part = "";
								
								if( $readOnly )
								{
									if( $start_y )
										echo( " von ". $start_y );
									if( $end_y )
										echo( " bis ". $start_y );
									if( $part )
										echo( " ". $part );
									echo("<br>");
								}
								else
								{
									echo( "<br>\nStart: " );
									createField( "start_y".$skill['skill_id'], 'number', $start_y, $readOnly, true, false, 4, 4 );

									echo( "\nEnde: " );
									createField( "end_y".$skill['skill_id'], 'number', $end_y, $readOnly, true, false, 4, 4 );

									echo( "\nAnteil: " );
									createField( "part".$skill['skill_id'], 'number', $part, $readOnly, true, false, 4, 4 );

									echo( "\n<input type='button' onClick=\"deleteSkill('{$skill['skill_id']}');\" value='Del'>" );
									echo("<HR>\n");
								}
							}
						}
						if( !$readOnly )
							echo("<i>Start: Das Jahr, wann Sie diese F&auml;higkeit erworben haben. Ende: ,,. wann Sie es das letzte Mal verwendet haben. Anteil: eine Gewichtung</i>");
					?></td>
				</tr>

				<?php if( !$readOnly ) { ?>
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">&nbsp;</td>
						<td>
							<input type="submit" value="Speichern">
							<input type='button' onClick='addSkill();' value='SKill'>
							<input type='button' onClick='cancelEdit();' value='Abbruch'>
						</td>
					</tr>
				<?php } ?>
			</table>
		</form>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
