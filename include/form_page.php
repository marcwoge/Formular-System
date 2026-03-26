<?php

function loadFormPage(string $pagePath, string $defaultTitle): array
{
    $defaultConfig = [
        'title' => $defaultTitle,
        'show_header' => true,
        'header_title' => null,
        'show_logo' => true,
    ];
    $formPageConfig = $defaultConfig;

    ob_start();
    include $pagePath;
    $content = ob_get_clean();

    if (!is_array($formPageConfig)) {
        $formPageConfig = [];
    }

    $formPageConfig = array_merge($defaultConfig, $formPageConfig);
    $detectedFormTitle = extractHiddenInputValue($content, 'form_title');
    $formPageConfig['title'] = normalizeFormPageTitle($formPageConfig['title'], $detectedFormTitle ?: $defaultTitle);
    $formPageConfig['header_title'] = normalizeNullableString($formPageConfig['header_title']);
    $formPageConfig['show_header'] = (bool) $formPageConfig['show_header'];
    $formPageConfig['show_logo'] = (bool) $formPageConfig['show_logo'];

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

function extractHiddenInputValue(string $content, string $name): ?string
{
    $pattern = '/<input\b[^>]*type\s*=\s*["\']hidden["\'][^>]*name\s*=\s*["\']' . preg_quote($name, '/') . '["\'][^>]*value\s*=\s*["\']([^"\']*)["\']/i';
    if (!preg_match($pattern, $content, $matches)) {
        return null;
    }

    return html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
}
