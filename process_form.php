<?php
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/include/config_loader.php';
require_once __DIR__ . '/include/pdf_template.php';
require_once __DIR__ . '/include/user_context.php';
require_once __DIR__ . '/include/simple_pdf.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$mailConfig = loadProjectConfig('mailconfig');
$textsConfig = loadProjectConfig('texts');
$userContextConfig = loadProjectConfig('usercontext');

require 'include/PHPMailer/src/Exception.php';
require 'include/PHPMailer/src/PHPMailer.php';
require 'include/PHPMailer/src/SMTP.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submissionFingerprint = buildSubmissionFingerprint($_POST, $_FILES);
    if (isDuplicateSubmission($submissionFingerprint)) {
        exit('success');
    }

    storeSubmissionGuard($submissionFingerprint, 'pending');
    $timestamp = date('YmdHis');
    $userContext = resolveCurrentUserContext($userContextConfig);
    $hiddenFields = getHiddenFields();

    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileUrl = '';

    if (!empty($_FILES['file']['name'])) {
        $originalFilename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $cleanFilename = preg_replace('/[^A-Za-z0-9_-]/', '_', $originalFilename);
        $uploadFile = $uploadDir . $timestamp . '_' . $cleanFilename . ($fileExtension !== '' ? '.' . $fileExtension : '');

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $fileUrl = $scheme . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/' . $uploadFile;
        } else {
            clearSubmissionGuard($submissionFingerprint);
            exit('Datei konnte nicht hochgeladen werden.');
        }
    }

    $saveDir = 'form_submissions/';
    if (!is_dir($saveDir)) {
        mkdir($saveDir, 0777, true);
    }

    $postData = $_POST;
    enrichPostDataWithUserContext($postData, $userContext);
    $postData = ['submission_number' => generateSubmissionNumber($saveDir, $textsConfig)] + $postData;

    if ($fileUrl !== '') {
        $postData['file_link'] = $fileUrl;
    }

    if (empty($postData['email']) && !empty($postData['current_windows_email'])) {
        $postData['email'] = $postData['current_windows_email'];
    } elseif (empty($postData['email']) && !empty($userContext['email'])) {
        $postData['email'] = $userContext['email'];
    }

    $postData['formdatetime'] = round(microtime(true) * 1000);

    $pdfFilename = buildPdfFilename($timestamp, $postData);
    $pdfPath = $saveDir . $pdfFilename;
    createSubmissionPdf($pdfPath, $postData, $hiddenFields);

    $jsonFilename = $saveDir . $timestamp . '_form_submission.json';
    file_put_contents($jsonFilename, json_encode($postData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $defaultSubject = $mailConfig['defaultSubject'];
    $defaultRecipients = $mailConfig['defaultRecipients'];
    $defaultPreText = $mailConfig['defaultPreText'];
    $defaultPostText = $mailConfig['defaultPostText'];

    $emailSubject = isset($postData['email_subject']) ? $postData['email_subject'] : $defaultSubject;
    $emailRecipients = isset($postData['email_recipients']) ? explode(',', $postData['email_recipients']) : $defaultRecipients;
    $emailPreText = isset($postData['email_pretext']) ? $postData['email_pretext'] : $defaultPreText;
    $emailPostText = isset($postData['email_posttext']) ? $postData['email_posttext'] : $defaultPostText;
    $userEmail = isset($postData['email']) ? trim((string) $postData['email']) : '';
    $emailSubject = buildSubmissionSubject($emailSubject, $postData);

    $emailBody = buildEmailBody($emailPreText, $emailPostText, $postData, $hiddenFields);
    $plainTextBody = buildPlainTextBody($emailPreText, $emailPostText, $postData, $hiddenFields);

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->SMTPAuth = $mailConfig['SMTPAuth'];
        $mail->Username = $mailConfig['username'];
        $mail->Password = $mailConfig['password'];
        $mail->SMTPSecure = $mailConfig['encryption'];
        $mail->Port = $mailConfig['port'];
        $mail->CharSet = 'UTF-8';
        $mail->SMTPOptions = $mailConfig['SMTPOptions'];

        $mail->setFrom($mailConfig['from']);

        foreach ($emailRecipients as $recipient) {
            $recipient = trim((string) $recipient);
            if ($recipient !== '') {
                $mail->addAddress($recipient);
            }
        }

        attachStaticFiles($mail, $postData);

        if (file_exists($pdfPath)) {
            $mail->addAttachment($pdfPath, basename($pdfPath));
        }

        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = $emailBody;
        $mail->AltBody = $plainTextBody;

        $mail->send();
        $mail->clearAddresses();

        if ($userEmail !== '') {
            $mail->addAddress($userEmail);
            $mail->send();
        }
    } catch (Exception $e) {
        clearSubmissionGuard($submissionFingerprint);
        exit("E-Mail konnte nicht gesendet werden. Fehler: {$mail->ErrorInfo}");
    }

    $url = $mailConfig['server_url'] ?? '';
    if (!empty($url)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    }

    storeSubmissionGuard($submissionFingerprint, 'success');
    exit('success');
}

echo 'Ungültige Anforderung.';

function getHiddenFields(): array
{
    return [
        'file_attachments',
        'email_subject',
        'email_recipients',
        'email_pretext',
        'email_posttext',
        'formdatetime',
    ];
}

function enrichPostDataWithUserContext(array &$postData, array $userContext): void
{
    mergeUserContextValue($postData, 'current_windows_display_name', $userContext['display_name'] ?? '');
    mergeUserContextValue($postData, 'current_windows_email', $userContext['email'] ?? '');
}

function attachStaticFiles(PHPMailer $mail, array $postData): void
{
    if (!isset($postData['file_attachments']) || !is_array($postData['file_attachments'])) {
        return;
    }

    foreach ($postData['file_attachments'] as $file) {
        $filePath = __DIR__ . '/files/' . basename((string) $file);
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath);
        }
    }
}

function buildPdfFilename(string $timestamp, array $postData): string
{
    $formTitle = isset($postData['form_title']) ? (string) $postData['form_title'] : 'formular';
    $submissionNumber = isset($postData['submission_number']) ? (string) $postData['submission_number'] : '';
    $sanitizedTitle = preg_replace('/[^A-Za-z0-9_-]/', '_', $formTitle);
    $sanitizedNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $submissionNumber);
    $sanitizedTitle = trim((string) $sanitizedTitle, '_');
    $sanitizedNumber = trim((string) $sanitizedNumber, '_');

    if ($sanitizedTitle === '') {
        $sanitizedTitle = 'formular';
    }

    $parts = [$timestamp];
    if ($sanitizedNumber !== '') {
        $parts[] = $sanitizedNumber;
    }
    $parts[] = $sanitizedTitle;

    return implode('_', $parts) . '.pdf';
}

function createSubmissionPdf(string $pdfPath, array $postData, array $hiddenFields): void
{
    global $textsConfig;

    $document = buildSubmissionPdfDocument($postData, $hiddenFields, $textsConfig);
    $document['logo_path'] = resolvePdfLogoPath();
    $generator = new SimplePdfGenerator();
    file_put_contents($pdfPath, $generator->renderSubmissionDocument($document));
}

function buildEmailBody(string $emailPreText, string $emailPostText, array $postData, array $hiddenFields): string
{
    $rows = '';

    foreach ($postData as $key => $value) {
        if (in_array($key, $hiddenFields, true)) {
            continue;
        }

        $rows .= '<tr><th>' . htmlspecialchars(formatFieldLabel($key), ENT_QUOTES, 'UTF-8') . '</th><td>' .
            nl2br(htmlspecialchars(normalizeSubmittedValue($value), ENT_QUOTES, 'UTF-8')) .
            '</td></tr>';
    }

    return '<p>' . $emailPreText . "</p><table border='1' cellpadding='5' cellspacing='0'>{$rows}</table><p>{$emailPostText}</p>";
}

function buildPlainTextBody(string $emailPreText, string $emailPostText, array $postData, array $hiddenFields): string
{
    $lines = [
        trim(html_entity_decode(strip_tags($emailPreText), ENT_QUOTES, 'UTF-8')),
        '',
    ];

    foreach ($postData as $key => $value) {
        if (in_array($key, $hiddenFields, true)) {
            continue;
        }

        $lines[] = formatFieldLabel($key) . ': ' . normalizeSubmittedValue($value);
    }

    $lines[] = '';
    $lines[] = trim(html_entity_decode(strip_tags($emailPostText), ENT_QUOTES, 'UTF-8'));

    return implode(PHP_EOL, $lines);
}

function normalizeSubmittedValue($value): string
{
    if (is_array($value)) {
        $parts = array_map('normalizeSubmittedValue', $value);

        return implode(', ', array_filter($parts, static function ($item) {
            return $item !== '';
        }));
    }

    return trim((string) $value);
}

function formatFieldLabel(string $key): string
{
    $labelMap = [
        'submission_number' => 'Meldungsnummer',
        'current_windows_display_name' => 'Name',
        'current_windows_user' => 'Username',
        'current_windows_email' => 'Email',
        'email' => 'Email',
        'formdatetime' => 'Erstellt am',
        'file_link' => 'Datei-Link',
    ];

    if (isset($labelMap[$key])) {
        return $labelMap[$key];
    }

    return ucwords(str_replace('_', ' ', $key));
}

function mergeUserContextValue(array &$postData, string $key, string $detectedValue): void
{
    $detectedValue = trim($detectedValue);

    if ($detectedValue !== '') {
        $postData[$key] = $detectedValue;
        return;
    }

    if (!isset($postData[$key])) {
        $postData[$key] = '';
    }
}

function buildSubmissionSubject(string $baseSubject, array $postData): string
{
    $submissionNumber = trim((string) ($postData['submission_number'] ?? ''));
    if ($submissionNumber === '') {
        return $baseSubject;
    }

    return '[' . $submissionNumber . '] ' . $baseSubject;
}

function generateSubmissionNumber(string $saveDir, array $textsConfig): string
{
    $prefix = trim((string) ($textsConfig['submission_number_prefix'] ?? 'MEL'));
    if ($prefix === '') {
        $prefix = 'MEL';
    }

    $datePart = date('Ymd');
    $counterFile = rtrim($saveDir, '/\\') . DIRECTORY_SEPARATOR . '.submission_number_counter.json';
    $fallbackNumber = sprintf('%s-%s-%04d', $prefix, $datePart, random_int(1, 9999));
    $handle = @fopen($counterFile, 'c+');

    if ($handle === false) {
        return $fallbackNumber;
    }

    if (!flock($handle, LOCK_EX)) {
        fclose($handle);
        return $fallbackNumber;
    }

    $rawState = stream_get_contents($handle);
    $state = is_string($rawState) && trim($rawState) !== '' ? json_decode($rawState, true) : [];

    if (!is_array($state) || ($state['date'] ?? '') !== $datePart) {
        $state = [
            'date' => $datePart,
            'counter' => 0,
        ];
    }

    $state['counter'] = ((int) ($state['counter'] ?? 0)) + 1;

    rewind($handle);
    ftruncate($handle, 0);
    fwrite($handle, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($handle);
    flock($handle, LOCK_UN);
    fclose($handle);

    return sprintf('%s-%s-%04d', $prefix, $datePart, $state['counter']);
}

function buildSubmissionFingerprint(array $postData, array $files): string
{
    $normalizedPostData = normalizeSubmissionFingerprintValue($postData);
    unset($normalizedPostData['formdatetime']);

    $normalizedFiles = [];
    foreach ($files as $key => $file) {
        $normalizedFiles[$key] = normalizeSubmissionFingerprintValue([
            'name' => $file['name'] ?? '',
            'size' => $file['size'] ?? 0,
            'error' => $file['error'] ?? 0,
        ]);
    }

    return hash('sha256', json_encode([
        'post' => $normalizedPostData,
        'files' => $normalizedFiles,
    ], JSON_UNESCAPED_UNICODE));
}

function normalizeSubmissionFingerprintValue($value)
{
    if (is_array($value)) {
        ksort($value);

        foreach ($value as $key => $item) {
            $value[$key] = normalizeSubmissionFingerprintValue($item);
        }

        return $value;
    }

    return trim((string) $value);
}

function isDuplicateSubmission(string $fingerprint): bool
{
    $guards = getSubmissionGuards();

    if (!isset($guards[$fingerprint])) {
        return false;
    }

    $guard = $guards[$fingerprint];

    return in_array($guard['state'] ?? '', ['pending', 'success'], true);
}

function storeSubmissionGuard(string $fingerprint, string $state): void
{
    $guards = getSubmissionGuards();
    $guards[$fingerprint] = [
        'state' => $state,
        'timestamp' => time(),
    ];

    $_SESSION['form_submission_guards'] = $guards;
}

function clearSubmissionGuard(string $fingerprint): void
{
    $guards = getSubmissionGuards();
    unset($guards[$fingerprint]);
    $_SESSION['form_submission_guards'] = $guards;
}

function getSubmissionGuards(): array
{
    $guards = $_SESSION['form_submission_guards'] ?? [];
    $expiresBefore = time() - 30;

    foreach ($guards as $fingerprint => $guard) {
        if (($guard['timestamp'] ?? 0) < $expiresBefore) {
            unset($guards[$fingerprint]);
        }
    }

    return $guards;
}

function resolvePdfLogoPath(): ?string
{
    $candidates = [
        __DIR__ . '/img/pdf_logo.png',
        __DIR__ . '/img/pdf_logo.jpg',
        __DIR__ . '/img/pdf_logo.jpeg',
        __DIR__ . '/img/logo.png',
        __DIR__ . '/img/logo.jpg',
        __DIR__ . '/img/logo.jpeg',
    ];

    foreach ($candidates as $candidate) {
        if (file_exists($candidate) && is_file($candidate)) {
            return $candidate;
        }
    }

    return null;
}
