document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('.validated-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                alert('Bitte füllen Sie alle Felder korrekt aus.');
            }
        });

        const emailFields = form.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => {
            field.addEventListener('input', function () {
                if (field.validity.typeMismatch) {
                    field.setCustomValidity('Bitte geben Sie eine gültige E-Mail-Adresse ein.');
                } else {
                    field.setCustomValidity('');
                }
            });
        });

        const dateFields = form.querySelectorAll('input[type="date"], input[type="datetime-local"], input[type="time"]');
        dateFields.forEach(field => {
            field.addEventListener('input', function () {
                if (field.validity.rangeUnderflow || field.validity.rangeOverflow) {
                    field.setCustomValidity('Bitte geben Sie ein gültiges Datum/Uhrzeit ein.');
                } else {
                    field.setCustomValidity('');
                }
            });
        });

        const numberFields = form.querySelectorAll('input[type="number"]');
        numberFields.forEach(field => {
            field.addEventListener('input', function () {
                if (field.validity.rangeUnderflow || field.validity.rangeOverflow) {
                    field.setCustomValidity('Bitte geben Sie eine gültige Zahl ein.');
                } else {
                    field.setCustomValidity('');
                }
            });
        });
    });
});
