import './App.css'

const pillars = [
  'Laravel API mit PostgreSQL und Redis',
  'Rollen, Rechte und Gruppen',
  'JSON-basierter Formulardesigner',
  'Submissionen als Vorgaenge',
  'Audit, Archiv und sensible Daten',
  'Plugin-System, Lizenzierung und Kiosk-Mode',
]

function App() {
  return (
    <main className="shell">
      <section className="hero">
        <p className="eyebrow">FormsHub</p>
        <h1>Admin und Plattformbasis fuer Version 2</h1>
        <p className="lead">
          Dieses Frontend ist das neue Admin-Grundgeruest fuer Benutzer, Rollen, Formulare,
          Submissionen, Plugins, Lizenzierung, Statistik und Kiosk-Konfiguration.
        </p>
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
    </main>
  )
}

export default App