<?php
function sanitizePageParameter($page) {
    // Dekodieren der URL (z.B. '%2F' zu '/')
    $page = urldecode($page);

    // Entfernt eventuelle '../' für zusätzliche Sicherheit
    $page = str_replace('../', '', $page);

    return $page;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formular Portal</title>
    <link rel="stylesheet" href="styles.css">
    <script src="validation.js" defer></script>
    <?php
    $googleApiConfig = require __DIR__ . '/config/googleapi.php';
    $texts = require __DIR__ . '/config/texts.php';
    echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $googleApiConfig['api_key'] . '&libraries=places" defer></script>';
    ?>
    <script src="multi-step.js"></script>
    <script src="form-options.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const forms = document.querySelectorAll('.validated-form');

        forms.forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const formData = new FormData(form);

                fetch('process_form.php', {
                    method: 'POST',
                    body: formData
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
    <?php
    $navType = isset($_GET['nav']) ? $_GET['nav'] : '';

    if ($navType === 'wiki') {
        // Keine Header- oder Navigationselemente anzeigen, nur das Formular
    } else {
        // Zeige Header und Navigation nur, wenn nav=true oder nav leer ist
        if ($navType !== 'wiki') {
            echo '<header id="banner">';
            if ($navType === 'true') {
                echo '<button class="menu-toggle" onclick="toggleMenu()">☰</button>';
            }
            echo '<h1>Formular Portal</h1>';
            echo '<img src="img/logo.png" alt="Logo">';
            echo '</header>';
        }

        if ($navType === 'true') {
            echo '<nav id="side-menu" class="side-menu">';
            echo '<h2>Formulare</h2>';
            echo '<ul>';
            function listFiles($dir, $relativeDir = '', $navType = 'true') {
                $files = scandir($dir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        $fullPath = $dir . '/' . $file;
                        $relativePath = $relativeDir === '' ? $file : $relativeDir . '/' . $file;
                        if (is_dir($fullPath)) {
                            echo "<li class=\"folder\">$file<ul class=\"nested\">";
                            listFiles($fullPath, $relativePath, $navType);
                            echo '</ul></li>';
                        } else if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                            $formName = pathinfo($file, PATHINFO_FILENAME);
                            $navParam = $navType !== '' ? '&nav=' . urlencode($navType) : '';
                            echo "<li><a href=\"index.php?page=" . urlencode($relativePath) . $navParam . "\">$formName</a></li>";
                        }
                    }
                }
            }
            listFiles('forms', '', $navType);
            echo '</ul>';
            echo '</nav>';
        }
    }
    ?>

    <main id="main-content" class="main-content">
        <?php
        if (isset($_GET['page'])) {
            $page = sanitizePageParameter($_GET['page']);
            $page = 'forms/' . ltrim($page, '/');
            echo "<pre>Requested page: $page</pre>";
            if (file_exists($page) && pathinfo($page, PATHINFO_EXTENSION) === 'php') {
                include($page);
            } else {
                echo '<p>Seite nicht gefunden</p>';
                echo "<pre>Page not found: $page</pre>";
            }
        } else {
            include('forms/form1.php');
        }
        ?>
    </main>

    <?php if ($navType !== 'wiki'): ?>
    <footer>
        <p id="disclaimer"><?= $texts['disclaimer']; ?></p>
    </footer>
    <?php endif; ?>
</body>
</html>
