<form action="process_form.php" method="POST" class="validated-form">
    <h2>Demo-Formular: Alle HTML5-Formularoptionen</h2>
    <input type="hidden" name="form_title" value="Formular 2">
    <!-- E-Mail Empfänger und Texte -->
        <input type="hidden" name="email_subject" value="Neue Formularübermittlung">
        <!--
        <input type="hidden" name="email_recipients" value="email@example.com, email2@example.com">
        -->
        <input type="hidden" name="email_pretext" value="Hallo,<br>nvielen Dank für Ihre Registrierung.<br>Folgende Daten wurden in unserem System gespeichert:<p>">
        <input type="hidden" name="email_posttext" value="</p> <br>Wir wünschen Ihnen einen tollen Aufenthalt.<br>Mit freundlichen Grüßen<br> Türsteher.">

     <!-- Versteckte Felder für Dateien -->
    <input type="hidden" name="file_attachments[]" value="datei1.pdf">
    <input type="hidden" name="file_attachments[]" value="datei2.jpg">

    <!-- Hier beginnt das Formular -->    
    <!-- Text -->
    <label for="text">Text:</label>
    <input type="text" id="text" name="text">
    <br>

    <!-- Email -->
    <label for="email">Email:</label>
    <input type="email" id="email" name="email">
    <br>

    <!-- Password -->
    <label for="password">Passwort:</label>
    <input type="password" id="password" name="password">
    <br>

    <!-- Adresse -->
    <label for="address">Adresse:</label>
    <input type="text" id="address" name="address" required>
    <br>

    <!-- Bild -->
    <img src="/img/NTC_eShelter_59.jpg" alt="Beschreibung" class="img-responsive">
    <br>

    <!-- Number -->
    <label for="number">Zahl:</label>
    <input type="number" id="number" name="number" min="0" max="100">
    <br>

    <!-- Range (Slider) -->
    <label for="Slider">Slider: <span id="sliderValue">50</span></label>
    <input type="range" min="1" max="100" value="50" class="slider" id="myRange" name="slider_value">
    <br>

    <!-- Video -->
    <video controls class="video-responsive">
        <source src="/img/NTC_-_Animation_2023.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <br>

    <!-- Radio Buttons -->
    <label for="radio">Wählen Sie eine Option:</label><br>
    <input type="radio" id="option1" name="radioOptions" value="Option 1">
    <label for="option1">Option 1</label><br>
    <input type="radio" id="option2" name="radioOptions" value="Option 2">
    <label for="option2">Option 2</label><br>
    <input type="radio" id="option3" name="radioOptions" value="Option 3">
    <label for="option3">Option 3</label>
    <br>

    <!-- Checkbox -->
    <label for="checkbox">Wählen Sie eine oder mehrere Optionen:</label><br>
    <label for="checkbox1">Checkbox 1</label>
    <input type="checkbox" id="checkbox1" name="checkboxes" value="Checkbox 1">
    <br>
    <label for="checkbox2">Checkbox 2</label>
    <input type="checkbox" id="checkbox2" name="checkboxes" value="Checkbox 2">
    <br>
    <label for="checkbox3">Checkbox 3</label>
    <input type="checkbox" id="checkbox3" name="checkboxes" value="Checkbox 3">
    <br>

    <!-- Date -->
    <label for="date">Datum:</label>
    <input type="date" id="date" name="date">
    <br>

    <!-- Time -->
    <label for="time">Uhrzeit:</label>
    <input type="time" id="time" name="time">
    <br>

    <!-- Color -->
    <label for="color">Farbe:</label>
    <input type="color" id="color" name="color" value="#003057">
    <br>

    <!-- File -->
    <label for="file">Datei hochladen:</label>
    <input type="file" id="file" name="file">
    <br>

    <!-- Tel -->
    <label for="tel">Telefonnummer:</label>
    <input type="tel" id="tel" name="tel">
    <br>

    <!-- URL -->
    <label for="url">Website:</label>
    <input type="url" id="url" name="url">
    <br>

    <!-- Textarea -->
    <label for="textarea">Nachricht:</label>
    <textarea id="textarea" name="textarea" rows="4" cols="50"></textarea>
    <br>

    <!-- Select -->
    <label for="select">Wählen Sie eine Option:</label>
    <select id="select" name="select">
        <option value="Option 1">Option 1</option>
        <option value="Option 2">Option 2</option>
        <option value="Option 3">Option 3</option>
    </select>
    <br>

    <!-- Datalist -->
    <label for="datalist">Wählen Sie oder geben Sie einen Wert ein:</label>
    <input list="datalistOptions" id="datalist" name="datalist">
    <datalist id="datalistOptions">
        <option value="Wert 1">
        <option value="Wert 2">
        <option value="Wert 3">
    </datalist>
    <br>

    <!-- Output -->
    <label for="output">Ergebnis:</label>
    <output id="output" name="output">0</output>
    <br>

    <!-- Submit Button -->
    <button type="submit">Absenden</button>
</form>
