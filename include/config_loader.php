<?php

function loadProjectConfig(string $name): array
{
    $configDir = __DIR__ . '/../config/';
    $primaryConfig = $configDir . $name . '.php';
    $defaultConfig = $configDir . $name . '_default.php';

    if (file_exists($primaryConfig)) {
        return require $primaryConfig;
    }

    if (file_exists($defaultConfig)) {
        return require $defaultConfig;
    }

    throw new RuntimeException("Konfigurationsdatei '{$name}' wurde nicht gefunden.");
}
