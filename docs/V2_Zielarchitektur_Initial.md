# V2 Zielarchitektur Initial

## Architekturentscheidung

Version 2.0 wird als neue Plattformgeneration auf Basis von Laravel, PostgreSQL, Redis, React und Docker aufgebaut.

## Hauptbausteine

### Backend API

- Laravel 11
- PHP 8.3+
- REST-API mit OpenAPI-Dokumentation
- Authentifizierung, Autorisierung, Formulare, Submissionen, Antworten, Plugins, Adminfunktionen

### Admin Frontend

- React mit TypeScript
- Oberflaechen fuer Benutzerverwaltung, Rollen, Formulare, Designer, Submissionen, Archive, Plugins, Themes und Statistiken

### Formular Runtime

- serverseitig kontrollierte Formularauslieferung auf Basis versionierter JSON-Definitionen
- getrennt vom Admin-Frontend
- geeignet fuer normale Nutzung, Gastnutzung und Kiosk-Mode

### Infrastruktur

- nginx als Reverse Proxy
- PostgreSQL als Hauptdatenbank
- Redis fuer Queue, Cache und Rate-Limiting-nahe Anwendungsfaelle
- Queue Worker fuer PDF, Mail, Webhooks und spaetere Inbound-Mail
- Scheduler fuer wiederkehrende Aufgaben

## Modulgrenzen im Backend

- Auth / Identity
- Users / Groups / Roles / Permissions
- Forms / Form Versions / Form Rules
- Submissions / Submission Versions / Messages / Attachments / Status History
- Audit
- Plugins
- Integrations
- Dashboards / Statistics
- Kiosk
- Themes / Branding

## Sicherheitsgrundsaetze

- Rechtepruefung ausschliesslich serverseitig
- sensible Inhalte auf Anwendungsebene verschluesseln
- Trennung von App-Key und Data-Encryption-Key
- Audit-Logs fuer sicherheitsrelevante Aktionen
- keine produktiven Secrets oder Branding-Dateien im Repository

## Betriebsgrundsaetze

- Docker-first
- App, nginx, DB, Redis, Queue und Scheduler getrennt
- Hintergrundjobs asynchron
- produktionsnahe lokale Entwicklungsumgebung

## Erste Implementierungsprioritaeten

1. Docker-Basis mit Laravel API, React Admin, PostgreSQL, Redis und nginx
2. Datenmodell fuer Benutzer, Rollen, Gruppen, Formulare und Submissionen
3. Authentifizierungs- und Autorisierungsbasis
4. Formularmodell und Submissionen als Vorgangsobjekte
5. Queue-basierte Nebenprozesse

## Lizenzierungsarchitektur

- Die Lizenzierung wird als eigener Connector-Baustein modelliert.
- Die konkrete Lizenzserver-URL bleibt konfigurierbar und liegt nicht im Repository.
- Das System arbeitet fail-soft: ohne gueltige Lizenz bleibt der Betrieb als Free Edition moeglich.
- Die Runtime und PDF-/Dokumentenerzeugung muessen den Lizenzstatus kennen, um Free-Edition-Hinweise ein- oder auszublenden.
- Der Lizenzstatus wird lokal zwischengespeichert und im Adminbereich sichtbar gemacht.
