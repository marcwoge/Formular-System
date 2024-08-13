<form action="process_form.php" method="POST" class="validated-form">
    <input type="hidden" name="form_title" value="Formular 1">
    <table>
        <tr>
            <td><label for="name">Name:</label></td>
            <td><input type="text" id="name" name="name" required></td>
        </tr>
        <tr>
            <td><label for="email">Email:</label></td>
            <td><input type="email" id="email" name="email" required></td>
        </tr>
        <tr>
            <td><label for="message">Nachricht:</label></td>
            <td><textarea id="message" name="message" required></textarea></td>
        </tr>
        <tr>
            <td><label for="date">Datum:</label></td>
            <td><input type="date" id="date" name="date" required></td>
        </tr>
        <tr>
            <td><label for="datetime">Datum und Uhrzeit:</label></td>
            <td><input type="datetime-local" id="datetime" name="datetime" required></td>
        </tr>
        <tr>
            <td><label for="time">Uhrzeit:</label></td>
            <td><input type="time" id="time" name="time" required></td>
        </tr>
        <tr>
            <td><label for="address">Adresse:</label></td>
            <td><input type="text" id="address" name="address" required></td>
        </tr>
        <tr>
            <td><label for="integer">Ganzzahl:</label></td>
            <td><input type="number" id="integer" name="integer" required></td>
        </tr>
        <tr>
            <td><label for="float">Fließkommazahl:</label></td>
            <td><input type="number" step="0.01" id="float" name="float" required></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                <button type="submit" class="button">Absenden</button>
            </td>
        </tr>
    </table>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
    });
</script>
