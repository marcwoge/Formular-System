function toggleMenu() {
    var menu = document.getElementById('side-menu');
    menu.classList.toggle('active');
}

function toggleFolder(element) {
    var nested = element.querySelector('.nested');
    if (nested) {
        nested.classList.toggle('active');
        if (nested.style.display === 'block') {
            nested.style.display = 'none';
        } else {
            nested.style.display = 'block';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var folders = document.querySelectorAll('.side-menu .folder');
    folders.forEach(function(folder) {
        folder.addEventListener('click', function(event) {
            toggleFolder(this);
            event.stopPropagation(); // Verhindert das Auslösen von Klickereignissen auf übergeordneten Elementen
        });
    });
});
