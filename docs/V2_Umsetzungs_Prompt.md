# Prompt Fuer Formularsystem 2.0

```text
Du arbeitest in einem bestehenden PHP-Repository fuer ein einfaches Formularsystem. Erstelle daraus eine echte, produktionsreife Version 2.0. Verwende keine Dummys, keine Fake-Implementierungen, keine Platzhalter-Features und keine nur optischen Oberflaechen ohne funktionierendes Backend. Jede gebaute Funktion muss technisch integriert, testbar und Ende-zu-Ende nutzbar sein.

Technische Zielentscheidung:
- Verwende Laravel als Backend-Framework.
- Verwende PostgreSQL als relationale Datenbank.
- Verwende Docker Compose fuer die Entwicklungsumgebung und eine containerfaehige Produktionsstruktur.
- Verwende Queue-Worker fuer PDF-Erzeugung, E-Mail-Versand, Webhooks und spaetere Inbound-Mail-Verarbeitung.
- Verwende eine JSON-basierte Formdefinition fuer Formulardesigner und Formularlaufzeit.
- Verwende ein separates Frontend fuer Adminbereich, Designer und Dashboards, z. B. mit React oder Vue.
- Baue eine dokumentierte OpenAPI-basierte REST-API.

Projektziel:
Baue aus dem bestehenden Formularsystem eine vollwertige Formular-, Vorgangs- und Bearbeitungsplattform mit Benutzerverwaltung, Rollen und Rechten, Gruppen, AD- und Microsoft-Entra-Integration, Web-Formulardesigner, Antworten, Archiv, Statistik, Kiosk-Mode, Plugin-System, sensiblen Formularen mit Verschluesselung, Adminbereich und Docker-Betrieb.

Wichtige Grundsaetze:
- Analysiere zuerst das bestehende Repository.
- Uebernimm fachlich sinnvolle Bestandteile des aktuellen Systems.
- Erhalte bestehende Schnittstellen fuer E-Mail, URL-Callbacks und Webhooks.
- Plane keine direkte Patch-Fortsetzung des alten Aufbaus, sondern eine saubere neue Architektur.
- Rechtepruefungen muessen serverseitig umgesetzt werden.
- Sensible Daten muessen verschluesselt gespeichert werden.
- Schwere Prozesse muessen asynchron laufen.
- Produktive Kunden-CI wie echte Logos, Farben, Fonts oder Branding-Dateien duerfen nicht im Repository liegen.
- Das Repository darf nur Demo-Assets und neutrale Themes enthalten.

Funktionsumfang, der vollstaendig umgesetzt werden soll:

1. Benutzer und Authentifizierung
- lokale Benutzerkonten
- Gastkonten
- Anmeldung gegen lokales Active Directory
- Anmeldung gegen Microsoft 365 / Microsoft Entra ID
- mehrere Identitaetsquellen parallel
- Benutzerprofile mit Rollen, Gruppen, Status und organisatorischen Attributen

2. Gruppen, Rollen und Rechte
- lokale Gruppen
- Gruppen aus AD oder Entra synchronisierbar oder zuordenbar
- serverseitiges Rollen- und Rechtesystem
- Rechte auf System-, Formular- und Submission-Ebene
- benutzer- und gruppenbasierte Berechtigungen

3. Formularverwaltung
- Formulare erstellen, bearbeiten, versionieren, deaktivieren, archivieren
- Formulare als Entwurf, aktiv, deaktiviert oder archiviert markieren
- Formulare als sensibel markieren
- Formulare bestimmten Rollen, Gruppen oder Benutzern zuordnen

4. Webbasierter Formulardesigner
- browserbasierter Designer
- JSON-basierte Formdefinition
- Vorschau, Testmodus und Versionsvergleich
- Feldbibliothek
- wiederverwendbare Subformulare
- bedingte Logik
- If-Then-Else-Regeln
- Pflichtfelder auf Basis von Antworten
- Einblendung zusaetzlicher Felder und Subformulare auf Basis bestimmter Antwortkombinationen

5. Feldtypen und Validierung
- Text, Zahl, Datum, Uhrzeit, E-Mail, Telefon, Checkbox, Radio, Auswahl, Mehrfachauswahl, Textbereich, Datei, versteckte Felder, Medienfelder
- umfangreiche clientseitige und serverseitige Validierung
- formularweite und feldbezogene Regeln
- pluginbasierte Zusatzvalidierungen

6. Submissionen und Vorgangsmodell
- jede Submission erhaelt eine eindeutige Meldungsnummer
- Submissionen werden als Vorgaenge gespeichert
- Statusmodell fuer Vorgaenge
- Antworten, Kommentare und Anhaenge pro Vorgang
- Zuweisung an Benutzer oder Teams
- Korrekturprozesse
- Bearbeitung eigener Submissionen mit Audit-Log und Versionshistorie

7. Sichtbarkeit und Bearbeitung
- Benutzer sehen eigene Submissionen
- Empfaenger sehen relevante Submissionen
- Teams sehen zugeordnete Submissionen
- Administratoren sehen freigegebene Gesamtbereiche
- sensible Felder koennen maskiert oder gesondert geschuetzt werden

8. Antworten und verknuepfte Formulare
- Antworten auf bestehende Vorgaenge
- verknuepfte Antwortformulare
- ticketaehnlicher Verlauf mit Statuswechseln, Kommentaren und Anhaengen

9. Archiv
- Formulare archivieren
- Submissionen archivieren
- Aufbewahrungs- und Loeschkonzepte
- Trennung von aktivem Bereich und Archiv

10. E-Mail, PDF und Webhooks
- E-Mail-Versand mit Meldungsnummer
- PDF-Erzeugung
- vorhandene Callback-Mechanismen beibehalten
- Webhooks konfigurierbar
- asynchrone Verarbeitung ueber Queues

11. Statistik und Dashboards
- persoenliches Benutzer-Dashboard
- formularbezogene Statistiken
- systemweite Statistiken fuer Admins
- Kennzahlen zu Anzahl, Status, Bearbeitungszeiten, Antworten und Korrekturen

12. Kiosk-Mode
- echter Kiosk-Mode fuer einzelne Geraete
- ein Geraet darf nur definierte Formulare sehen
- keine Navigation zu anderen Formularen oder in den Adminbereich
- Ruecksetzung nach Absenden
- konfigurierbare Geraetezuordnung

13. Datenschutz und Sicherheit
- sensible Formulare explizit markierbar
- verschluesselte Speicherung sensibler Submissionen oder Felder
- Entschluesselung nur fuer berechtigte Benutzer
- Audit-Logs fuer Zugriffe und Aenderungen
- DSGVO-relevante Aspekte wie Nachvollziehbarkeit, Loeschkonzepte und Schutz besonders sensibler Daten

14. Plugin-System
- baue ein echtes Plugin-System
- Plugins installierbar, aktivierbar, deaktivierbar und versionierbar
- Plugins mit serverseitigen und optional clientseitigen Komponenten
- Plugin-Hooks fuer Formularrendering, Validierung, Speicherung, PDF, Mail, Antworten, Export und API
- Plugins pro Formular konfigurierbar
- Plugins mit eigener Konfiguration und Berechtigungslogik

15. Scanner-Plugins
- Ausweisscanner-Plugin:
  - Personalausweise und Reisepaesse lesen
  - Daten selektiv in Formularfelder uebernehmen
  - Felder wie Name, Geburtsdatum, Dokumentennummer, Nationalitaet, Dokumententyp und Gueltigkeit verarbeiten
  - speichern, ob Dokumentenvalidierung moeglich war
  - speichern, ob Echtheitspruefung durchgefuehrt wurde und mit welchem Ergebnis
  - das eigentliche Ausweisdokument oder dessen Scan darf nicht persistent gespeichert werden
- Visitenkartenscanner-Plugin:
  - Name, Firma, Position, E-Mail, Telefon, Mobilnummer, Website und Adresse extrahieren
  - Daten in Formularfelder uebernehmen
  - Scan und OCR-Daten duerfen gespeichert werden, wenn das Formular dies erlaubt

16. Design und Branding
- das System muss vollstaendig themingfaehig sein
- Themes fuer UI, PDF, Login, Kiosk und E-Mails
- im Repository nur Demo-Assets und neutrale Themes
- produktive Kunden-CI ausserhalb des Repositories, z. B. ueber Volumes, Uploads oder lokale Konfiguration

17. Adminbereich
- Benutzerverwaltung
- Gruppenverwaltung
- Rollen- und Rechteverwaltung
- AD- und Entra-Anbindung
- Formularverwaltung
- Submission-Verwaltung
- Antwort- und Archivverwaltung
- Pluginverwaltung
- Verwaltung von SMTP, URL-Callbacks und Webhooks
- Theme- und Branding-Verwaltung
- Statistik und Monitoring

18. OpenAPI
- baue eine vollstaendige dokumentierte OpenAPI fuer Benutzer, Gruppen, Rollen, Rechte, Formulare, Formdefinitionen, Submissionen, Antworten, Archive, Plugins, Statistiken und Adminfunktionen

Nicht-funktionale Anforderungen:
- stabiler Docker-Betrieb
- saubere Containertrennung fuer App, Datenbank, Queue-Worker, Scheduler und Reverse Proxy
- Tests fuer sicherheitsrelevante und fachlich kritische Pfade
- klare Architektur und Dokumentation
- keine grossen Versprechen ueber Fertigstellung, wenn Teile nicht real integriert sind

Migrationsansatz:
- behandle das bestehende System als fachliche Ausgangsbasis
- uebernimm nutzbare fachliche Logik
- migriere nicht blind den alten Codeaufbau
- plane Version 2.0 als neue Architektur
- halte Kompatibilitaet fuer bestehende Mail-, URL- und Webhook-Schnittstellen
- beruecksichtige eine spaetere Migration bestehender Formulare und Submissionen

Arbeitsweise:
1. Analysiere die bestehende Codebasis
2. Erstelle eine Zielarchitektur auf Laravel-Basis
3. Definiere Datenmodell, Berechtigungsmodell, Plugin-Schnittstellen und Theme-System
4. Erstelle Docker-Setup, Datenbankmigrationen und technische Basis
5. Implementiere schrittweise die Kernmodule
6. Fuehre echte Tests und Integrationspruefungen durch
7. Dokumentiere Entscheidungen knapp und nachvollziehbar im Repository

Wichtig:
- Keine Dummys
- Keine Fake-Buttons
- Keine Mock-Verwaltung ohne echte Daten
- Keine Scheinanbindung an AD oder Entra
- Keine nichtfunktionalen Plugins
- Keine Platzhalter fuer Sicherheit
- Jede als fertig markierte Funktion muss real funktionieren
```
