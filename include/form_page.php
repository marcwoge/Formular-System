<?php

function loadFormPage(string $pagePath, string $defaultTitle): array
{
    $defaultConfig = [
        'title' => $defaultTitle,
        'show_header' => true,
        'show_footer' => true,
        'header_title' => null,
        'show_logo' => true,
        'footer_html' => null,
    ];
    $formPageConfig = $defaultConfig;

    ob_start();
    include $pagePath;
    $content = ob_get_clean();

    if (!is_array($formPageConfig)) {
        $formPageConfig = [];
    }

    $formPageConfig = array_merge($defaultConfig, $formPageConfig);
    $formPageConfig['title'] = normalizeFormPageTitle($formPageConfig['title'], $defaultTitle);
    $formPageConfig['header_title'] = normalizeNullableString($formPageConfig['header_title']);
    $formPageConfig['show_header'] = (bool) $formPageConfig['show_header'];
    $formPageConfig['show_footer'] = (bool) $formPageConfig['show_footer'];
    $formPageConfig['show_logo'] = (bool) $formPageConfig['show_logo'];
    $formPageConfig['footer_html'] = normalizeNullableString($formPageConfig['footer_html']);

    return [
        'content' => $content,
        'config' => $formPageConfig,
    ];
}

function normalizeFormPageTitle($value, string $fallback): string
{
    $value = trim((string) $value);

    return $value !== '' ? $value : $fallback;
}

function normalizeNullableString($value): ?string
{
    $value = trim((string) $value);

    return $value !== '' ? $value : null;
}
