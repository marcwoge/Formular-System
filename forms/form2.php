<form action="process_form.php" method="POST" class="validated-form">
    <input type="hidden" name="form_title" value="Formular 2">
    <!-- E-Mail Empfänger und Texte -->
        <input type="hidden" name="email_subject" value="Neue Formularübermittlung">
        <input type="hidden" name="email_pretext" value="Hallo,\nvielen Dank für Ihre Registrierung.\nFolgende Daten wurden in unserem System gespeichert:\n\n">
        <input type="hidden" name="email_posttext" value="\n\nWir wünschen Ihnen einen tollen Aufenthalt.\nMit freundlichen Grüßen\nNotstromtechnik-Clasen Türsteher.">

    <label for="username">Benutzername:</label>
    <input type="text" id="username" name="username" required>
    <br>
<label for="username">Firma:</label>
    <input type="text" id="Firma" name="Firma">
    <br>

    <label for="password">Passwort:</label>
    <input type="password" id="password" name="password" required>
    <br>
    <label for="email2">Email:</label>
    <input type="email" id="email2" name="email" required>
    <br>
    <label for="birthdate">Datum:</label>
    <input type="date" id="date" name="date" required>
    <br>
    <label for="Slider">Slider: <span id="sliderValue">50</span></label>
    <input type="range" min="1" max="100" value="50" class="slider" id="myRange" name="slider_value">

    <br>
    <button type="submit">Absenden</button>
</form>
