<?php
require_once __DIR__ . '/include/config_loader.php';
require_once __DIR__ . '/include/form_page.php';
require_once __DIR__ . '/include/user_context.php';

function sanitizePageParameter($page)
{
    $page = urldecode($page);
    $page = str_replace('../', '', $page);

    return $page;
}

$formName = 'Formular Portal';
$pagePath = 'forms/start.php';
$requestedPagePath = $pagePath;
$pageContent = '';
$pageFound = true;
$pageConfig = [
    'title' => $formName,
    'show_header' => true,
    'header_title' => null,
    'show_logo' => true,
];

if (isset($_GET['page'])) {
    $page = sanitizePageParameter($_GET['page']);
    $page = 'forms/' . ltrim($page, '/');
    $requestedPagePath = $page;

    if (file_exists($page) && pathinfo($page, PATHINFO_EXTENSION) === 'php') {
        $pagePath = $page;
        $formName = pathinfo($page, PATHINFO_FILENAME);
    } else {
        $pageFound = false;
    }
}

$navType = isset($_GET['nav']) ? $_GET['nav'] : '';

if (empty($navType) && !isset($_GET['page'])) {
    $queryParams = $_GET;
    $queryParams['nav'] = 'true';
    $newQueryString = http_build_query($queryParams);
    header("Location: index.php?$newQueryString");
    exit;
}

$googleApiConfig = loadProjectConfig('googleapi');
$texts = loadProjectConfig('texts');
$userContextConfig = loadProjectConfig('usercontext');
$currentUserContext = resolveCurrentUserContext($userContextConfig);
$frontendUserContext = [
    'available' => $currentUserContext['available'],
    'username' => $currentUserContext['username'],
    'display_name' => $currentUserContext['display_name'],
    'email' => $currentUserContext['email'],
    'labels' => $currentUserContext['labels'],
];

if ($pageFound) {
    $loadedPage = loadFormPage($pagePath, $formName);
    $pageContent = $loadedPage['content'];
    $pageConfig = $loadedPage['config'];
    $formName = $pageConfig['title'];
} else {
    $pageContent = '<p>Seite nicht gefunden</p><pre>Page not found: ' . htmlspecialchars($requestedPagePath, ENT_QUOTES, 'UTF-8') . '</pre>';
}

$shouldShowHeader = $navType !== 'wiki' && $pageConfig['show_header'];
$shouldShowFooter = $navType !== 'wiki' && $navType !== '';
$headerTitle = $pageConfig['header_title'] ?? $formName;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars('Formular Portal: ' . $formName, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="styles.css">
    <script>
    window.formSystemUserContext = <?= json_encode($frontendUserContext, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="validation.js" defer></script>
    <?php if (!empty($googleApiConfig['api_key'])): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= urlencode($googleApiConfig['api_key']) ?>&libraries=places" defer></script>
    <?php endif; ?>
    <script src="menu.js" defer></script>
    <script src="multi-step.js" defer></script>
    <script src="form-options.js" defer></script>
    <script src="user-context.js" defer></script>
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
                    console.log('Server Response:', data);
                    if (data.trim() === 'success') {
                        alert('Die Daten wurden erfolgreich übertragen.');
                        window.location.href = window.location.pathname + window.location.search;
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
<body class="<?= $shouldShowHeader ? 'has-page-header' : 'no-page-header' ?> <?= $shouldShowFooter ? 'has-page-footer' : 'no-page-footer' ?>">
    <?php
    if ($shouldShowHeader) {
        echo '<header id="banner">';
        echo '<button class="menu-toggle" onclick="toggleMenu()">☰</button>';
        echo '<h1>' . htmlspecialchars($headerTitle, ENT_QUOTES, 'UTF-8') . '</h1>';
        if ($pageConfig['show_logo']) {
            echo '<img src="img/logo.png" alt="Logo">';
        }
        echo '</header>';
    }

    if ($navType === 'true') {
        echo '<nav id="side-menu" class="side-menu">';
        echo '<h2>Formulare</h2>';
        echo '<ul>';

        function listFiles($dir, $relativeDir = '', $navType = 'true')
        {
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $fullPath = $dir . '/' . $file;
                    $relativePath = $relativeDir === '' ? $file : $relativeDir . '/' . $file;
                    if (is_dir($fullPath)) {
                        echo "<li class=\"folder\">$file<ul class=\"nested\">";
                        listFiles($fullPath, $relativePath, $navType);
                        echo '</ul></li>';
                    } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $currentFormName = pathinfo($file, PATHINFO_FILENAME);
                        $navParam = $navType !== '' ? '&nav=' . urlencode($navType) : '';
                        echo "<li><a href=\"index.php?page=" . urlencode($relativePath) . $navParam . "\">$currentFormName</a></li>";
                    }
                }
            }
        }

        listFiles('forms', '', $navType);
        echo '</ul>';
        echo '</nav>';
    }
    ?>

    <main id="main-content" class="main-content<?= $shouldShowHeader ? '' : ' main-content-no-header' ?><?= $shouldShowFooter ? '' : ' main-content-no-footer' ?>">
        <?= $pageContent ?>
    </main>

    <?php if ($shouldShowFooter): ?>
    <footer>
        <p id="disclaimer"><?= $texts['disclaimer']; ?></p>
    </footer>
    <?php endif; ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function sendHeight() {
            var height = document.body.scrollHeight;
            window.parent.postMessage({ type: 'resizeIframe', height: height }, '*');
        }

        window.onload = sendHeight;
        window.onresize = sendHeight;
    });
    </script>
</body>
</html>
