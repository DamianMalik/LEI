<!DOCTYPE html>
<html lang="de">
<head>
	<!-- Required meta tags -->
	<title>LEI Abruf</title>
	<meta charset="utf-8">
	<meta name="viewport" 
	      content="width=device-width, initial-scale=1">  
	
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" 
	      href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" 
	      integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" 
	      crossorigin="anonymous">
	
	<!-- Zusätzliches CSS -->
	<link rel="stylesheet" 
	      href="style.css">
	
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" 
            integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" 
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" 
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" 
            integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" 
            crossorigin="anonymous"></script>
    
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

$Basis_URL_LEI = "https://leilookup.gleif.org/api/v2/leirecords?lei="; 
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

# Überprüfung, ob die GET-Abfrage leer ist.
if ($content == "[]" ) $Fehlermeldung = "Dies ist keine gültige LEI Nummer";

# String $content wird in JSON decodiert
$json_content_LEI=json_decode($content, true);


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
					placeholder="LEI Nummer">
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
		echo "</div>";
		echo "</body>";
		echo "</html>";
		exit;
	}
	
	# h1 with padding-top 3 
	echo '<p class="h1 pt-3 pb-3 font-Bitter">LEI ' 
			. $json_content_LEI['0']['LEI']['$']
			. '</p>'; 
	if ($json_content_LEI['0']['Entity']['EntityStatus']['$'] == "ACTIVE") {
		echo '<a href="#" 
				class="btn btn-success float-right btn-sm align-left font-Bitter disabled" 
				tabindex="-1" 
				role="button" 
				aria-disabled="true">'
				. $json_content_LEI['0']['Entity']['EntityStatus']['$']
				. '</a>';
	} else {
		echo '<a href="#" 
			class="btn btn-danger float-right btn-sm align-left font-Bitter disabled" 
			tabindex="-1" 
			role="button" 
			aria-disabled="true">'
			. $json_content_LEI['0']['Entity']['EntityStatus']['$']
			. '</a>';
	}  
	
	# Abruf URL als Link anzeigen
	echo '<a href="' 
			. $URL_LEI 
			. '" target="_blank" class="btn btn-link font-Bitter pl-0 pb-3" role="button">' 
			. $URL_LEI 
			. '</a><br>';
	?>
	
	<button type="button" 
			class="btn btn-outline-dark btn-sm font-Bitter" 
			data-toggle="collapse" 
			data-target="#demo">zeige JSON...</button>
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
					echo $json_content_LEI['0']['Entity']['LegalName']['$'];
				echo '</li>';
				# Prüfung, ob `OtherEntityName vorhanden` ist und 
				# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['@type'] 
					== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>OtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
					== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
					== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
					echo '</li>';
				}
				# Prüfung, ob MailRouting vorhanden ist
				if( isset( $json_content_LEI['0']['Entity']['LegalAddress']['MailRouting']['$']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Mail Routing</b></small><br>";
						echo $json_content_LEI['0']['Entity']['LegalAddress']['MailRouting']['$'];
					echo '</li>';
				}
					echo '<li class="list-group-item">';
						echo "<small><b>First Address Line</b></small><br>";
						echo $json_content_LEI['0']['Entity']['LegalAddress']['FirstAddressLine']['$'];
					echo '</li>';
				# Prüfung, ob AdditionalAddressLine vorhanden ist
				if( isset( $json_content_LEI['0']['Entity']['LegalAddress']['AdditionalAddressLine']['0']['$']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Additional Address Line</b></small><br>";
						echo $json_content_LEI['0']['Entity']['LegalAddress']['AdditionalAddressLine']['0']['$'];
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>City</b></small><br>";
					if( isset( $json_content_LEI['0']['Entity']['LegalAddress']['PostalCode']['$'] ) ){
						echo $json_content_LEI['0']['Entity']['LegalAddress']['PostalCode']['$'];
						echo " ";
					}
					echo $json_content_LEI['0']['Entity']['LegalAddress']['City']['$'];
				echo '</li>';
				echo '<li class="list-group-item">';
					echo "<small><b>Country</b></small><br>";
					echo $json_content_LEI['0']['Entity']['LegalAddress']['Country']['$'];
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
					echo $json_content_LEI['0']['Entity']['LegalName']['$'];
				echo '</li>';
				# Prüfung, ob `OtherEntityName vorhanden` ist und 
				# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['@type'] 
					== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>OtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
					== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
					echo '</li>';
				}
				# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
				# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
				if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
					&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
					== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					echo '<li class="list-group-item">';
						echo "<small><b>TransliteratedOtherEntityName"
							. " ("
							. $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type']
							. ")"
							. "</b></small><br>";
						echo $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
					echo '</li>';
				}
				
				# Prüfung, ob MailRouting vorhanden ist
				if( isset( $json_content_LEI['0']['Entity']['HeadquartersAddress']['MailRouting']['$']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Mail Routing</b></small><br>";
						echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['MailRouting']['$']; 
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>First Address Line</b></small><br>";
					echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['FirstAddressLine']['$'];
				echo '</li>';
				# Prüfung, ob AdditionalAddressLine vorhanden ist
				if( isset( $json_content_LEI['0']['Entity']['HeadquartersAddress']['AdditionalAddressLine']['0']['$']) ){
					echo '<li class="list-group-item">';
						echo "<small><b>Additional Address Line</b></small><br>";
						echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['AdditionalAddressLine']['0']['$'];
					echo '</li>';
				}
				echo '<li class="list-group-item">';
					echo "<small><b>City</b></small><br>";
					if( isset( $json_content_LEI['0']['Entity']['HeadquartersAddress']['PostalCode']['$']) ){
						echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['PostalCode']['$'];
						echo " ";
					}
					echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['City']['$'];
					echo '</li>';
					echo '<li class="list-group-item">';
					echo "<small><b>Country</b></small><br>";
					echo $json_content_LEI['0']['Entity']['HeadquartersAddress']['Country']['$'];
				echo '</li>';
			?>
			</ul>
		</div>
		
		<?php
		# **********************************************************
		# ***        Ländernamen herunterladen                   ***
		# **********************************************************
		$Basis_URL_Land = "https://restcountries.eu/rest/v2/alpha/"; 
		$URL_Land = $Basis_URL_Land
					. strtolower($json_content_LEI['0']['Entity']['HeadquartersAddress']['Country']['$']);
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
		if ($json_content_LEI['0']['Entity']['EntityStatus']['$'] == "ACTIVE") {
			# Entity Name
			$Entityname = $json_content_LEI['0']['Entity']['LegalName']['$'];
			# Prüfung, ob `OtherEntityName vorhanden` ist und 
			# der Typ `ALTERNATIVE_LANGUAGE_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'] ) 
				&& $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['@type'] 
				== "ALTERNATIVE_LANGUAGE_LEGAL_NAME") {
					$Entityname = $json_content_LEI['0']['Entity']['OtherEntityNames']['OtherEntityName']['0']['$'];
			}
			# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
			# der Typ `PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
				&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
				== "PREFERRED_ASCII_TRANSLITERATED_LEGAL_NAME") {
					$Entityname = $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
			}
			# Prüfung, ob `TransliteratedOtherEntityName` vorhanden ist und
			# der Typ `AUTO_ASCII_TRANSLITERATED_LEGAL_NAME` lautet.
			if( isset( $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'] ) 
				&& $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['@type'] 
				== "AUTO_ASCII_TRANSLITERATED_LEGAL_NAME") {
					$Entityname = $json_content_LEI['0']['Entity']['TransliteratedOtherEntityNames']['TransliteratedOtherEntityName']['0']['$'];
			} 
			# Zeilenumbruch durchführen, damit lange Adresszeilen 
			# für Briefetikett vermieden werden
			$Umbruchzeile = wordwrap($Entityname, $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
						$Adresszeilen[] = $Umbruchzeile;
				}
			
			# Prüfung, ob MailRouting vorhanden ist
			if( isset( $json_content_LEI['0']['Entity']['HeadquartersAddress']['MailRouting']['$'] ) ){
				$Umbruchzeile = wordwrap($json_content_LEI['0']['Entity']['HeadquartersAddress']['MailRouting']['$'], $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
					$Adresszeilen[] = $Umbruchzeile;
				}
			}
			# First Adress Line 
			$Umbruchzeile = wordwrap($json_content_LEI['0']['Entity']['HeadquartersAddress']['FirstAddressLine']['$'], $Max_Zeilenlaenge, "\n");
			$Subarray = explode("\n", $Umbruchzeile);
			foreach($Subarray AS $Umbruchzeile) {
				$Adresszeilen[] = $Umbruchzeile;
			}
			# Zusatz-Adressangabe
			# Prüfung, ob AdditionalAddressLine vorhanden ist
			if( isset( $json_content_LEI['0']['Entity']['HeadquartersAddress']['AdditionalAddressLine']['0']['$'] ) ){
				$Umbruchzeile = wordwrap($json_content_LEI['0']['Entity']['HeadquartersAddress']['AdditionalAddressLine']['0']['$'], $Max_Zeilenlaenge, "\n");
				$Subarray = explode("\n", $Umbruchzeile);
				foreach($Subarray AS $Umbruchzeile) {
					$Adresszeilen[] = $Umbruchzeile;
				}
			}
			
			# PLZ und Ort
			if ( $json_content_LEI['0']['Entity']['HeadquartersAddress']['PostalCode']['$'] <> ".") {
				$Adresszeilen[] = $json_content_LEI['0']['Entity']['HeadquartersAddress']['PostalCode']['$']
					. " " 
					. strtoupper($json_content_LEI['0']['Entity']['HeadquartersAddress']['City']['$']);
			} else {
				$Adresszeilen[] = strtoupper($json_content_LEI['0']['Entity']['HeadquartersAddress']['City']['$']);
			}
			
			# Land 
			# In der Export-Adresse soll das eigne Land nicht angedruckt werden. 
			if ( strtoupper($json_content_Land['translations']['de']) != "DEUTSCHLAND" ) {
				$Adresszeilen[] = strtoupper($json_content_Land['translations']['de']);
			}
		}
		?>
		<!-- Karte 3 -->
		<div class="card" style="width: 18rem;">
			<div class="card-header text-white bg-info">Export Adresse <small>(Zeilen 1-6)</small></div>
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
	$RTF = file_get_contents('Serienbrief.rtf.php');
	// PHP-Tags entfernen
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
	?>
	
	<!-- Button -->
	<a class="btn btn-dark font-Bitter" href="/adressen.csv" role="button">CSV Export</a>
	
	<!-- Button -->
	<!-- <a class="btn btn-dark font-Bitter disabled" href="#" role="button">Serienbrief Export</a> -->
	<a class="btn btn-outline-secondary font-Bitter" href="/Brief_1.rtf" role="button">Serienbrief Export</a>
	
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
</body>
</html>
