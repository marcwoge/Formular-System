<?php

function buildSubmissionPdfDocument(array $postData, array $hiddenFields, array $textsConfig = []): array
{
    $createdAt = date('d.m.Y H:i:s');

    return [
        'summary_left' => [
            'Formularausgabe',
            'Titel: ' . normalizeSubmittedValue($postData['form_title'] ?? 'Formular'),
            'Meldungsnummer: ' . normalizeSubmittedValue($postData['submission_number'] ?? ''),
            'Erstellt am: ' . $createdAt,
        ],
        'summary_right' => array_values(array_filter([
            'Name: ' . normalizeSubmittedValue($postData['current_windows_display_name'] ?? ''),
            'Username: ' . normalizeSubmittedValue($postData['current_windows_user'] ?? ''),
            'Email: ' . normalizeSubmittedValue($postData['current_windows_email'] ?? ''),
        ], static function ($line) {
            return !preg_match('/:\s*$/', $line);
        })),
        'rows' => buildSubmissionPdfRows($postData, $hiddenFields),
        'footer_lines' => normalizePdfFooterLines($textsConfig),
    ];
}

function buildSubmissionPdfRows(array $postData, array $hiddenFields): array
{
    $excludedFields = array_merge($hiddenFields, [
        'form_title',
        'submission_number',
        'current_windows_display_name',
        'current_windows_user',
        'current_windows_email',
    ]);

    $rows = [];

    foreach ($postData as $key => $value) {
        if (in_array($key, $excludedFields, true)) {
            continue;
        }

        $rows[] = [
            'label' => formatFieldLabel($key),
            'value' => normalizeSubmittedValue($value),
        ];
    }

    return $rows;
}

function normalizePdfFooterLines(array $textsConfig): array
{
    $footerLines = $textsConfig['pdf_footer_lines'] ?? [];

    if (is_string($footerLines) && trim($footerLines) !== '') {
        $footerLines = preg_split('/\r\n|\r|\n/', $footerLines);
    }

    if (!is_array($footerLines)) {
        $footerLines = [];
    }

    return array_values(array_filter(array_map(static function ($line) {
        return trim((string) $line);
    }, $footerLines), static function ($line) {
        return $line !== '';
    }));
}
