# V2 Workspace

Dieses Verzeichnis enthaelt das erste technische Grundgeruest fuer FormsHub.

## Bestandteile

- `backend/`: Laravel API
- `admin/`: React TypeScript Admin-Frontend
- `infra/nginx/`: Reverse-Proxy-Konfiguration
- `docker-compose.yml`: lokale Multi-Container-Entwicklung

## Ziel

Dieses Geruest ist die Basis fuer:

- API / Backend
- Admin-Frontend
- Queue Worker
- Scheduler
- PostgreSQL
- Redis
- nginx
- externe Lizenzierung mit Free-Edition-Fallback

## Docker-First

Auf dem Host ist kein lokales PHP erforderlich. Laravel, Composer und spaetere Migrationen laufen ausschliesslich in Containern.

Vorgehen:

1. `.env.example` nach `.env` kopieren
2. Docker Compose starten
3. Laravel-Migrationen und Seeder im Backend-Container ausfuehren
4. React-Admin und API gegen die laufenden Container entwickeln

## Hinweis

Die Struktur ist als V2-Neuaufbau gedacht. Version 1 bleibt getrennt.

## Lizenzierung

- Die V2-Basis sieht eine externe Lizenzierungsanbindung auf Basis des lokalen `Lizenz-server`-Projekts vor.
- Die Lizenzserver-URL bleibt vorerst konfigurierbar.
- Ohne gueltige Lizenz laeuft FormsHub als Free Edition.
- In der Free Edition muessen Formulare, Footer, PDFs und andere Dokumente einen Copyright- und Free-Version-Hinweis anzeigen.
- Bei gueltiger Lizenz verschwindet dieser Hinweis automatisch.
- Fuer spaetere Produktivsetzung sind z. B. `lizenz.systemhub.de` oder `lizenz.woge.engineer` moegliche Ziel-URLs.

## Standard-Branding

- Das Vendor-Standardlogo liegt in ormshub.png im V2-Root.
- Wenn kein White-Label-Logo konfiguriert ist, wird dieses Logo als Standardlogo verwendet.
- Wenn eine White-Label-Konfiguration aktiv ist und die Installation lizenziert ist, bleibt das FormsHub-Logo klein im Footer sichtbar.
- Wenn die Installation unlizenziert ist, erzwingt FormsHub ueberall das Vendor-Logo und zeigt den Free-Edition-Hinweis an.


## Aktueller API-Stand

- POST /api/auth/login: lokale Anmeldung per E-Mail und Passwort
- GET /api/auth/me: aktuelles Benutzerprofil inklusive Rollen, Gruppen und Rechten
- POST /api/auth/logout: aktuelles API-Token abmelden
- GET /api/access/catalog: Rollen-, Gruppen- und Rechtekatalog fuer Admins
- GET /api/forms: vorhandene Formulare laden
- POST /api/forms: neues Formular mit Initialversion anlegen
- POST /api/forms/{id}/versions: neue Formularversion erzeugen
- POST /api/forms/{id}/publish/{versionId}: Formularversion veroeffentlichen
- GET /api/system/license-status: Lizenz- und Brandingstatus

## Dev-Seed

- Seed legt lokal dmin@example.local mit dem Passwort dmin123! an.
- Diese Zugangsdaten sind nur fuer Entwicklung gedacht und muessen spaeter durch echte Benutzerverwaltung ersetzt werden.


## Microsoft 365 SSO

- Microsoft 365 / Entra ID SSO ist als OpenID-Connect-Basis ueber Socialite vorgesehen und bereits im Backend verdrahtet.
- Aktivierung erfolgt ueber MS365_SSO_ENABLED=true sowie AZURE_CLIENT_ID, AZURE_CLIENT_SECRET, AZURE_TENANT_ID und AZURE_REDIRECT_URI.
- Backend-Routen: /auth/microsoft/redirect und /auth/microsoft/callback.
- API liefert verfuegbare Auth-Provider ueber /api/auth/providers.
- Bei erfolgreichem Callback wird der Benutzer ueber external_identities mit dem Identity-Provider verknuepft und ein API-Token fuer das Admin-Frontend erzeugt.
