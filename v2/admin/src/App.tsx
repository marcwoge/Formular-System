import { useEffect, useState } from 'react'
import './App.css'

type SystemContext = {
  product: {
    name: string
    code: string
  }
  license: {
    edition: string
    is_valid: boolean
    status: string
    reason?: string | null
  }
  free_edition_notice: {
    enabled: boolean
    text: string
  }
  branding?: {
    mode: string
    display_name: string
    primary_logo_url: string
    vendor_logo_url: string
    footer_logo_url?: string | null
    white_label_configured: boolean
    force_vendor_branding: boolean
  }
}

const pillars = [
  'Laravel API mit PostgreSQL und Redis',
  'Rollen, Rechte und Gruppen',
  'JSON-basierter Formulardesigner',
  'Submissionen als Vorgaenge',
  'Audit, Archiv und sensible Daten',
  'Plugin-System, Lizenzierung und Kiosk-Mode',
]

const fallbackContext: SystemContext = {
  product: {
    name: 'FormsHub',
    code: 'formshub',
  },
  license: {
    edition: 'free',
    is_valid: false,
    status: 'free',
  },
  free_edition_notice: {
    enabled: true,
    text: 'FormsHub Free Edition - this installation is currently running without a valid commercial license.',
  },
  branding: {
    mode: 'default',
    display_name: 'FormsHub',
    primary_logo_url: '/brand-assets/formshub.png',
    vendor_logo_url: '/brand-assets/formshub.png',
    footer_logo_url: '/brand-assets/formshub.png',
    white_label_configured: false,
    force_vendor_branding: true,
  },
}

function App() {
  const [context, setContext] = useState<SystemContext>(fallbackContext)

  useEffect(() => {
    let isActive = true

    fetch('/api/system/license-status')
      .then(async (response) => {
        if (!response.ok) {
          throw new Error(`Request failed: ${response.status}`)
        }

        return response.json() as Promise<SystemContext>
      })
      .then((payload) => {
        if (isActive) {
          setContext(payload)
        }
      })
      .catch(() => {
        if (isActive) {
          setContext(fallbackContext)
        }
      })

    return () => {
      isActive = false
    }
  }, [])

  const branding = context.branding ?? fallbackContext.branding
  const showFreeNotice = context.free_edition_notice?.enabled
  const footerLogoUrl = branding?.footer_logo_url

  return (
    <main className="shell">
      <section className="hero">
        <div className="brand-lockup">
          <img className="brand-logo" src={branding?.primary_logo_url} alt={branding?.display_name ?? 'FormsHub'} />
          <div>
            <p className="eyebrow">{branding?.display_name ?? 'FormsHub'}</p>
            <h1>Admin und Plattformbasis fuer Version 2</h1>
          </div>
        </div>
        <p className="lead">
          Dieses Frontend ist das neue Admin-Grundgeruest fuer Benutzer, Rollen, Formulare,
          Submissionen, Plugins, Lizenzierung, Statistik und Kiosk-Konfiguration.
        </p>
        {showFreeNotice ? <p className="notice">{context.free_edition_notice.text}</p> : null}
      </section>

      <section className="grid">
        {pillars.map((pillar) => (
          <article key={pillar} className="card">
            <h2>{pillar}</h2>
            <p>
              Dieser Bereich wird schrittweise mit echter API, serverseitigen Rechten und
              produktionsfaehiger Fachlogik ausgebaut.
            </p>
          </article>
        ))}
      </section>

      <footer className="app-footer">
        <div>
          <strong>{context.product.name}</strong>
          <span>{context.license.is_valid ? ' Lizenz aktiv' : ' Free Edition'}</span>
        </div>
        {footerLogoUrl ? (
          <img className="footer-logo" src={footerLogoUrl} alt="FormsHub Footer Logo" />
        ) : null}
      </footer>
    </main>
  )
}

export default App