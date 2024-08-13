document.addEventListener('DOMContentLoaded', function () {
    var currentStep = 0;
    var steps = document.querySelectorAll('.form-step');
    var nextButtons = document.querySelectorAll('.next-step');
    var prevButtons = document.querySelectorAll('.previous-step');
    var form = document.getElementById('multiStepForm');

    function showStep(step) {
        steps.forEach(function (stepElement, index) {
            stepElement.style.display = (index === step) ? 'block' : 'none';
        });
    }

    function validateStep(step) {
        var inputs = steps[step].querySelectorAll('input, textarea');
        for (var i = 0; i < inputs.length; i++) {
            if (!inputs[i].checkValidity()) {
                inputs[i].reportValidity();
                return false;
            }
        }
        return true;
    }

    nextButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
            }
        });
    });

    prevButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            currentStep--;
            showStep(currentStep);
        });
    });

    // Initial anzeigen
    showStep(currentStep);

    // Google Places Autocomplete für Adressfeld
    var autocomplete = new google.maps.places.Autocomplete(document.getElementById('address'));
});
