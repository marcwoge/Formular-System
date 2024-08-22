<form id="multiStepForm" action="process_form.php" method="POST" class="validated-form">
        <input type="hidden" name="form_title" value="Formular 1">

        <!-- E-Mail Empfänger und Texte -->
            <input type="hidden" name="email_subject" value="Neue Formularübermittlung">
            <input type="hidden" name="email_pretext" value="Hallo,\nvielen Dank für Ihre Registrierung.\nFolgende Daten wurden in unserem System gespeichert:\n\n">
            <input type="hidden" name="email_posttext" value="\n\nWir wünschen Ihnen einen tollen Aufenthalt.\nMit freundlichen Grüßen\n Türsteher.">
            
        <!-- Schritt 1: Persönliche Daten -->
        <div class="form-step">
            <h2>Schritt 1: Persönliche Daten</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

	    <label for="float">Fließkommazahl:</label>
            <input type="number" step="0.01" id="float" name="float" required>



            <button type="button" class="next-step">Weiter</button>
        </div>

        <!-- Schritt 2: Nachricht und Datum -->
        <div class="form-step" style="display:none;">
            <h2>Schritt 2: Nachricht und Datum</h2>
            <label for="message">Nachricht:</label>
            <textarea id="message" name="message" required></textarea>

            <label for="date">Datum:</label>
            <input type="date" id="date" name="date" required>

            <button type="button" class="previous-step">Zurück</button>
            <button type="button" class="next-step">Weiter</button>
        </div>

        <!-- Schritt 3: Zeit und Adresse -->
        <div class="form-step" style="display:none;">
            <h2>Schritt 3: Zeit und Adresse</h2>
            <label for="datetime">Datum und Uhrzeit:</label>
            <input type="datetime-local" id="datetime" name="datetime" required>

            <label for="time">Uhrzeit:</label>
            <input type="time" id="time" name="time" required>

            <label for="address">Adresse:</label>
            <input type="text" id="address" name="address" required>

            <button type="button" class="previous-step">Zurück</button>
            <button type="button" class="next-step">Weiter</button>
        </div>

        <!-- Schritt 4: Zahlen -->
        <div class="form-step" style="display:none;">
            <h2>Schritt 4: Zahlen</h2>
            <label for="integer">Ganzzahl:</label>
            <input type="number" id="integer" name="integer" required>

            <label for="float">Fließkommazahl:</label>
            <input type="number" step="0.01" id="float" name="float" required>

            <button type="button" class="previous-step">Zurück</button>
            <button type="submit" class="button">Absenden</button>
        </div>
    </form>