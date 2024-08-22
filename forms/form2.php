<form action="process_form.php" method="POST" class="validated-form">
    <input type="hidden" name="form_title" value="Formular 2">
    <!-- E-Mail Empfänger und Texte -->
        <input type="hidden" name="email_subject" value="Neue Formularübermittlung">
        <input type="hidden" name="email_pretext" value="Hallo,\nvielen Dank für Ihre Registrierung.\nFolgende Daten wurden in unserem System gespeichert:\n\n">
        <input type="hidden" name="email_posttext" value="\n\nWir wünschen Ihnen einen tollen Aufenthalt.\nMit freundlichen Grüßen\nNotstromtechnik-Clasen Türsteher.">

    <label for="username">Benutzername:</label>
    <input type="text" id="username" name="username" required>
    <br>
    <label for="Slider">Slider: <span id="sliderValue">50</span></label>
    <input type="range" min="1" max="100" value="50" class="slider" id="myRange" name="slider_value">

    <br>
 <!-- Checkbox -->
 <label for="checkbox">Wählen Sie eine oder mehrere Optionen:</label><br>
    <label for="checkbox1">Checkbox 1</label>
    <input type="checkbox" id="checkbox1" name="checkboxes[]" value="Checkbox 1">
    <br>
    <label for="checkbox2">Checkbox 2</label>
    <input type="checkbox" id="checkbox2" name="checkboxes[]" value="Checkbox 2">
    <br>
    <label for="checkbox3">Checkbox 3</label>
    <input type="checkbox" id="checkbox3" name="checkboxes[]" value="Checkbox 3">
    <br>


    <button type="submit">Absenden</button>
</form>
