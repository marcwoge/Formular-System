<?php

function buildSubmissionPdfDocument(array $postData, array $hiddenFields): array
{
    $createdAt = date('d.m.Y H:i:s');

    return [
        'summary_left' => [
            'Formularausgabe',
            'Titel: ' . normalizeSubmittedValue($postData['form_title'] ?? 'Formular'),
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
    ];
}

function buildSubmissionPdfRows(array $postData, array $hiddenFields): array
{
    $excludedFields = array_merge($hiddenFields, [
        'form_title',
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
