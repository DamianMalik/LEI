![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/DamianMalik/LEI)


# LEI-Adresse für Adressetikett

Identifiziert aus einer beliebigen LEI-Adresse die relevanten Datenfelder für ein Adressetikett. 

Dieses Skript zeigt für einen beliebigen **Legal Entity Identifier (LEI)** die Adressangaben aus der `Legal Address` und der `Headquarters Address` übersichtlich nebeneinander an. Zudem werden aus der `Headquarters Address` die relevanten Adressangaben für ein Adressetikett ausgewählt und zum Vergleich dargestellt. Hierbei  werden die Adressfelder  wie folgt aufbereitet: 
* Berücksichtigung von Überlängen in den Feldern `LegalName`, `MailRouting`, `FirstAddressLine` und `AdditionalAddressLine`: Adresselemente mit mehr als 49 Zeichen werden auf nächste Adresszeile umbrochen.  
* Nichtberücksichtigung eines inaktiven `EntityStatus`: Inaktive LEI werden nicht weiterverarbeitet. 
* Es wird für den `Country`-ISO-Code die Langbezeichnung des Ländernamens hinzugefügt. 

Die aufbereiteten Adressfelder werden in eine CSV-Datei exportiert. Darüberhinaus wird die  Adresse in einen Serienbrief eingefügt (Beispiel RTF-Datei). Beide Dokumente können einfach über die Weboberfläche abgerufen werden. 

![Beispiel Darstellung:](https://raw.githubusercontent.com/DamianMalik/LEI/master/LEI-Screenshot.png)

# Installation, Anforderungen und Abhängigkeiten

## Abhängigkeiten
Das Skript nutzt die folgenden Services: 
+ Bootstrap 4 
+ Google Fonts 
+ GLEIF LEI Look-up API v2.0.1
+ Restcountries.com API v3.1

## Systemvoraussetzungen
+ PHP > 7.0 

## Installation
+ Download 
+ ggf. Entpacken
+ auf Webserver hochladen


