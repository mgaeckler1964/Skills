<?php 
	if( array_key_exists( "id", $_GET ) )
	{
		$id = $_GET['id'];
		$tryLogin = true;
		$readOnly = true;
	}
	include_once( "includes/components/login.php" ); 
	if( !isset( $id ) )
		$id = $actUser['id'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">

<html>
	<head>
		<?php
			if( isset( $readOnly ) )
				$title = "Firmenprofil";
			else
				$title = "Ihr Firmenprofil";
			include_once( "includes/components/defhead.php" );
		?>
	</head>
	<body>
		<?php
			include( "includes/components/headerlines.php" );

			$company = getCompany( $dbConnect, $id );

			$id = array_key_exists("id", $company ) ? $company['id'] : null;
			$name = $company['name'];
			$branch = $company['branch'];
			$foundation = $company['foundation'];
			$employees = $company['employees'];
			$address = $company['address'];
			$description = $company['description'];
			$country = $company['country'];
			$symbol = $company['symbol'];
		?>

		<form action="company2.php" method="post" enctype="multipart/form-data" id="frm">
			<input type="hidden" name="id" value="<?php echo $id; ?>">

			<table>
				<tr><td class="fieldLabel">Name *</td><td>
					<?php createField("name", "text", $name, isset( $readOnly ), false, true, TEXT_UI_WIDTH, 128 ); ?>
				</td></tr>
				<tr>
					<td class="fieldLabel">Branche *</td>
					<?php if( isset( $readOnly ) ) { ?>
						<td><?php writeConstantCombo($dbConnect, BRANCH, $branch); ?></td>
					<?php } else { ?>
						<td><?php writeConstantCombo($dbConnect, BRANCH, $branch, "branch", "branch"); ?></td>
					<?php } ?>
				</tr>

				<tr><td class="fieldLabel">Gr&uuml;ndung *</td><td>
					<?php createField("foundation", "number", $foundation, isset( $readOnly ), false ); ?>
				</td></tr>

				<tr><td class="fieldLabel">Mitarbeiter *</td><td>
					<?php createField("employees", "number", $employees, isset( $readOnly ), false ); ?>
				</td></tr>
				
				<tr><td class="fieldLabel">Land-Bezirk *</td><td>
					<?php createField("country", "text", $country, isset( $readOnly ), false, false, 4, 4 ); ?> -
					<?php createField("symbol", "text", $symbol, isset( $readOnly ), false, 8, 8 ); ?>
				</td></tr>
				<tr><td class="fieldLabel">Adresse *</td><td>
					<?php createField("address", "text", $address, isset( $readOnly ), false, false, TEXT_UI_WIDTH, 128 ); ?>
				</td></tr>

				<tr>
					<td class="fieldLabel">Kurzbeschreibung</td>
					<td>
						<?php createMemo("description", $description, isset( $readOnly ), false, 1024, TEXT_UI_WIDTH, TEXT_UI_HEIGHT ); ?>
					</td>
				</tr>
				<tr><td class="fieldLabel">Beschreibung</td>
				<td>
					<?php writeFileInput( $company, $companyFileInfo, false, isset($readOnly) ); ?>
				</td></tr>

				<?php if( !isset( $readOnly ) ) { ?>
					<tr><td class="fieldLabel">&nbsp;</td><td>&nbsp;</td></tr>
					<tr>
						<td class="fieldLabel">&nbsp;</td>
						<td>
							<input type="submit" value="Speichern">
							<input type='button' onClick='window.history.back();' value='Abbruch'>
						</td>
					</tr>
				<?php } ?>
			</table>
		</form>
		<?php include( "includes/components/footerlines.php" ); ?>
	</body>
</html>
