<!DOCTYPE html>
<html lang="de">
<head>
	<!-- Required meta tags -->
	<title>LEI Abruf</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">  
	
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	
  <link rel="stylesheet" href="style.css">
	
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    
    
    <!-- Google Fonts -->
    <!-- Font Type: Patrick Hand -->
    <link href="https://fonts.googleapis.com/css?family=Bitter" rel="stylesheet"> 
    
</head>
<body class="bg-white">
	
	<!--Container-->	
	<div class="container">
	
		<!--Navbar-->	
		<nav class="navbar navbar-expand-md bg-dark navbar-dark">
			<form class="form-inline col-11" action="/action_page.php">
				<input class="form-control mr-sm-2 font-Bitter" type="text" placeholder="LEI Suche">
				<button class="btn btn-outline-light font-Bitter" type="submit">Suche</button>
			</form>
		</nav> 
	
		<!-- h1 with padding-top 3 -->
		<p class="h1 pt-3 pb-3 font-Bitter">Adresse von LEI 988400T354OS9KEUI974</p>
		
		<!-- Abstand -->
		<div class="border-top my-3"></div>
		
		<!-- URL-Link und JSON Inhalt -->
		<p class="font-weight-bold font-Bitter">Abruf URL:</p>
		<p class="font-Bitter">https://....</p>
		
		<button type="button" class="btn btn-outline-dark btn-sm font-Bitter" data-toggle="collapse" data-target="#demo">zeige JSON...</button>
		<div id="demo" class="pt-3 small collapse font-Bitter">
			[{"LEI":{"$":"549300JNXF87XCUMN685"},"Entity":{"LegalName":{"$":"CBC Pension Board of Trustees"},"LegalAddress":{"FirstAddressLine":{"$":"99 Bank Street"},"City":{"$":"Ottawa"},"Region":{"$":"CA-ON"},"Country":{"$":"CA"}},"HeadquartersAddress":{"FirstAddressLine":{"$":"99 Bank Street"},"City":{"$":"Ottawa"},"Region":{"$":"CA-ON"},"Country":{"$":"CA"}},"RegistrationAuthority":{"RegistrationAuthorityID":{"$":"RA999999"}},"LegalJurisdiction":{"$":"CA-ON"},"LegalForm":{"EntityLegalFormCode":{"$":"8888"},"OtherLegalForm":{"$":"Other"}},"EntityStatus":{"$":"ACTIVE"}},"Registration":{"InitialRegistrationDate":{"$":"2014-03-20T01:30:57.914Z"},"LastUpdateDate":{"$":"2019-09-12T19:19:20.451Z"},"RegistrationStatus":{"$":"ISSUED"},"NextRenewalDate":{"$":"2020-09-18T12:10:21.358Z"},"ManagingLOU":{"$":"5493001KJTIIGC8Y1R12"},"ValidationSources":{"$":"ENTITY_SUPPLIED_ONLY"},"ValidationAuthority":{"ValidationAuthorityID":{"$":"RA999999"}}}}]
		</div>
		
		<!-- Abstand -->
		<div class="border-top my-3"></div>
  
		
		<!-- Karten -->
		<div class="card-deck font-Bitter">
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-dark">Legal Address</div>
				<ul class="list-group list-group-flush bg-light">
					<li class="list-group-item">
						<address>
							Example 1 Inc.<br>
							1234 Example Street<br>
							Antartica, Example 0987<br>
							(123) 456-7890
						</address>
					</li>
				</ul>
			</div>
			
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-dark">Headquarters Address</div>
				<ul class="list-group list-group-flush bg-light">
					<li class="list-group-item">
						<address>
							Example 1 Inc.<br>
							1234 Example Street<br>
							Antartica, Example 0987<br>
							(123) 456-7890
						</address>
					</li>
				</ul>
			</div>
			
			<div class="card" style="width: 18rem;">
				<div class="card-header text-white bg-info">Export Adresse <small>(Zeilen 1-6)</small></div>
				<ul class="list-group list-group-flush bg-light">
					<li class="list-group-item">
						Example 1 Inc.
					</li>
					<li class="list-group-item">
						1234 Example Street
					</li>
					<li class="list-group-item">
						Antartica, Example 0987
					</li>
					<li class="list-group-item">
						(123) 456-7890
					</li>
					<li class="list-group-item">
						x
					</li>
					<li class="list-group-item">
						x
					</li>
				</ul>
			</div>
		</div>
	
		<!-- Abstand -->
		<div class="border-top my-3"></div>
	
		<!-- Button -->
		<a class="btn btn-dark font-Bitter" href="#" role="button">CSV Export</a>
	
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
