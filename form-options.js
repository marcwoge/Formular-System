document.addEventListener('DOMContentLoaded', function () {
    // Slider Value Update
    const slider = document.getElementById("myRange");
    const output = document.getElementById("sliderValue");

    if (slider && output) { 
        output.innerHTML = slider.value;

        slider.oninput = function() {
            output.innerHTML = this.value;
        }
    }
});
