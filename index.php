<?php
function sanitizePageParameter($page) {
    $page = urldecode($page);  // Dekodieren der URL
    $page = str_replace('../', '', $page);  // Entfernt eventuelle '../' für zusätzliche Sicherheit
    return $page;
}

$formName = "Formular Portal"; // Standardtitel

if (isset($_GET['page'])) {
    $page = sanitizePageParameter($_GET['page']);
    $page = 'forms/' . ltrim($page, '/');

    if (file_exists($page) && pathinfo($page, PATHINFO_EXTENSION) === 'php') {
        $formName = pathinfo($page, PATHINFO_FILENAME);
    }
}

$navType = isset($_GET['nav']) ? $_GET['nav'] : '';

if (empty($navType) && !isset($_GET['page'])) {
    // Wenn nav nicht gesetzt ist und keine page-Parameter vorhanden ist, leite zu nav=true um
    $queryParams = $_GET;
    $queryParams['nav'] = 'true';
    $newQueryString = http_build_query($queryParams);
    header("Location: index.php?$newQueryString");
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= "Formular Portal: " . $formName ?></title>
    <link rel="stylesheet" href="styles.css">
    <script src="validation.js" defer></script>
    <?php
    $googleApiConfig = require __DIR__ . '/config/googleapi.php';
    $texts = require __DIR__ . '/config/texts.php';
    echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . $googleApiConfig['api_key'] . '&libraries=places" defer></script>';
    ?>
    <script src="menu.js" defer></script>
    <script src="multi-step.js" defer></script>
    <script src="form-options.js" defer></script>
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
                    console.log('Server Response:', data);  // Füge diese Zeile hinzu
                    if (data.trim() === 'success') {  // Überprüfe auf den exakten Erfolgstext
                        alert('Die Daten wurden erfolgreich übertragen.');
                        window.location.href = window.location.pathname + window.location.search; // Seite neu laden
                    } else {
                        alert('Ein Fehler ist aufgetreten: ' + data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ein Fehler ist aufgetreten: ' + error);
                });
            });
        });
    });
    </script>
</head>
<body>
    <?php
    if ($navType !== 'wiki') {
        // Zeige Header und Navigation, wenn nav nicht 'wiki' ist
        echo '<header id="banner">';
        echo '<button class="menu-toggle" onclick="toggleMenu()">☰</button>';
        echo '<h1>Formular Portal: ' . htmlspecialchars($formName) . '</h1>';
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
            include('forms/start.php');
        }
        ?>
    </main>

    <?php if ($navType !== 'wiki' && $navType !== ''): ?>
    <footer>
        <p id="disclaimer"><?= $texts['disclaimer']; ?></p>
    </footer>
    <?php endif; ?>
    <script>
        function sendHeight() {
            var height = document.body.scrollHeight;
            window.parent.postMessage({ type: 'resizeIframe', height: height }, '*');
        }

        // Nur ausführen, wenn das Formular innerhalb eines iFrames geladen wird
        if (window !== window.parent) {
            window.onload = sendHeight;
            window.onresize = sendHeight;
        }
    </script>
</body>
</html>
