<?php
mb_internal_encoding('UTF-8');

require_once __DIR__ . '/include/config_loader.php';
require_once __DIR__ . '/include/user_context.php';
require_once __DIR__ . '/include/simple_pdf.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$mailConfig = loadProjectConfig('mailconfig');
$userContextConfig = loadProjectConfig('usercontext');

require 'include/PHPMailer/src/Exception.php';
require 'include/PHPMailer/src/PHPMailer.php';
require 'include/PHPMailer/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            exit('Datei konnte nicht hochgeladen werden.');
        }
    }

    $postData = $_POST;
    enrichPostDataWithUserContext($postData, $userContext);

    if ($fileUrl !== '') {
        $postData['file_link'] = $fileUrl;
    }

    if (empty($postData['email']) && !empty($userContext['email'])) {
        $postData['email'] = $userContext['email'];
    }

    $postData['formdatetime'] = round(microtime(true) * 1000);

    $saveDir = 'form_submissions/';
    if (!is_dir($saveDir)) {
        mkdir($saveDir, 0777, true);
    }

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
    $postData['current_windows_display_name'] = $userContext['display_name'] ?? '';
    $postData['current_windows_user'] = $userContext['username'] ?? '';
    $postData['current_windows_email'] = $userContext['email'] ?? '';
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
    $sanitizedTitle = preg_replace('/[^A-Za-z0-9_-]/', '_', $formTitle);
    $sanitizedTitle = trim((string) $sanitizedTitle, '_');

    if ($sanitizedTitle === '') {
        $sanitizedTitle = 'formular';
    }

    return $timestamp . '_' . $sanitizedTitle . '.pdf';
}

function createSubmissionPdf(string $pdfPath, array $postData, array $hiddenFields): void
{
    $lines = [
        'Formularausgabe',
        'Titel: ' . normalizeSubmittedValue($postData['form_title'] ?? 'Formular'),
        'Erstellt am: ' . date('d.m.Y H:i:s'),
        '',
    ];

    foreach ($postData as $key => $value) {
        if (in_array($key, $hiddenFields, true)) {
            continue;
        }

        $lines[] = formatFieldLabel($key) . ': ' . normalizeSubmittedValue($value);
    }

    $generator = new SimplePdfGenerator();
    file_put_contents($pdfPath, $generator->render($lines));
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
    return ucwords(str_replace('_', ' ', $key));
}
