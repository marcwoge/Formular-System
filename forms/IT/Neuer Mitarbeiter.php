<form action="process_form.php" method="POST" class="validated-form">
    <h2>IT-Anforderung: Neuer Mitarbeiter</h2>

    <input type="hidden" name="form_title" value="Neuer Mitarbeiter">
    <input type="hidden" name="email_subject" value="IT-Anforderung: Neuer Mitarbeiter">
    <!-- <input type="hidden" name="email_recipients" value="it@example.com"> -->
    <input type="hidden" name="email_pretext" value="Hallo,<br>es wurde ein neuer Mitarbeiter zum Anlegen angefordert.<br>Bitte die folgenden Angaben prüfen und bearbeiten:<p>">
    <input type="hidden" name="email_posttext" value="</p><br>Vielen Dank.<br>Mit freundlichen Grüßen<br>Formular-System">

    <label for="correction_notice">
        <input type="checkbox" id="correction_notice" name="correction_notice" value="Ja">
        Korrektur Meldung
    </label>
    <br>

    <label for="employee_salutation">Anrede:</label>
    <select id="employee_salutation" name="Anrede" required>
        <option value="">Bitte auswählen</option>
        <option value="Frau">Frau</option>
        <option value="Herr">Herr</option>
        <option value="Divers">Divers</option>
    </select>

    <label for="employee_first_name">Vorname:</label>
    <input type="text" id="employee_first_name" name="Vorname" required>

    <label for="employee_last_name">Nachname:</label>
    <input type="text" id="employee_last_name" name="Nachname" required>

    <label for="employee_email">Email-Adresse:</label>
    <input type="email" id="employee_email" name="Email" required>

    <label for="employee_display_name">Anzeigename:</label>
    <input type="text" id="employee_display_name" name="Anzeigename" required>

    <label for="employee_description">Beschreibung:</label>
    <input type="text" id="employee_description" name="Beschreibung" required>

    <label for="employee_office">Büro:</label>
    <select id="employee_office" name="Büro" required>
        <option value="">Bitte auswählen</option>
        <option value="Zentrale Ahrensburg">Zentrale Ahrensburg</option>
        <option value="Niederlassung Frankfurt am Main">Niederlassung Frankfurt am Main</option>
    </select>

    <label for="employee_phone">Rufnummer:</label>
    <input type="text" id="employee_phone" name="Rufnummer" class="readonly-field" readonly>
    <small class="field-hint">Wird durch die IT vergeben.</small>

    <label for="employee_login_name">Anmeldename:</label>
    <input type="text" id="employee_login_name" name="Anmeldename" required>
    <small class="field-hint">Wird automatisch mit der Email-Adresse synchronisiert.</small>

    <label for="employee_short_code">Kürzel:</label>
    <input type="text" id="employee_short_code" name="Kürzel" class="readonly-field" readonly>
    <small class="field-hint">Wird durch die IT vergeben.</small>

    <label for="employee_position">Position:</label>
    <input type="text" id="employee_position" name="Position" required>
    <small class="field-hint">Wird automatisch mit der Beschreibung synchronisiert.</small>

    <label for="employee_department">Abteilung:</label>
    <input type="text" id="employee_department" name="Abteilung" required>

    <label for="employee_manager">Vorgesetzter:</label>
    <input type="text" id="employee_manager" name="Vorgesetzter" required>

    <button type="submit">Absenden</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const emailField = document.getElementById('employee_email');
    const loginField = document.getElementById('employee_login_name');
    const descriptionField = document.getElementById('employee_description');
    const positionField = document.getElementById('employee_position');

    bindMirroredFields(emailField, loginField);
    bindMirroredFields(descriptionField, positionField);
});

function bindMirroredFields(firstField, secondField) {
    if (!firstField || !secondField) {
        return;
    }

    let isSyncing = false;

    const syncValue = function (sourceField, targetField) {
        if (isSyncing) {
            return;
        }

        isSyncing = true;
        targetField.value = sourceField.value;
        isSyncing = false;
    };

    firstField.addEventListener('input', function () {
        syncValue(firstField, secondField);
    });

    secondField.addEventListener('input', function () {
        syncValue(secondField, firstField);
    });
}
</script>
