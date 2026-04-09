# Formularsystem 2.0

## Zielsetzung

Das bestehende Formularsystem soll zu einer stabilen, sicheren und Docker-faehigen Version 2.0 weiterentwickelt werden. Version 2.0 ist nicht nur ein Formular-Submitter, sondern eine vollwertige Formular-, Vorgangs- und Bearbeitungsplattform mit Benutzerverwaltung, Rollen und Rechten, Designer, Antworten, Archiv, Statistiken, Plugins und API.

## Technologieempfehlung

### Empfohlener Stack

- Backend: Laravel
- Datenbank: PostgreSQL
- Queue und Hintergrundjobs: Laravel Queues
- API: OpenAPI-basierte REST-API
- Frontend fuer Admin, Designer und Dashboards: React oder Vue
- Betrieb: Docker Compose fuer Entwicklung, containerfaehige Produktionsumgebung
- Dateiablage: lokales Volume oder S3-kompatibler Storage

### Warum Laravel

- Bestehendes System ist bereits in PHP umgesetzt.
- Laravel bringt Authentifizierung, Autorisierung, Validation, Mail, Storage, Encryption, Queues und Events bereits in tragfaehiger Form mit.
- Rollen, Rechte, sensible Daten, asynchrone Verarbeitung und Adminfunktionen lassen sich damit sehr gut umsetzen.
- Docker-Betrieb ist mit Laravel sehr gut beherrschbar.
- Die technische Huerde fuer die Migration aus dem aktuellen PHP-System ist deutlich geringer als bei einem Wechsel auf .NET oder eine komplett andere Plattform.

### Grundsatz

- Version 2.0 soll fachliche Logik aus Version 1 uebernehmen, aber technisch neu strukturiert werden.
- Es handelt sich nicht nur um Refactoring, sondern um Neuarchitektur plus Migration.
- Version 2.0 soll parallel zu Version 1 aufgebaut werden.

## Leitprinzipien

- Stabilitaet vor Schnelligkeit
- Sicherheit und DSGVO von Anfang an
- Klare Trennung zwischen Runtime, Adminbereich, Designer und API
- Alle Berechtigungen serverseitig durchsetzen
- Formulare und Antworten als echte Vorgaenge modellieren
- Erweiterbarkeit ueber Plugins
- Produktives Branding nicht im Repository speichern
- Schwere Prozesse asynchron verarbeiten

## Hauptfunktionen

### 1. Benutzerverwaltung

- Lokale Benutzerkonten
- Gastkonten
- Anmeldung ueber lokales Active Directory
- Anmeldung ueber Microsoft 365 / Microsoft Entra ID
- Mehrere Identitaetsquellen parallel
- Benutzerprofil mit Name, Anzeigename, E-Mail, Login, Status, Rollen, Gruppen, optional organisatorischen Attributen

### 2. Gruppen, Rollen und Rechte

- Lokale Gruppen
- Synchronisierte Gruppen aus AD oder Entra ID
- Rollenmodell mit mehrfacher Rollenzuweisung pro Benutzer
- Rechte auf Systemebene
- Rechte auf Formularebene
- Rechte auf Submission-Ebene
- Rechte benutzerbasiert und gruppenbasiert

### 3. Formularverwaltung

- Formulare erstellen
- Formulare bearbeiten
- Formulare versionieren
- Formulare deaktivieren
- Formulare archivieren
- Formulare als Entwurf oder produktiv kennzeichnen
- Formulare als sensibel markieren
- Formulare bestimmten Rollen, Gruppen oder Benutzern zuweisen

### 4. Webbasierter Formulardesigner

- Browserbasierter Designer
- Keine manuelle PHP-Bearbeitung fuer neue Formulare
- JSON-basierte Formdefinition
- Vorschau und Testmodus
- Versionsvergleich
- Wiederverwendbare Feldbausteine
- Wiederverwendbare Subformulare

### 5. Formularlogik

- If-Then-Else-Regeln
- Sichtbarkeitsregeln
- Pflichtfelder abhaengig von Antworten
- Regelbasierte Einbindung von Zusatzfeldern
- Regelbasierte Einbindung von Subformularen
- Regelbasierte Antwortkombinationen
- Bedingte Validierung
- Mehrstufige Formulare

### 6. Feldtypen und Inhalte

- Text
- Zahl
- Datum
- Uhrzeit
- E-Mail
- Telefonnummer
- Checkbox
- Radio
- Auswahl und Mehrfachauswahl
- Datei-Upload
- Textbereich
- Versteckte Felder
- Medienfelder wie Bild, Audio und Video
- Berechnete oder abgeleitete Felder
- Plugin-basierte Spezialfelder

### 7. Validierung

- Umfangreiche serverseitige Validierung
- Umfangreiche clientseitige Validierung
- Pflichtfelder
- Formatpruefungen
- Wertebereiche
- Abhaengige Validierungen
- Validierungsregeln pro Feld und formularweit
- Plugin-Validierungen

### 8. Submissionen und Vorgangsmodell

- Jede Submission bekommt eine eindeutige Meldungsnummer
- Submissionen werden als Vorgaenge gespeichert
- Submissionen besitzen Status
- Submissionen koennen einem Bearbeiter oder Team zugeordnet werden
- Submissionen koennen Antworten und Kommentare erhalten
- Submissionen koennen korrigiert werden
- Submissionen koennen archiviert werden

### 9. Eigene Submissionen bearbeiten

- Benutzer duerfen ihre eigenen Submissionen einsehen
- Benutzer duerfen ihre eigenen Submissionen aendern, sofern das Formular dies erlaubt
- Jede Aenderung erzeugt eine neue nachvollziehbare Version
- Jede Aenderung wird in einem Audit-Log gespeichert
- Falls direkte Bearbeitung nicht erlaubt ist, existiert ein Korrekturprozess

### 10. Antworten und verknuepfte Formulare

- Antworten auf bestehende Submissionen
- Kommentare zu Vorgangen
- Verknuepfte Antwortformulare
- Beispiel: Auf eine Besucheranmeldung folgt eine Erledigungsmeldung
- Antwortverlauf mit Zeitstempel, Benutzer, Status und Anhaengen
- Ticketartige Darstellung des Vorgangsverlaufs

### 11. Sichtbarkeit und Berechtigungen fuer Submissionen

- Benutzer sehen eigene Submissionen
- Empfaenger sehen relevante Submissionen
- Teams sehen zugewiesene Submissionen
- Administratoren sehen alle freigegebenen Bereiche
- Sensible Felder koennen maskiert oder eingeschraenkt sichtbar sein

### 12. Archiv

- Archiv fuer Formulare
- Archiv fuer Submissionen
- Trennung zwischen aktivem Bereich und Archiv
- Aufbewahrungsfristen
- Loeschfristen
- DSGVO-konforme Nachvollziehbarkeit

### 13. E-Mail und Kommunikation

- Weiterhin Versand von E-Mails
- PDF-Anhang
- Meldungsnummer in Betreff und Inhalt
- Konfigurierbarer Empfaengerkreis
- Optionale Bestaetigung an Absender
- Beibehaltung bestehender Mail-Schnittstellen

### 14. Antworten per E-Mail

- In Version 2.0 vorbereiten, aber sinnvollerweise nicht im ersten MVP voll umsetzen
- Ziel: Eingehende E-Mails einer Meldungsnummer zuordnen
- Ziel: Inhalte und gegebenenfalls Anhaenge als Antwort in den Vorgang uebernehmen
- Ziel: Ticketaehnliche Kommunikation auf Mailbasis

### 15. PDF, Webhooks und Hintergrundjobs

- PDF-Erzeugung fuer Submissionen
- E-Mail-Versand
- URL-Callbacks
- Webhooks
- Asynchrone Ausfuehrung ueber Queue-Worker
- Benutzer soll nach dem Speichern schnell Rueckmeldung erhalten

### 16. Statistik und Dashboards

- Persoenliches Dashboard fuer Benutzer
- Statistiken pro Formular
- Statistiken pro Bereich
- Systemweite Statistiken fuer Admins
- Kennzahlen wie Anzahl, Status, Bearbeitungszeit, Korrekturquote, Antwortquote

### 17. Kiosk-Mode

- Echter Kiosk-Mode fuer einzelne Geraete
- Ein Geraet darf nur definierte Formulare sehen
- Keine Navigation zu anderen Formularen oder in den Adminbereich
- Ruecksetzung nach erfolgreicher Abgabe
- Geraetebezogene Konfiguration
- Vollbild- oder terminalartige Oberflaeche fuer Eingangsbereiche

### 18. Datenschutz und Sicherheit

- Formulare als sensibel markierbar
- Sensible Submissionen verschluesselt speichern
- Entschluesselung nur fuer berechtigte Benutzer
- Zugriff auf sensible Daten protokollieren
- Audit-Log fuer sicherheitsrelevante Aktionen
- DSGVO-relevante Anforderungen beruecksichtigen
- Kein Speichern produktiver Geheimnisse oder Kunden-CI im Repository

## Plugin-System

### Ziel

Das System muss ueber ein echtes Plugin-System fachlich und technisch erweiterbar sein.

### Anforderungen

- Plugins installierbar
- Plugins aktivierbar und deaktivierbar
- Plugins versionierbar
- Plugins konfigurierbar
- Plugins formularbezogen aktivierbar
- Plugins mit eigener Berechtigungslogik
- Plugins mit serverseitigen und optional clientseitigen Komponenten
- Plugin-Hooks fuer Rendern, Validieren, Speichern, PDF, Mail, Antwortverarbeitung, Export und API
- Plugin-Aktivitaeten im Audit-Log sichtbar

## Plugin-Beispiele

### Ausweisscanner-Plugin

- Scan von Personalausweisen und Reisepaessen
- Extraktion definierter Daten in Formularfelder
- Uebernahme selektierbarer Daten wie Name, Geburtsdatum, Dokumentennummer, Nationalitaet, Dokumententyp, Gueltigkeit
- Speicherung von Validierungsstatus
- Speicherung, ob Echtheitspruefung durchgefuehrt wurde
- Speicherung des Ergebnisses der Echtheitspruefung
- Kein persistentes Speichern des eigentlichen Ausweisscans
- Nur temporaere Verarbeitung, falls technisch notwendig
- DSGVO-konformer Betrieb

### Visitenkartenscanner-Plugin

- Scan von Visitenkarten
- OCR-Extraktion von Name, Firma, Position, E-Mail, Telefon, Mobilnummer, Website, Adresse
- Uebernahme in Formularfelder
- Speicherung der extrahierten Daten
- Speicherung des Scans erlaubt, wenn das Formular dies vorsieht

## Design, Theming und Branding

- Vollstaendig themingfaehig
- Theme fuer Benutzeroberflaeche, PDF, Login, Kiosk und E-Mails
- Demo-Assets im Repository
- Produktive Kunden-CI ausserhalb des Repositories
- Produktive Logos, Farben, Schriften und PDF-Assets ueber Uploads, Volumes oder lokale Konfiguration
- Fallback-Theme mit neutralen Demo-Farben und Demo-Logo

## Adminbereich

- Benutzerverwaltung
- Gruppenverwaltung
- Rollenverwaltung
- Rechteverwaltung
- AD- und Entra-Anbindung
- Formularverwaltung
- Submission-Verwaltung
- Antwortverwaltung
- Archivverwaltung
- Pluginverwaltung
- Schnittstellenverwaltung fuer SMTP, URL-Callbacks und Webhooks
- Theme- und Branding-Verwaltung
- Statistik und Systemmonitoring

## Schnittstellen

- SMTP / Mailserver
- URL-Aufruf / Callback
- Webhook
- OpenAPI REST API
- Plugin-API
- LDAP / LDAPS
- OpenID Connect / Entra ID

## OpenAPI-Anforderungen

- API fuer Authentifizierung
- API fuer Benutzer
- API fuer Gruppen
- API fuer Rollen und Rechte
- API fuer Formulare
- API fuer Formulardefinitionen
- API fuer Submissionen
- API fuer Antworten
- API fuer Archiv
- API fuer Statistiken
- API fuer Pluginverwaltung
- API fuer Adminkonfiguration

## Nicht-funktionale Anforderungen

### Stabilitaet

- Produktionsreifer Betrieb in Docker
- Klare Containertrennung
- Fehlerrobuste Hintergrundverarbeitung
- Sichere Migrationen

### Sicherheit

- Sichere Authentifizierung
- Serverseitige Rechtepruefung
- Verschluesselung sensibler Inhalte
- Audit-Logging
- Schutz vor unautorisierten Zugriffen

### Performance

- Schnelles Speichern von Formularen
- Asynchrone Verarbeitung schwerer Prozesse
- Gute Such- und Filterbarkeit
- Skalierbarer API- und Queue-Betrieb

### Wartbarkeit

- Modulorientierte Architektur
- Tests fuer Kernlogik und Sicherheitslogik
- Dokumentation fuer Betrieb und Entwicklung
- Klare Migrationsstrategie

## Update- und Migrationsstrategie

### Empfehlung

- Version 1 stabil halten
- Version 2 in separatem V2-Branch starten
- Mittelfristig fuer die produktive 2.0 ein eigenes Repository einplanen

### Begruendung

- Version 2 ist eine Neuarchitektur, kein kleines Refactoring
- Das Risiko fuer laufende Prozesse in Version 1 soll gering bleiben
- Bugfixes in 1.x und Neubau in 2.0 muessen sauber trennbar bleiben

### Praktischer Vorschlag

- `main` oder `master` bleibt fuer 1.x
- eigener V2-Branch fuer Architektur und Aufbau
- spaeter optional neues Repo wie `formular-system-v2`
- Pilotformulare zuerst in 2.0 nachbauen
- Danach schrittweise Migration weiterer Formulare

## Empfohlene Umsetzungsphasen

- Phase 1: Architektur, Docker, PostgreSQL, Auth, Rollen, Rechte, Grunddatenmodell
- Phase 2: Formularverwaltung, Designer, Runtime, Submissionen, API
- Phase 3: AD und Entra, Gruppen, sensible Formulare, Verschluesselung
- Phase 4: Antworten, Korrekturen, Archiv, Statistiken, Kiosk-Mode
- Phase 5: Plugin-System, Scanner-Plugins, Inbound-E-Mail, erweiterte Workflows
