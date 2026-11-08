document.addEventListener('DOMContentLoaded', function () {
    // Slider Value Update
    const slider = document.getElementById("myRange");
    const output = document.getElementById("sliderValue");

    if (slider && output) { 
        output.textContent = slider.value;

        slider.oninput = function() {
            output.textContent = this.value;
        }
    }
});

