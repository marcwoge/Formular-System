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