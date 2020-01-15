<!DOCTYPE html>
<html lang="de">
<head>
	<!-- Required meta tags -->
	<title>LEI Abruf</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">  
	
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	
	<!-- Zusätzliches CSS -->
	<link rel="stylesheet" href="style.css">
	
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    
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
	$LEI = "353800ZR4YBXWTFO6014"; 
} // Ende der If-Abfrage


$Basis_URL = "https://leilookup.gleif.org/api/v2/leirecords?lei="; 
$Abruf_URL = $Basis_URL . $LEI;

	# **********************************************************
	# ***        LEI-Daten  herunterladen                    ***
	# ********************************************************** 


	$datei = $Abruf_URL;
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
$json_content=json_decode($content, true);

?>

	
	<!--Container-->	
	<div class="container">
		<?php 
		foreach ($json_content as $element) {
		?>
		
		<!--Navbar-->	
		<nav class="navbar navbar-expand-md bg-dark navbar-dark">
			<form class="form-inline col-11">
				<input class="form-control mr-sm-2 font-Bitter" 
				       type="text" 
				       name="LEI" 
				       placeholder="LEI Nummer">
				<button class="btn btn-outline-light font-Bitter" 
				        type="submit">Suche</button>
			</form>
		</nav> 
	
		<!-- h1 with padding-top 3 -->
		<?php
			echo '<p class="h1 pt-3 pb-3 font-Bitter">LEI ' 
			    . $element["LEI"]["$"] 
			    . ' ';
				if ($element["Entity"]["EntityStatus"]["$"] == "ACTIVE") {
					echo '<button type="button" 
					       class="btn btn-success 
					       float-right" 
					       disabled>' 
					       . $element["Entity"]["EntityStatus"]["$"] 
					       . '</button>';
				} else {
					echo '<button type="button" 
					              class="btn btn-danger float-right" 
					              disabled>' 
					     . $element["Entity"]["EntityStatus"]["$"] 
					     . '</button>';
				}  
			echo '</p>'; 
		} 
		
		?>
		
		<?php
		# Abruf URL als Link anzeigen
		echo '<a href="' 
		        . $Abruf_URL 
		        . '" target="_blank" 
		             class="btn btn-link font-Bitter pl-0 pb-3" 
		             role="button">' 
		        . $Abruf_URL 
		        . '</a><br>';
		?>
		
		<button type="button" class="btn btn-outline-dark btn-sm font-Bitter" data-toggle="collapse" data-target="#demo">zeige JSON...</button>
		<div id="demo" class="pt-3 small collapse font-Bitter">
			<?php
			echo $content;
			?>
		</div>
		
		<!-- Abstand -->
		<div class="border-top my-3"></div>
  
		
		<!-- Karten -->
		<div class="card-deck font-Bitter">
			<!-- Karte 1 -->
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-dark">Legal Address</div>
				<ul class="list-group list-group-flush bg-light">
				<?php
				foreach ($json_content as $element) {
					echo '<li class="list-group-item">';
						echo "<small><b>Legal Name</b></small><br>";
						echo $element["Entity"]["LegalName"]["$"];
					echo '</li>';
					# Prüfung, ob OtherEntityName vorhanden ist
					if( isset( $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Other Entity Name"
							. " ("
							. $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["@type"]
							. ")"
							. "</b></small><br>";
							echo $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"];
						echo '</li>';
					}
					# Prüfung, ob MailRouting vorhanden ist
					if( isset( $element["Entity"]["LegalAddress"]["MailRouting"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Mail Routing</b></small><br>";
							echo $element["Entity"]["LegalAddress"]["MailRouting"]["$"];
						echo '</li>';
					}
					echo '<li class="list-group-item">';
						echo "<small><b>First Address Line</b></small><br>";
						echo $element["Entity"]["LegalAddress"]["FirstAddressLine"]["$"];
					echo '</li>';
					# Prüfung, ob AdditionalAddressLine vorhanden ist
					if( isset( $element["Entity"]["LegalAddress"]["AdditionalAddressLine"]["0"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Additional Address Line</b></small><br>";
							echo $element["Entity"]["LegalAddress"]["AdditionalAddressLine"]["0"]["$"];
						echo '</li>';
					}
					echo '<li class="list-group-item">';
						echo "<small><b>City</b></small><br>";
						if( isset( $element["Entity"]["LegalAddress"]["PostalCode"]["$"] ) ){
							echo $element["Entity"]["LegalAddress"]["PostalCode"]["$"];
							echo " ";
						}
						echo $element["Entity"]["LegalAddress"]["City"]["$"];
					echo '</li>';
					echo '<li class="list-group-item">';
						echo "<small><b>Country</b></small><br>";
						echo $element["Entity"]["LegalAddress"]["Country"]["$"];
					echo '</li>';
				}
				?>
				
				</ul>
			</div>
			
			<!-- Karte 2 -->
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-dark">Headquarters Address</div>
				<ul class="list-group list-group-flush bg-light">
				<?php
				foreach ($json_content as $element) {
					echo '<li class="list-group-item">';
						echo "<small><b>Legal Name</b></small><br>";
						echo $element["Entity"]["LegalName"]["$"];
					echo '</li>';
					# Prüfung, ob OtherEntityName vorhanden ist
					if( isset( $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Other Entity Name"
							. " ("
							. $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["@type"]
							. ")"
							. "</b></small><br>";
							echo $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"];
						echo '</li>';
					}
					# Prüfung, ob MailRouting vorhanden ist
					if( isset( $element["Entity"]["HeadquartersAddress"]["MailRouting"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Mail Routing</b></small><br>";
							echo $element["Entity"]["HeadquartersAddress"]["MailRouting"]["$"];
						echo '</li>';
					}
					echo '<li class="list-group-item">';
						echo "<small><b>First Address Line</b></small><br>";
						echo $element["Entity"]["HeadquartersAddress"]["FirstAddressLine"]["$"];
					echo '</li>';
					# Prüfung, ob AdditionalAddressLine vorhanden ist
					if( isset( $element["Entity"]["HeadquartersAddress"]["AdditionalAddressLine"]["0"]["$"] ) ){
						echo '<li class="list-group-item">';
							echo "<small><b>Additional Address Line</b></small><br>";
							echo $element["Entity"]["HeadquartersAddress"]["AdditionalAddressLine"]["0"]["$"];
						echo '</li>';
					}
					echo '<li class="list-group-item">';
						echo "<small><b>City</b></small><br>";
						if( isset( $element["Entity"]["HeadquartersAddress"]["PostalCode"]["$"] ) ){
							echo $element["Entity"]["HeadquartersAddress"]["PostalCode"]["$"];
							echo " ";
						}
						echo $element["Entity"]["HeadquartersAddress"]["City"]["$"];
					echo '</li>';
					echo '<li class="list-group-item">';
						echo "<small><b>Country</b></small><br>";
						echo $element["Entity"]["HeadquartersAddress"]["Country"]["$"];
					echo '</li>';
				}
				?>
				</ul>
			</div>
			
			<!-- Karte 3 -->
			<?php
			
			
			// Variable als Array deklarieren
			$Adresszeilen = array();

			foreach ($json_content as $element) {
				# Nur Aktive Entity wird ausgegeben. 
				# Inactive Entity wird nicht ausgegeben. 
				if ($element["Entity"]["EntityStatus"]["$"] == "ACTIVE") {
					
					# Entity Name
					# Prüfung, ob OtherEntityName vorhanden ist
					if( isset( $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"] ) && 
					$element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["@type"] != "PREVIOUS_LEGAL_NAME"
					){
						$Adresszeilen[1] = $element["Entity"]["OtherEntityNames"]["OtherEntityName"]["0"]["$"];
					} else {
						$Adresszeilen[1] = $element["Entity"]["LegalName"]["$"];
					}
					# Prüfung, ob MailRouting vorhanden ist
					if( isset( $element["Entity"]["HeadquartersAddress"]["MailRouting"]["$"] ) ){
						$Adresszeilen[] = $element["Entity"]["HeadquartersAddress"]["MailRouting"]["$"];
					}
					
					# First Adress Line 
					$Adresszeilen[] =  $element["Entity"]["HeadquartersAddress"]["FirstAddressLine"]["$"];
					
					# Zusatz-Adressangabe
					# Prüfung, ob AdditionalAddressLine vorhanden ist
					if( isset( $element["Entity"]["HeadquartersAddress"]["AdditionalAddressLine"]["0"]["$"] ) ){
						$Adresszeilen[] = $element["Entity"]["HeadquartersAddress"]["AdditionalAddressLine"]["0"]["$"];
					}
					
					
					# PLZ und Ort
					if( isset( $element["Entity"]["HeadquartersAddress"]["PostalCode"]["$"] ) ){
							$Adresszeilen[] = $element["Entity"]["HeadquartersAddress"]["PostalCode"]["$"] 
							                . " " 
											. strtoupper($element["Entity"]["HeadquartersAddress"]["City"]["$"]);
						} else {
							echo strtoupper($element["Entity"]["HeadquartersAddress"]["City"]["$"]);
						}
				}
			} 
			?>
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-info">Export Adresse <small>(Zeilen 1-6)</small></div>
				<ul class="list-group list-group-flush bg-light">
					<?php
					# Alle Array Werte ausgeben
					foreach ($Adresszeilen as $zeile => $inhalt) {
						echo '<li class="list-group-item">';
							echo "<small><b>Adresszeile " . $zeile . "</b></small><br>";
							echo $inhalt . "<br>";
						echo '</li>';
					}
					?>
				</ul>
			</div>
		</div>
		<br>
		<!-- Button -->
		<a class="btn btn-dark font-Bitter disabled" href="#" role="button">CSV Export</a>
	
		<!-- Button -->
		<a class="btn btn-dark font-Bitter disabled" href="#" role="button">Serienbrief Export</a>
  
		<!-- Abstand -->
		<div class="border-top my-3"></div>
  
		<!-- Link -->
		<nav class="navbar justify-content-end navbar-light bg-light font-Bitter">
			
			<a target="_blank" class="navbar-brand" href="https://github.com/DamianMalik/LEI">GitHub
				<img src="https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png" width="30" height="30" class="d-inline-block align-top" alt="">
			</a>
			
		</nav>
		
	</div>
</body>
</html>
