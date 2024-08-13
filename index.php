<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular Portal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validation.js" defer></script>
    <!-- Google Places API für Adressvorschläge -->
    <?php
    $googleApiConfig = require __DIR__ . '/config/googleapi.php';
    $texts = require __DIR__ . '/config/texts.php';
    echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $googleApiConfig['api_key'] . '&libraries=places" defer></script>';
    ?>
    <script src="menu.js" defer></script>
    <script src="multi-step.js"></script>
    <script src="form-options.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.validated-form');

        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                // Erstelle FormData-Objekt direkt aus dem Formular
                const formData = new FormData(form);

                fetch('process_form.php', {
                    method: 'POST',
                    body: formData // Sende das FormData-Objekt direkt, damit Dateien korrekt übertragen werden
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
    </script>
</head>
<body>
    <header id="banner">
        <button class="menu-toggle" onclick="toggleMenu()">☰</button>
        <h1>Formular Portal</h1>
        <img src="img/logo.png" alt="Logo">
    </header>
    <nav id="side-menu" class="side-menu">
        <h2>Formulare</h2>
        <ul>
            <?php
            function listFiles($dir, $relativeDir = '') {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $fullPath = $dir . '/' . $file;
                        $relativePath = $relativeDir === '' ? $file : $relativeDir . '/' . $file;
                        if (is_dir($fullPath)) {
                            echo "<li class=\"folder\">$file<ul class=\"nested\">";
                            listFiles($fullPath, $relativePath);
                            echo '</ul></li>';
                        } else if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                            $formName = pathinfo($file, PATHINFO_FILENAME);
                            echo "<li><a href=\"index.php?page=" . urlencode($relativePath) . "\">$formName</a></li>";
                        }
                    }
                }
            }
            listFiles('forms');
            ?>
        </ul>
    </nav>
    <main id="main-content" class="main-content">
        <?php
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            $page = str_replace('../', '', $page); // Sicherheit: Entfernt relative Pfadangaben
            $page = 'forms/' . ltrim($page, '/'); // Pfadbereinigung und relativer Pfad
            echo "<pre>Requested page: $page</pre>"; // Debugging-Ausgabe
            if (file_exists($page) && pathinfo($page, PATHINFO_EXTENSION) === 'php') {
                include($page);
            } else {
                echo '<p>Seite nicht gefunden</p>';
                echo "<pre>Page not found: $page</pre>"; // Debugging-Ausgabe
            }
        } else {
            include('forms/form1.php');
        }
        ?>
    </main>
    <footer>
        <p id="disclaimer"><?= $texts['disclaimer']; ?></p>
    </footer>
</body>
</html>
