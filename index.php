<!DOCTYPE html>
<html lang="de">
<head>
	
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" 
		content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" 
			href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" 
			integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" 
			crossorigin="anonymous">
	
	
	<title>LEI Abruf</title>
	
	<!-- Zusätzliches CSS -->
	<link rel="stylesheet" 
	      href="style.css">
	
	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet"> 

</head>
<body class="bg-white">
<?php

# **********************************************************
# ***         LEI definieren und Variable definieren     ***
# **********************************************************

# Werte aus der Formulareingabe auslesen
# Hierbei werden unerwünschte Zeichen und Tags entfernt
# htmlentities --> Aus der Zeichenkette werden HTML-Tags in Code umgewandelt. 
# strip_tags   --> Aus der Zeichenkette werden HTML- und PHP-Tags entfernt.
# preg_replace --> Aus der Zeichenkette werden bestimmte Zeichen gelöscht.
if(isset($_GET['LEI'])) {
	$LEI = preg_replace('![^0-9A-Z]!', '', strip_tags(htmlentities($_GET['LEI']))) ;
} else {
	$LEI = "INR2EJN1ERAN0W5ZP974"; // falls LEI nicht gesetzt wurde
} // Ende der If-Abfrage

if (strlen($LEI) <> 20 ) $Fehlermeldung = "Dies ist keine gültige LEI Nummer";

# $Basis_URL_LEI = "https://leilookup.gleif.org/api/v2/leirecords?lei="; 
$Basis_URL_LEI = "https://api.gleif.org/api/v1/lei-records/"; 

$URL_LEI = $Basis_URL_LEI . $LEI;


# **********************************************************
# ***        LEI-Daten  herunterladen                    ***
# ********************************************************** 

$datei = $URL_LEI;
if (function_exists('curl_version')) {
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $datei);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$content = curl_exec($curl);
	curl_close($curl);
	# echo "LEI mit CURL abgerufen." . "<br>"; 
} else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
	$content = file_get_contents($datei);
	# echo "LEI mit FOPEN abgerufen." . "<br>"; 
	
} else {
	# echo "Sie haben weder cURL installiert, ";
	# echo "noch allow_url_fopen aktiviert. ";
	# echo "Bitte aktivieren/installieren ";
	# echo "allow_url_fopen oder Curl!";
}


# **********************************************************
# ***                 Fehlerbehandlung                   ***
# ********************************************************** 

# Überprüfung, ob die GET-Abfrage leer ist.
if ($content == "[]" ) $Fehlermeldung = "Dies ist keine gültige LEI Nummer";

# Überprüfung, ob die GET-Abfrage unter leer ist.
if ($content == "[]" ) $Fehlermeldung = "Dies ist keine gültige LEI Nummer";

# String $content wird in JSON decodiert
$json_content_LEI=json_decode($content, true);

# Prüfung, ob die Antwort ein gültiges JSON ist, ansonsten Fehlermeldung
if (json_last_error() <> JSON_ERROR_NONE) {
	$Fehlermeldung = "Fehler: Die Abfrage kann nicht verarbeitet werden";

	# Überprüfung auf 403 Forbidden
	if (strpos($content, '403 Forbidden')) { 
		$Fehlermeldung2 = "403 Forbidden Error"; 
	}

	# Überprüfung auf 500 Internal Server Error
	if (strpos($content, '500 Internal Server Error')) { 
		$Fehlermeldung2 = "500 Internal Server Error"; 
	}

	# Überprüfung auf 502 Bad Gateway
	if (strpos($content, '502 Bad Gateway')) { 
		$Fehlermeldung2 = "502 Bad Gateway"; 
	}

	# Überprüfung auf 503 Service Unavailable
	if (strpos($content, '503 Service Unavailable')) { 
		$Fehlermeldung2 = "503 Service Unavailable"; 
	}

	# Überprüfung auf 504 Gateway Timeout
	if (strpos($content, '504 Gateway Timeout')) { 
		$Fehlermeldung2 = "504 Gateway Timeout"; 
	}
}


# **********************************************************
# ***               HTML-Container                       ***
# ********************************************************** 

?>
<div class="container overflow-hidden">
	
	<!--Navbar-->	
	<nav class="navbar navbar-expand-md bg-dark navbar-dark">
		<form class="form-inline col-11">
			<input 	class="form-control mr-sm-2 font-Bitter" 
					type="text" 
					name="LEI" 
					placeholder="LEI Nummer...">
			<button class="btn btn-outline-light font-Bitter" 
					type="submit">Suche
					</button>
		</form>
	</nav> 
	<?php
	
	# **********************************************************
	# ***               HTML-Container                       ***
	# ********************************************************** 
	# Überprüfung, ob Fehlermeldung vorliegt. 
	# Bei vorliegen eines Fehlers wird das Skript abgebrochen. 
	if( isset( $Fehlermeldung )){
		echo '<p class="h1 pt-3 pb-3 font-Bitter">' . $Fehlermeldung . '</p>'; 
		echo '<p class="h3 pt-3 pb-3 font-Bitter">' . $Fehlermeldung2 . '</p>'; 
		echo "</div>";
		echo "</body>";
		echo "</html>";
		exit;
	}
	
	# h1 with padding-top 3 
	# Anzeige LEI ID
	echo '<p class="h1 pt-3 pb-3 font-Bitter">LEI ' 
			. $json_content_LEI['data']['id']
			. '</p>'; 
	# Abruf URL als Link anzeigen
	echo '<a href="' 
			. $URL_LEI 
			. '" target="_blank" class="btn btn-link font-Bitter pl-0 pb-3" role="button">' 
			. $URL_LEI 
			. '</a><br>';
	?>
	
	
	<?php
	# **********************************************************
	# ***        Update-Informationen und zeige JSON         ***
	# ********************************************************** 
	
	# Update-Informationen aus JSON ermitteln 
	$Registration_Initial = $json_content_LEI['data']['attributes']['registration']['initialRegistrationDate'];
	$Registration_LastUpdate = $json_content_LEI['data']['attributes']['registration']['lastUpdateDate']; 
	?>
	
	<?php
	# JSON-Button anzeigen
	?>
	<button type="button" 
			class="btn btn-outline-dark btn-sm font-Bitter mr-1" 
			data-toggle="collapse" 
			data-target="#demo">zeige JSON...</button>
	<?php
	# Update-Informationen im Button-Format anzeigen
	echo '<button type="button" class="btn btn-dark btn-sm font-Bitter mr-1" disabled><b>Registration:</b> <small>' . date("d.m.Y", strtotime($Registration_Initial)) . '</small></button>';
	echo '<button type="button" class="btn btn-dark btn-sm font-Bitter mr-1" disabled><b>Letztes Update:</b> <small>' . date("d.m.Y", strtotime($Registration_LastUpdate)) . '</small></button>';
	# Button "Active" / "Passive" anzeigen
	if ($json_content_LEI['data']['attributes']['entity']['status'] == "ACTIVE") {
		echo '<button type="button" class="btn btn-success btn-sm font-Bitter mr-1" disabled><b>'
			. $json_content_LEI['data']['attributes']['entity']['status']
			. '</b></button>';
	} else {
		echo '<button type="button" class="btn btn-danger btn-sm font-Bitter mr-1" disabled><b>'
			. $json_content_LEI['data']['attributes']['entity']['status']
			. '</b></button>';
		} 
	?>
	
	<div id="demo" class="pt-3 small collapse font-Bitter">
		<?php
		# Inhalt des JSON ausgeben
		echo $content;
		?>
	</div>
	
	<!-- Abstand -->
	<div class="border-top my-3"></div>
	
	<?php
	# **********************************************************
	# ***               Adresskarten anzeigen                ***
	# ********************************************************** 
	?>
	<div class="card-deck font-Bitter">
	
		<!-- Adresskarte 1 -->
		<div class="card" style="width: 18rem;">
			<div class="card-header text-white bg-dark">Legal Address</div>
			<ul class="list-group list-group-flush bg-light">
			<?php
				echo '<li class="list-group-item">';
					echo "<small><b>Legal Name</b></small><br>";
					# Legal Name
					echo $json_content_LEI['data']['attributes']['entity']['legalName']['name'];
				echo '</li>';
				# Prüfung, ob `OtherName vorhanden` ist und 
				# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['type'] 
					== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>OtherNames"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
					== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
					== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob MailRouting vorhanden ist
				if( isset( $json_content_LEI['data']['attributes']['entity']['legalAddress']['mailRouting']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Mail Routing</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['mailRouting'];
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>First Address Line</b></small><br>";
					echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['addressLines']['0'];
				echo '</li>';
				# Prüfung, ob AdditionalAddressLine vorhanden ist
				if( isset( $json_content_LEI['data']['attributes']['entity']['legalAddress']['addressLines']['1']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Additional Address Line</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['addressLines']['1'];
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>City</b></small><br>";
					if( isset( $json_content_LEI['data']['attributes']['entity']['legalAddress']['postalCode'] ) ){
						echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['postalCode'];
						echo " ";
					}
					echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['city'];
				echo '</li>';
				echo '<li class="list-group-item">';
					echo "<small><b>Country</b></small><br>";
					echo $json_content_LEI['data']['attributes']['entity']['legalAddress']['country'];
				echo '</li>';
			?>
			</ul>
		</div>
		
		<!-- Karte 2 -->
		<div class="card" style="width: 18rem;">
			<div class="card-header text-white bg-dark">Headquarters Address</div>
			<ul class="list-group list-group-flush bg-light">
			<?php
				echo '<li class="list-group-item">';
					echo "<small><b>Legal Name</b></small><br>";
					echo $json_content_LEI['data']['attributes']['entity']['legalName']['name'];
				echo '</li>';
				# Prüfung, ob `OtherEntityName vorhanden` ist und 
				# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['type'] 
					== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>OtherEntityName"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['type'] 
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type']  
					== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
					&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type']  
					== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
					echo '</li>';
				}
				# Prüfung, ob MailRouting vorhanden ist
				if( isset( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['mailRouting']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Mail Routing</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['mailRouting']; 
					echo '</li>';
				}
				# First Address Line
				echo '<li class="list-group-item">';
					echo "<small><b>First Address Line</b></small><br>";
					echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['0'];
				echo '</li>';
				# Prüfung, ob AdditionalAddressLine vorhanden ist
				if( isset( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['1']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Additional Address Line</b></small><br>";
						echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['1'];
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>City</b></small><br>";
					if( isset( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['postalCode'] ) ){
						echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['postalCode'];
						echo " ";
					}
					echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['city'];
					echo '</li>';
					echo '<li class="list-group-item">';
					echo "<small><b>Country</b></small><br>";
					echo $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['country'];
				echo '</li>';
			?>
			</ul>
		</div>
		<?php
		# **********************************************************
		# ***        Ländernamen herunterladen                   ***
		# **********************************************************
		$Basis_URL_Land = "https://restcountries.com/v3.1/alpha/"; 
		$URL_Land = $Basis_URL_Land
					. strtolower($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['country']);
		$datei = $URL_Land;
		if (function_exists('curl_version')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $datei);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$content = curl_exec($curl);
			curl_close($curl);
			# echo "LEI mit CURL abgerufen." . "<br>"; 
		} else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
			$content = file_get_contents($datei);
			# echo "LEI mit FOPEN abgerufen." . "<br>"; 
		} else {
			# echo "Sie haben weder cURL installiert, ";
			# echo "noch allow_url_fopen aktiviert. ";
			# echo "Bitte aktivieren/installieren ";
			# echo "allow_url_fopen oder Curl!";
		}
		$json_content_Land=json_decode($content, true);
		
		
		# **********************************************************
		# ***        Relevante Adresszeilen ermitteln            ***
		# **********************************************************
		
		// Variable als Array deklarieren
		$Adresszeilen = array();
		$Max_Zeilenlaenge = 49;
		
		# Nur Aktive Entity wird ausgegeben. 
		# Inactive Entity wird nicht ausgegeben. 
		if ($json_content_LEI['data']['attributes']['entity']['status'] == "ACTIVE") {
			# Entity Name
			$Entityname = $json_content_LEI['data']['attributes']['entity']['legalName']['name'];
			# Prüfung, ob `OtherEntityName vorhanden` ist und 
			# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'] ) 
				&& $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['type'] 
				== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					$Entityname = $json_content_LEI['data']['attributes']['entity']['otherNames']['0']['name'];
			}
			# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
			# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
				&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type']
				== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					$Entityname = $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
			}
			# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
			# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'] ) 
				&& $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['type'] 
				== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					$Entityname = $json_content_LEI['data']['attributes']['entity']['transliteratedOtherNames']['0']['name'];
			} 
			# Zeilenumbruch durchführen, damit lange Adresszeilen 
			# für Briefetikett vermieden werden
			$Umbruchzeile = wordwrap($Entityname, $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
						$Adresszeilen[] = $Umbruchzeile;
				}
			# Prüfung, ob MailRouting vorhanden ist
			if( isset( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['mailRouting'] ) ){
				$Umbruchzeile = wordwrap($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['mailRouting'], $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
					$Adresszeilen[] = $Umbruchzeile;
				}
			}
			# First Adress Line 
			$Umbruchzeile = wordwrap($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['0'], $Max_Zeilenlaenge, "\n");
			$Subarray = explode("\n", $Umbruchzeile);
			foreach($Subarray AS $Umbruchzeile) {
				$Adresszeilen[] = $Umbruchzeile;
			}
			# Zusatz-Adressangabe
			# Prüfung, ob AdditionalAddressLine vorhanden ist
			if( isset( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['1'] ) ){
				$Umbruchzeile = wordwrap($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['addressLines']['1'], $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
					$Adresszeilen[] = $Umbruchzeile;
				}
			}
			# PLZ und Ort
			if( isset ($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['postalCode'] )){
				if ( $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['postalCode'] <> ".") {
				$Adresszeilen[] = $json_content_LEI['data']['attributes']['entity']['headquartersAddress']['postalCode'] 
					. " " 
					. strtoupper($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['city']);
				}
			} else {
				$Adresszeilen[] = strtoupper($json_content_LEI['data']['attributes']['entity']['headquartersAddress']['city']);
			}
			# Land 
			# In der Export-Adresse soll das eigne Land nicht angedruckt werden. 
			if ( strtoupper($json_content_Land['0']['translations']['deu']['official']) != "DEUTSCHLAND" ) {
				$Adresszeilen[] = strtoupper($json_content_Land['0']['translations']['deu']['official']);
			}
		}
		?>
		<!-- Karte 3 -->
		<div class="card" style="width: 18rem;">
			<div class="card-header text-white bg-info">Export Adresse</div>
			<ul class="list-group list-group-flush bg-light">
				<?php
				# Alle Array Werte ausgeben
				foreach ($Adresszeilen as $zeile => $inhalt) {
					echo '<li class="list-group-item">';
						echo "<small><b>Adresszeile " . ( $zeile + 1 ) . "</b></small><br>";
						echo $inhalt . "<br>";
					echo '</li>';
				}
				?>
			</ul>
		</div>
	</div>
	<br>
	
	
	<?php
	# **********************************************************
	# ***             CSV-Output vorbereiten                 ***
	# ********************************************************** 
	
	# Definition der Überschriften 
	$Ueberschriften = array("Adresszeile 1", "Adresszeile 2",
	                        "Adresszeile 3", "Adresszeile 4", 
	                        "Adresszeile 5", "Adresszeile 6",
	                        "Adresszeile 7", "Adresszeile 8");
	
	# CSV Datei wird auf dem Server speichern
	$CSV_Datei = fopen('adressen.csv', 'w');
	# fügt BOM ein, um UTF-8 in Excel darzustellen
	fprintf($CSV_Datei, chr(0xEF).chr(0xBB).chr(0xBF));
	# Schreibe Überschrift
	fputcsv($CSV_Datei, $Ueberschriften, ";");
	# Screibe Adressdaten
	fputcsv($CSV_Datei, $Adresszeilen, ";");
	fclose($CSV_Datei);
	
	
	# **********************************************************
	# ***             RTF-Dokument verändern                 ***
	# ********************************************************** 
	
	# RTF / PHP Datei öffnen
	# $RTF = file_get_contents('Serienbrief.rtf.php');
	// PHP-Tags entfernen
	/*
	$RTF = str_replace('<?php /*', '', $RTF);
	$RTF = str_replace('?>', '', $RTF);
	// Variablen ersetzen
	# Hierbei wird überprüft, ob der Arrayzeiger gesetzt ist. 
	$RTF = isset($Adresszeilen[0]) ? str_replace('%ADRESSZEILE_1%', $Adresszeilen[0], $RTF) : '';
	$RTF = isset($Adresszeilen[1]) ? str_replace('%ADRESSZEILE_2%', $Adresszeilen[1], $RTF) : '';
	$RTF = isset($Adresszeilen[2]) ? str_replace('%ADRESSZEILE_3%', $Adresszeilen[2], $RTF) : '';
	$RTF = isset($Adresszeilen[3]) ? str_replace('%ADRESSZEILE_4%', $Adresszeilen[3], $RTF) : '';
	$RTF = isset($Adresszeilen[4]) ? str_replace('%ADRESSZEILE_5%', $Adresszeilen[4], $RTF) : '';
	$RTF = isset($Adresszeilen[5]) ? str_replace('%ADRESSZEILE_6%', $Adresszeilen[5], $RTF) : '';
	$RTF = isset($Adresszeilen[6]) ? str_replace('%ADRESSZEILE_7%', $Adresszeilen[6], $RTF) : '';
	
	# $RTF = iconv("ISO-8859-1", "UTF-8", $RTF);
	# RTF Datei auf dem Server speichern
	file_put_contents("Brief_1.rtf", $RTF);
	*/
	?>
	
	<!-- Button -->
	<a class="btn btn-dark font-Bitter" href="/adressen.csv" role="button">CSV Export</a>
	
	<!-- Button -->
	<a class="btn btn-outline-secondary font-Bitter disabled" href="#" role="button">Serienbrief Export</a>
	
	<!-- Abstand -->
	<div class="border-top my-3"></div>
	
	<!-- Link -->
	<nav class="navbar justify-content-end navbar-light bg-light font-Bitter">
		<a 	target="_blank" 
			class="navbar-brand" 
			href="https://github.com/DamianMalik/LEI">GitHub
			<img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" 
				 width="30" 
				 height="30" 
				 class="d-inline-block align-top" 
				 alt="">
		</a>
	</nav>
</div>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
		integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" 
		crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" 
		integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" 
		crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" 
		integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" 
		crossorigin="anonymous"></script>
</body>
</html>
