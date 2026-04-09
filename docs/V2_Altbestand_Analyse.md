# V2 Altbestand Analyse

## Technische Ausgangslage

Das aktuelle System ist ein leichtgewichtiges PHP-Formularsystem ohne echtes Framework. Formulare werden als PHP-Dateien im Verzeichnis `forms/` abgelegt. Die Laufzeitlogik liegt schwerpunktmaessig in `index.php` und `process_form.php`.

## Fachlich wiederverwendbare Bestandteile

### Formular-Metadaten und Bedienkonzept

- Hidden Fields wie `form_title`, `email_subject`, `email_recipients`, `email_pretext` und `email_posttext`
- Formularstruktur als einfache deklarative HTML-Formulare
- Mehrstufige Formulare und Validierungsansatz
- Einfache Formularnavigation mit unterschiedlichen Anzeigevarianten

### Submission-Verarbeitung

- Erzeugung einer Meldungsnummer
- PDF-Erzeugung aus Formulardaten
- E-Mail-Versand an Empfaenger und optional an den Absender
- URL-Callback / Server-Callback
- Speicherung der Abgabe als JSON-Datei

### Benutzerkontext

- Vorbelegung auf Basis des aktuellen Windows-Benutzers
- Namens- und E-Mail-Anreicherung aus Serverkontext / LDAP-nahem Kontext

### PDF- und Mail-Know-how

- Eigene PDF-Erzeugungslogik
- Mapping technischer Felder auf fachliche Labels
- Logo- und Footer-Handling
- Mailzusammenbau fuer HTML und Plain Text

## Technische Schwaechen des Altbestands

- Kein Framework, keine modulare Architektur
- Keine relationale Datenbank
- Keine echte Benutzerverwaltung
- Keine Rollen und Rechte
- Keine serverseitig zentrale Policy-Schicht
- Submissionen primär als JSON-Dateien statt als fachliche Vorgangsobjekte
- Keine API-first-Architektur
- Kein Queue-System fuer schwere Jobs
- Keine echte Mandanten-, Theme- oder Plugin-Architektur
- Keine tragfaehige Grundlage fuer Designer, Archiv, Ticketverlauf oder granulare Sichtbarkeit

## Konsequenz fuer V2

Version 2.0 soll fachliche Erkenntnisse und nutzbare Logik aus dem Altbestand uebernehmen, aber technisch neu aufgebaut werden. Besonders sinnvoll wiederzuverwenden sind:

- Feld- und Formularwissen
- PDF-Inhaltslogik
- Mail- und Webhook-Fachlogik
- Beispiel- und Pilotformulare
- bestehende Begriffe, Prozesse und Formulartexte

Nicht sinnvoll direkt zu uebernehmen sind:

- die aktuelle Dateistruktur
- die JSON-Dateispeicherung als Primärpersistenz
- die zentrale Ablaufsteuerung in `index.php` und `process_form.php`
- die aktuelle Rechte- und Authentifizierungslosigkeit
