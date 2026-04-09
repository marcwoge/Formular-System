# Formularsystem 2.0

## Zielbild
Formularsystem 2.0 ist eine produktionsreife, Docker-basierte Formular- und Vorgangsplattform fuer interne, externe und sensible Prozesse. Das System erweitert das bestehende Formularsystem um Identitaetsmanagement, Rollen und Rechte, Gruppensteuerung, Web-Designer, Vorgangsverlauf, Antworten, Archiv, API, Statistiken, Kiosk-Mode und verschluesselte Datenspeicherung.

Das System soll nicht nur Formulare absenden, sondern komplette Vorgaenge verwalten koennen. Eine Formularabsendung wird dabei als Vorgang oder Ticket betrachtet, kann Antworten erhalten, korrigiert werden, archiviert werden und revisionssicher nachvollziehbar bleiben.

## Ausgangsbasis
Version 2.0 basiert auf dem bestehenden Formularsystem und muss dessen bewaehrte Funktionen uebernehmen:

- Formularanzeige im Browser
- Formularvalidierung
- PDF-Erzeugung
- E-Mail-Versand
- Webhook- oder URL-Callback
- flexible Formularfelder
- Medien in Formularen wie Text, Bild, Audio und Video
- Formularnavigation und Einbettung

## Kernziele von Version 2.0

- produktionsreife Mehrbenutzerplattform statt reinem Formular-Submitter
- echte Benutzerverwaltung mit Rollen, Rechten und Gruppen
- Integration lokaler Benutzer, Gaeste, lokales Active Directory und Microsoft 365 / Microsoft Entra ID
- Formular-Designer im Browser ohne manuelle PHP-Bearbeitung
- formular- und vorgangsbezogene Berechtigungen
- aenderbare eigene Einreichungen mit Audit-Log
- Antworten auf bestehende Vorgaege mit verknuepften Antwortformularen
- Kiosk-Mode fuer einzelne Endgeraete mit hart begrenztem Zugriff
- DSGVO-konforme Speicherung sensibler Daten mit Verschluesselung
- Admin-Portal fuer Benutzer, Formulare, Rollen, Schnittstellen und Statistiken
- OpenAPI fuer alle Kernfunktionen
- Docker-Betrieb fuer Entwicklung und Produktion

## Fachliche Funktionsbeschreibung

### 1. Benutzer und Identitaeten

- Das System muss lokale Benutzerkonten unterstuetzen.
- Das System muss Gastkonten unterstuetzen.
- Das System muss Benutzer ueber lokales Active Directory authentifizieren koennen.
- Das System muss Benutzer ueber Microsoft 365 / Microsoft Entra ID per OpenID Connect oder SAML authentifizieren koennen.
- Das System muss mehrere Identitaetsquellen parallel unterstuetzen.
- Ein Benutzer kann einer oder mehreren Gruppen zugeordnet sein.
- Gruppen koennen lokal gepflegt oder aus AD / Entra synchronisiert werden.
- Benutzerprofile sollen Name, Anzeigename, E-Mail-Adresse, Loginname, organisatorische Zuordnung, Rollen, Gruppen und Status enthalten.
- Optional sollen Benutzerattribute aus dem Verzeichnis uebernommen werden koennen, etwa Abteilung, Standort oder Vorgesetzter.

### 2. Rollen und Rechte

- Das System braucht ein feingranulares Rollen- und Rechtesystem.
- Rechte muessen auf Systemebene, Formularebene und Submission-Ebene vergeben werden koennen.
- Rollen muessen lokal pflegbar sein.
- Rollen koennen mit Gruppen verknuepft werden.
- Ein Benutzer kann mehrere Rollen besitzen.
- Beispiele fuer Rollen sind Superadmin, Formularadmin, Fachadmin, Bearbeiter, Auditor, Kiosk-Benutzer, normaler Benutzer und Gast.
- Beispiele fuer Rechte sind Formular sehen, Formular absenden, Formular bearbeiten, Formular veroeffentlichen, Submission lesen, Submission beantworten, Submission korrigieren, Submission exportieren, Submission archivieren, Benutzer verwalten, Gruppen zuweisen, Schnittstellen konfigurieren und Statistiken sehen.
- Rechte sollen sowohl benutzerbasiert als auch gruppenbasiert vergebbar sein.

### 3. Formularverwaltung

- Formulare muessen im Adminbereich erstellt, bearbeitet, versioniert, deaktiviert und archiviert werden koennen.
- Ein Formular besitzt Metadaten wie Titel, Beschreibung, Kategorie, Verantwortliche, Empfaenger, Sichtbarkeit, Status, Sensitivitaet und Version.
- Ein Formular kann als aktiv, deaktiviert, archiviert, Entwurf oder nur intern markiert werden.
- Formulare muessen mit Berechtigungen versehen werden koennen.
- Es muss moeglich sein, Formulare nur fuer bestimmte Benutzer, Gruppen oder Rollen sichtbar zu machen.
- Es muss moeglich sein, Formulare nur in bestimmten Kontexten anzuzeigen, etwa intern, extern, Gast oder Kiosk.

### 4. Webbasierter Formulardesigner

- Der Designer muss browserbasiert sein.
- Der Designer muss Formulare ohne Codebearbeitung erstellen koennen.
- Es muss einen Drag-and-Drop- oder komponentenbasierten Aufbau geben.
- Felder muessen frei konfigurierbar sein.
- Der Designer muss Feldtypen wie Text, Zahl, Datum, Uhrzeit, E-Mail, Telefon, Auswahl, Mehrfachauswahl, Checkbox, Radio, Datei, Textbereich, Adresse, Medien, versteckte Felder, berechnete Felder und Referenzfelder beherrschen.
- Der Designer muss Bedingungen unterstuetzen.
- Bedingungen muessen If-Then-Else, Sichtbarkeit, Pflichtfeldlogik, Werteabhaengigkeiten, Schrittabfolge und Einbindung von Teilformularen oder Subformularen abbilden koennen.
- Subformulare muessen nur dann eingeblendet werden, wenn definierte Antwortkombinationen eintreten.
- Validierungsregeln muessen pro Feld und formularweit konfigurierbar sein.
- Es muss Vorschau, Testmodus und Versionsvergleich geben.

### 5. Formulare absenden, bearbeiten und korrigieren

- Jede Einreichung erhaelt eine eindeutige Meldungsnummer.
- Eine Submission wird als Vorgang gespeichert.
- Benutzer sollen ihre eigenen Submissions einsehen koennen.
- Benutzer sollen ihre eigenen Submissions aendern und erneut speichern koennen, sofern das Formular dies erlaubt.
- Jede Aenderung an einer Submission muss in einem Audit-Log gespeichert werden.
- Falls direkte Bearbeitung fachlich nicht erlaubt ist, muss ein Korrekturablauf verfuegbar sein.
- Der Korrekturablauf muss die originale Submission referenzieren und den Unterschied nachvollziehbar speichern.
- Administratoren und berechtigte Bearbeiter muessen alte und neue Versionen vergleichen koennen.

### 6. Antworten und Vorgangsverlauf

- Zu einer Submission muessen Antworten erzeugt werden koennen.
- Antworten koennen freie Kommentare oder strukturierte Antwortformulare sein.
- Ein Antwortformular muss mit einem Ursprungsformular in Beziehung gesetzt werden koennen.
- Beispiel: Auf eine Besucheranmeldung kann spaeter eine Erledigungsmeldung oder Rueckmeldung erfolgen.
- Ein Vorgang braucht einen Verlauf mit Statuswechseln, Antworten, Kommentaren, AnhÃ¤ngen, Bearbeitern und Zeitstempeln.
- Die Darstellung soll sich an einem Ticketsystem orientieren, aber formularbasiert bleiben.

### 7. E-Mail-Kommunikation

- Das System muss weiterhin E-Mails mit PDF und Metadaten versenden koennen.
- Die Meldungsnummer muss in Betreff und Inhalt auftauchen.
- Antworten per E-Mail sollen perspektivisch verarbeitet werden koennen.
- Fuer die Verarbeitung eingehender E-Mails wird eine spaetere Ausbaustufe empfohlen.
- Inbound-Mail soll die Meldungsnummer erkennen und die E-Mail dem passenden Vorgang zuordnen.
- AnhÃ¤nge aus eingehenden E-Mails sollen optional uebernommen werden koennen.

### 8. Submission-Sichtbarkeit

- Empfaenger eines Formulars muessen zugeordnete Submissions sehen koennen.
- Sichtbarkeit muss pro Formular, Rolle, Gruppe, Bearbeiterteam oder konkretem Empfaenger konfigurierbar sein.
- Es muss moeglich sein, dass ein Benutzer nur eigene Formulare sieht.
- Es muss moeglich sein, dass ein Team alle Formulare eines Bereichs sieht.
- Es muss moeglich sein, sensible Felder nur teilweise sichtbar zu machen.

### 9. Archiv

- Formulare muessen archiviert werden koennen.
- Submissions muessen archiviert werden koennen.
- Archivierte Daten muessen auffindbar, aber getrennt von aktiven Vorgaengen sein.
- Archivfristen und Loeschfristen muessen definierbar sein.
- Das System soll fuer sensible Formulare Aufbewahrungs- und Loeschkonzepte unterstuetzen.

### 10. Statistik und Reporting

- Jeder eingeloggte Benutzer soll ein eigenes Dashboard sehen koennen.
- Das persoenliche Dashboard soll eigene Formulare, eigene Submissions, offene Vorgaege, bearbeitete Vorgaege und relevante Kennzahlen zeigen.
- Formularverantwortliche sollen Statistiken pro Formular sehen koennen.
- Administratoren sollen Systemstatistiken sehen koennen.
- Beispiele sind Anzahl Einreichungen, Bearbeitungszeiten, Statusverteilung, Korrekturquote, Antwortquote und Archivquote.
- Exportfunktionen fuer CSV oder Excel sind sinnvoll.

### 11. Kiosk-Mode

- Es muss einen echten Kiosk-Mode geben.
- Ein Kiosk-Geraet darf nur auf ein oder wenige explizit freigegebene Formulare zugreifen.
- Das Geraet soll nach Moeglichkeit geraetebezogen registriert werden.
- Eine Registrierung ueber Geraete-ID, Zertifikat, Token oder festen Zugang ist empfehlenswert.
- Im Kiosk-Mode sollen Navigation, Adminbereiche, Suche und andere Formulare ausgeblendet sein.
- Der Kiosk-Mode soll nach Absenden automatisch wieder in den Startzustand zurueckspringen.
- Sitzungen im Kiosk-Mode sollen kurz leben und keine fremden Daten persistent hinterlassen.
- Fuer Besucherbereiche ist eine Vollbild- oder App-artige Oberflaeche sinnvoll.

### 12. Datenschutz und Sicherheit

- Formulare muessen als sensibel markierbar sein.
- Sensible Formulare muessen verschluesselte Ablage unterstuetzen.
- Sensible Daten sollen auf Anwendungsebene verschluesselt werden, nicht nur auf Datentraeger- oder Datenbankebene.
- Nur berechtigte Benutzer duerfen sensible Inhalte entschluesseln und sehen.
- Rechtepruefung muss serverseitig erfolgen.
- Das System muss Audit-Logs fuer sicherheitsrelevante Aktionen fuehren.
- Das System soll DSGVO-relevante Funktionen unterstuetzen, etwa Auskunft, Loeschung, Sperrung, Maskierung, Aufbewahrung und Nachvollziehbarkeit.
- Zugriffe auf sensible Formulare und sensible Submissions muessen protokolliert werden.

### 13. Schnittstellen

- Bestehende Schnittstellen muessen erhalten bleiben.
- E-Mail-Server muss weiter unterstuetzt werden.
- URL-Aufrufe oder Server-Callbacks muessen weiter moeglich sein.
- Webhooks muessen konfigurierbar sein.
- Schnittstellen muessen im Adminbereich verwaltet werden koennen.
- Es soll eine vollstaendige OpenAPI-basierte REST-API geben.
- Die API muss Authentifizierung, Benutzer, Gruppen, Rollen, Formulare, Submissionen, Antworten, Archive, Statistiken und Adminfunktionen abdecken.

### 14. Plugin-System
- Das System muss ein echtes Plugin-System besitzen.
- Plugins muessen installierbar, aktivierbar, deaktivierbar und versionierbar sein.
- Plugins muessen serverseitige und optional clientseitige Erweiterungen einbringen koennen.
- Plugins muessen ueber klar definierte Schnittstellen mit Formularen, Submissions, Antworten, Dateien, Events und Berechtigungen arbeiten.
- Plugins muessen im Adminbereich verwaltet werden koennen.
- Plugins muessen eigene Konfigurationen besitzen koennen.
- Plugins muessen formularbezogen aktivierbar sein.
- Plugins muessen definieren koennen, welche Felder sie lesen, schreiben oder validieren duerfen.
- Plugins muessen eigene Berechtigungen mitbringen koennen.
- Plugins muessen in Audit-Logs nachvollziehbar sein.
- Plugins duerfen sensible Daten nur im Rahmen der Formular- und Berechtigungsregeln verarbeiten.
- Das System braucht eine Plugin-API fuer Ereignisse wie Formular rendern, Formular validieren, Submission speichern, PDF erzeugen, Mail versenden, Antwort verarbeiten und Daten exportieren.
### 15. Plugin-Beispiel Ausweisscanner
- Es muss moeglich sein, ein Plugin fuer Ausweisscanner anzubinden.
- Das Plugin soll Personalausweise und Reisepaesse erfassen koennen.
- Das Plugin soll erkannte Daten strukturiert an Formularfelder uebergeben koennen.
- Auswahlbare Zielwerte sind zum Beispiel Vorname, Nachname, Geburtsdatum, Ausweisnummer, Dokumententyp, ausstellender Staat, Gueltigkeit und Nationalitaet.
- Das Plugin muss speichern koennen, ob eine Dokumentenvalidierung moeglich war.
- Das Plugin muss speichern koennen, ob eine Echtheitspruefung durchgefuehrt wurde.
- Das Plugin muss speichern koennen, ob die Echtheitspruefung erfolgreich, fehlgeschlagen oder nicht verfuegbar war.
- Das Plugin darf das eigentliche Ausweisdokument oder dessen vollstaendige Bilddatei nicht persistieren.
- Falls technisch notwendig, duerfen nur fluechtige Verarbeitungsdaten im Speicher oder in kurzlebigen Temporaerdaten verwendet werden.
- Das Plugin muss DSGVO-konform betrieben werden koennen.
- Das Plugin muss pro Formular aktivierbar sein.
- Das Plugin muss definieren koennen, welche erfassten Werte uebernommen werden sollen.
### 16. Plugin-Beispiel Visitenkartenscanner
- Es muss moeglich sein, ein Plugin fuer Visitenkartenscanner anzubinden.
- Das Plugin soll erkannte Daten wie Name, Firma, Position, E-Mail-Adresse, Telefon, Mobilnummer, Adresse und Website extrahieren koennen.
- Das Plugin soll die erkannten Daten in Formularfelder uebergeben koennen.
- Im Unterschied zum Ausweisscanner darf bei diesem Plugin sowohl das OCR-Ergebnis als auch der Scan der Visitenkarte gespeichert werden, sofern das Formular dies erlaubt.
- Das Plugin muss definieren koennen, welche Daten uebernommen, gespeichert oder verworfen werden.
- Das Plugin muss Validierungen fuer erkannte E-Mail-Adressen und Rufnummern anstossen koennen.
### 17. Design, Theming und Branding
- Das System muss vollstaendig themingfaehig sein.
- Farben, Logos, Schriften, Icons, Login-Seiten, Kiosk-Ansichten, PDF-Kopf und PDF-Footer muessen pro Umgebung oder Mandant anpassbar sein.
- Produktive CI-Assets wie NTC-Logos, Schriftarten oder echte Corporate-Design-Dateien duerfen nicht im Repository liegen.
- Stattdessen soll das Repository nur Demo-Assets und neutrale Beispiel-Themes enthalten.
- Echte Branding-Dateien sollen ueber lokale Konfigurationsverzeichnisse, ein Storage-Volume oder Admin-Uploads eingebunden werden.
- Das System soll ein Fallback-Theme mit Demo-Logo und Demo-Farben besitzen.
- Exportierte PDFs, E-Mails und Kiosk-Seiten muessen das aktive Theme verwenden koennen.

## Empfohlene Architektur fuer Version 2.0

### Backend

- Empfehlung: Laravel oder Symfony als robustes PHP-Backend mit klarer Benutzer-, Policy- und API-Struktur.
- Laravel ist fuer dieses Projekt besonders attraktiv, weil Auth, Queues, Policies, Events, Scheduler, Mail, Encryption und API-Ressourcen sehr gut abgedeckt sind.
- Die bestehende PHP-Basis kann fachlich migriert werden, statt alles neu in einer anderen Sprache zu bauen.

### Frontend

- Empfehlung: React oder Vue fuer Adminbereich, Dashboard und Formulardesigner.
- Der Endbenutzer-Renderer kann spaeter aus einer JSON-Formdefinition arbeiten.
- Der Designer sollte dieselbe Formdefinition nutzen wie das Runtime-Rendering.
- Das Frontend braucht ein Theme-System mit Demo-Assets im Repository und produktiven CI-Assets ausserhalb des Repositories.

### Datenbank

- Empfehlung: PostgreSQL als Hauptdatenbank.
- Submissions, Versionen, Antworten, Benutzer, Gruppen, Rollen, Rechte, Formulare, Formulardefinitionen und Audit-Logs gehoeren in relationale Tabellen.
- Dateiablage und Anhaenge koennen lokal oder in S3-kompatiblem Storage liegen.

### Identitaet und Verzeichnisanbindung

- Empfehlung: OpenID Connect fuer Microsoft Entra ID.
- Empfehlung: LDAP oder LDAPS fuer lokales Active Directory.
- Lokale Benutzer und Gastkonten sollten intern gepflegt werden koennen.
- Gruppen aus AD oder Entra sollten synchronisierbar und cachenbar sein.

### Sicherheit

- Verschluesselung sensibler Felder auf Anwendungsebene mit einem Key-Management-Konzept.
- Trennung zwischen normaler Formularsicht und entschluesselter Detailansicht.
- Audit-Logs unveraenderlich oder mindestens append-only modellieren.
- Berechtigungen konsequent ueber Policies oder Access-Control-Layer pruefen.

### Verarbeitung und Performance

- PDF-Erzeugung, E-Mail-Versand, Webhooks und Inbound-Mail-Verarbeitung sollten in Queues laufen.
- Das direkte Formular-Submit sollte schnell nur speichern und bestaetigen.
- Schwere Nebenprozesse sollen asynchron erfolgen.

### Kiosk-Mode

- Eigener Kiosk-Login oder geraetegebundener Zugang.
- Eigener Kiosk-Layoutmodus ohne Navigation.
- Konfiguration pro Geraet, welches Formular oder welcher Formularsatz angezeigt werden darf.

### Betrieb

- Docker Compose fuer Entwicklung.
- Docker-Stacks oder Kubernetes-kompatible Container fuer Produktion.
- Trennung von App, Datenbank, Queue-Worker, Scheduler, Reverse Proxy und Storage.
- Plugins sollen als signierte oder versionierte Erweiterungspakete installierbar sein.
- Produktive Themes und kundenspezifische Assets sollen ueber Volumes oder Admin-Uploads bereitgestellt werden.

## Empfohlene Datenobjekte

- User
- IdentityProvider
- Group
- Role
- Permission
- UserGroupAssignment
- GroupRoleAssignment
- Form
- FormVersion
- FormField
- FormRule
- FormVisibilityRule
- Submission
- SubmissionVersion
- SubmissionFieldValue
- SubmissionMessage
- SubmissionAttachment
- SubmissionStatusHistory
- AuditLog
- KioskDevice
- WebhookEndpoint
- MailConnector
- DirectoryConnector

## Empfehlenswerte Einfuehrungsreihenfolge

- Phase 1: neue technische Basis, Docker, PostgreSQL, Authentifizierung, lokale Benutzer, Rollen und Rechte, Formular- und Submission-Datenmodell
- Phase 2: Formverwaltung, Web-Designer, Runtime-Renderer, Validierung, API, Adminbereich
- Phase 3: AD- und Entra-Anbindung, Gruppen, formularbezogene Sichtbarkeit, sensible Formulare, Verschluesselung
- Phase 4: Antworten, Ticketverlauf, Korrekturen, Archiv, Statistiken
- Phase 5: Inbound-E-Mail, vollstaendige Workflows, Eskalationen, erweiterte Automatisierung

## Besondere Empfehlungen

- Fuer sensible Prozesse wie Besucheranmeldung und Verbandbuch sollten Formulare explizit als sensibel markierbar sein.
- Sensible Formulare sollten strengere Rechte, verschluesselte Speicherung und gesondertes Logging erhalten.
- Antworten per E-Mail wuerde ich als spaetere Phase sehen, nicht als ersten Release-Block.
- Der Formulardesigner sollte zwingend auf einer JSON-basierten Formdefinition aufbauen, nicht auf generiertem PHP-Code.
- Submissions sollten nicht mehr primÃ¤r als JSON-Dateien behandelt werden, sondern in einer echten Datenbank liegen.
- PDF, Mail und Webhook sollten asynchron ueber Jobs laufen, damit der Benutzer direkt eine schnelle Erfolgsmeldung bekommt.

## Empfehlung fuer das Update auf Version 2.0
### Grundsatz
- Fuer Version 2.0 empfehle ich kein hartes Refactoring direkt im laufenden Altprojekt.
- Das Risiko ist zu hoch, weil Authentifizierung, Datenmodell, Designer, API, Berechtigungen und Kiosk-Mode nahezu alle Kernbereiche betreffen.
- Sinnvoller ist eine kontrollierte Neuentwicklung mit Uebernahme fachlicher Logik aus dem bestehenden Repository.
### Empfohlener Weg
- Behalte das bestehende Repository als Version 1.x stabil und betriebsbereit.
- Erstelle fuer Version 2.0 einen neuen Branch auf Basis des aktuellen Repositories, zum Beispiel `v2-architecture` oder `feature/v2-platform`.
- Wenn sich frueh zeigt, dass Framework, Datenmodell und Build-Struktur grundlegend anders werden, ist mittelfristig ein neues Repository fuer Version 2.0 die sauberere Loesung.
- Praktisch bedeutet das: zuerst in einem Branch Architektur, Migrationen und Grundsystem aufbauen, spaeter bei Bedarf in ein neues Repo ausgliedern.
### Meine konkrete Empfehlung
- Kurzfristig: neuer Branch fuer Analyse, Architektur und Prototyping.
- Mittelfristig: neues Repository fuer die eigentliche produktive Version 2.0, sobald feststeht, dass Laravel oder Symfony, PostgreSQL, Queue-Worker, API und Frontend neu aufgebaut werden.
- Das bestehende Repository bleibt dann als Referenz und fuer Wartung von Version 1 erhalten.
### Warum nicht direkt im Hauptbranch umbauen
- Zu viele gleichzeitige Aenderungen.
- Hohes Risiko fuer laufende Formulare und produktive Prozesse.
- Kaum saubere Rueckfallmoeglichkeit.
- Schwer nachvollziehbare Git-Historie.
- Schwierige Trennung zwischen Bugfixes fuer 1.x und Neubau fuer 2.0.
### Was Refactoring hier praktisch bedeutet
- Refactoring heisst nicht automatisch alles neu zu schreiben.
- In eurem Fall wird es eher eine Kombination aus Refactoring, Neuarchitektur und Migration sein.
- Bestehende Fachlogik wie PDF, Mailtexte, Formularinhalte, Feldtypen oder Prozesswissen kann uebernommen werden.
- Technische Grundlagen wie Datenhaltung, Authentifizierung, Rechte, Designer und API sollten dagegen neu aufgebaut werden.
### Sinnvolle Migrationsstrategie
- Bestehende Formulare analysieren und in ein neues Formdefinitionsformat ueberfuehren.
- Bestehende Submissionen aus JSON in die neue Datenbank migrieren, falls fachlich erforderlich.
- Bestehende Schnittstellen fuer Mail, URL und Webhook kompatibel halten.
- Neue Plattform parallel zur alten betreiben.
- Pilotformulare zuerst in 2.0 nachbauen.
- Nach erfolgreicher Pilotphase schrittweise weitere Formulare migrieren.
### Git-Empfehlung fuer euch
- main oder master bleibt fuer Version 1 stabil.
- `feature/*` fuer kleinere Aenderungen an 1.x.
- `v2-architecture` oder `feature/v2-platform` fuer die neue Generation.
- Spaeter optional neues Repository wie `formular-system-v2`.
### Entscheidung in einem Satz
- Wenn ihr 2.0 wirklich mit Benutzerverwaltung, Designer, Plugins, API, Kiosk-Mode und Verschluesselung bauen wollt, startet mit einem separaten V2-Branch und plant sehr wahrscheinlich ein eigenes V2-Repository ein.
## Prompt fuer die Umsetzung durch einen Coding-Agenten

Nutze den folgenden Prompt als Ausgangspunkt fuer eine echte Umsetzung ohne Platzhalter:

```text
Du arbeitest in einem bestehenden PHP-Repository fuer ein einfaches Formularsystem. Erstelle daraus eine produktionsreife Version 2.0. Verwende keine Dummys, keine Fake-Implementierungen und keine Platzhalter-Features. Alle neu gebauten Funktionen muessen technisch funktionsfaehig, integriert und testbar sein.

Ziel:
Baue aus dem bestehenden Formularsystem eine vollwertige Formular- und Vorgangsplattform mit Benutzerverwaltung, Rollen und Rechten, Gruppen, Verzeichnisanbindung, Web-Formulardesigner, Submission-Verwaltung, Antworten, Archiv, Statistik, Kiosk-Mode, Verschluesselung fuer sensible Formulare, Adminbereich, OpenAPI und Docker-Betrieb.

Wichtige Vorgaben:
- Analysiere zuerst das bestehende Repository und uebernimm funktionierende Bestandteile sinnvoll.
- Erhalte die vorhandenen Schnittstellen fuer E-Mail, URL-Callback und Webhook.
- Plane das System von Anfang an pluginfaehig mit stabilen Erweiterungspunkten.
- Fuehre eine saubere Zielarchitektur ein statt die alte Struktur nur weiter zu patchen.
- Nutze eine relationale Datenbank fuer Formulare, Benutzer, Rollen, Gruppen, Submissionen, Antworten, Audit-Logs und Statistiken.
- Nutze eine JSON-basierte Formdefinition fuer Designer und Runtime.
- Baue echte Rechtepruefung serverseitig ein.
- Baue echte Verschluesselung fuer sensible Formulare und sensible Felder ein.
- Stelle alle Kernfunktionen ueber eine dokumentierte OpenAPI bereit.
- Das gesamte System muss in Docker laufen.

Funktionsumfang:
- Lokale Benutzerverwaltung
- Gastbenutzer
- Authentifizierung gegen lokales Active Directory
- Authentifizierung gegen Microsoft 365 / Microsoft Entra ID
- Synchronisation oder Mapping von AD- und Entra-Gruppen
- Rollen- und Rechtesystem
- formularbezogene Sichtbarkeit
- benutzer- und gruppenbasierte Freigaben
- Adminbereich fuer Benutzer, Gruppen, Rollen, Verzeichnisanbindungen und Schnittstellen
- Webbasierter Formulardesigner
- Formulardefinitionen mit Versionierung
- If-Then-Else-Regeln
- Pflichtfelder auf Basis von Bedingungen
- dynamische Subformulare
- Medienfelder
- umfangreiche Validierung
- Submissionen als Vorgang mit eindeutiger Meldungsnummer
- PDF-Erzeugung
- E-Mail-Versand
- Webhook- und URL-Callbacks
- Antworten auf bestehende Vorgaege mit verknuepften Antwortformularen
- Korrekturablaeufe und Bearbeitung eigener Submissions mit Audit-Log
- Archivierung
- persoenliche Dashboards und Admin-Statistiken
- Kiosk-Mode mit geraete- oder tokengebundener Freigabe auf genau definierte Formulare
- OpenAPI fuer Benutzer, Rollen, Gruppen, Formulare, Submissionen, Antworten, Archive, Statistiken und Administration
- Plugin-System fuer Feldscanner, Integrationen und Facherweiterungen
- Ausweisscanner-Plugin ohne persistente Speicherung des Ausweisscans, aber mit uebernehmbaren Extraktionsdaten und Pruefstatus
- Visitenkarten-Plugin mit Speicherung von Scan und extrahierten Daten
- Theme- und Branding-System mit Demo-Assets im Repo und produktiven Assets ausserhalb des Repositories

Sicherheits- und Datenschutzanforderungen:
- Formulare muessen als sensibel markierbar sein.
- Sensible Submissionen muessen verschluesselt gespeichert werden.
- Nur berechtigte Benutzer duerfen sensible Inhalte entschluesseln.
- Alle Lese-, Schreib- und Freigabeoperationen fuer sensible Daten muessen protokolliert werden.
- DSGVO-relevante Anforderungen wie Nachvollziehbarkeit, Maskierung, Archivierung und Loeschung muessen beruecksichtigt werden.

Kiosk-Mode:
- Implementiere einen echten Kiosk-Mode.
- Ein Kiosk-Geraet darf nur genau die freigegebenen Formulare sehen.
- Keine Navigation zu anderen Formularen oder Adminseiten.
- Nach Absenden Ruecksetzung auf Startzustand.
- Optional Vollbildmodus und automatische Abmeldung.

Technische Empfehlungen:
- Verwende ein modernes PHP-Framework wie Laravel oder Symfony.
- Verwende PostgreSQL.
- Verwende Docker Compose fuer Entwicklung und einen produktionsnahen Container-Stack.
- Nutze Queue-Worker fuer PDF, Mail und Webhooks.
- Baue API und UI so, dass der Designer und die Formularlaufzeit dieselbe Formdefinition verwenden.

Umsetzungsmodus:
- Arbeite in klaren Phasen.
- Beginne mit einer Architektur- und Migrationsanalyse des bestehenden Repositories.
- Erstelle dann die neue Projektstruktur.
- Migriere anschliessend schrittweise Kernfunktionen aus dem Altbestand.
- Fuehre keine grossen Behauptungen ueber Fertigstellung ein, wenn Funktionen noch nicht real integriert sind.
- Wenn du auf Entscheidungen triffst, dokumentiere sie knapp und nachvollziehbar im Repository.

Konkrete Deliverables:
- lauffaehige Docker-Umgebung
- Datenbankschema und Migrationen
- echte Authentifizierungs- und Autorisierungslogik
- Adminbereich
- Web-Formulardesigner
- Formularlaufzeit fuer Endbenutzer
- Submission- und Antwortverwaltung
- Audit-Log
- Archiv
- Statistik-Dashboards
- OpenAPI-Spezifikation
- Tests fuer zentrale fachliche und sicherheitsrelevante Pfade
- Dokumentation fuer Installation, Betrieb, Rollenmodell, Verzeichnisanbindung und API

Wichtig:
- Keine Dummys.
- Keine Mock-Oberflaechen als fertige Loesung ausgeben.
- Keine Fake-Buttons ohne Backend-Funktion.
- Keine halbe Benutzerverwaltung.
- Keine Scheinintegration von AD oder Entra.
- Jede implementierte Funktion muss Ende-zu-Ende funktionieren.

Beginne mit:
1. Analyse der bestehenden Codebasis
2. Vorschlag fuer Zielarchitektur und Migrationspfad
3. Umsetzung der technischen Basis
4. Schrittweise Implementierung bis zu einer lauffaehigen Version 2.0
```




