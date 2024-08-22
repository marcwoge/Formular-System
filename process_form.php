<?php
mb_internal_encoding('UTF-8');

// Laden der Mailserver- und Server-Konfiguration
$mailConfig = require __DIR__ . '/config/mailconfig.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'include/PHPMailer/src/Exception.php';
require 'include/PHPMailer/src/PHPMailer.php';
require 'include/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verzeichnis für den Dateiupload prüfen und erstellen
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileUrl = '';

    if (!empty($_FILES['file']['name'])) {
        // Ursprünglichen Dateinamen bereinigen und Dateiendung extrahieren
        $originalFilename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
        $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename); // Sonderzeichen durch _ ersetzen
        $timestamp = date('YmdHis'); // Zeitstempel im Format YYYYMMDDHHMMSS

        // Neuen Dateinamen mit Endung generieren
        $uploadFile = $uploadDir . $timestamp . '_' . $cleanFilename . '.' . $fileExtension;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            $fileUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $uploadFile;
        } else {
            echo "Datei konnte nicht hochgeladen werden.";
        }
    }

    // POST-Daten verarbeiten
    $postData = $_POST;

    // Link zur hochgeladenen Datei hinzufügen
    if ($fileUrl) {
        $postData['file_link'] = $fileUrl;
    }

    // Debugging-Ausgabe des POST-Arrays
    echo "<pre>";
    var_dump($postData);
    echo '</pre>';

    // Standardwerte für E-Mail-Konfiguration
    $defaultSubject = $mailConfig['defaultSubject'];
    $defaultRecipients = $mailConfig['defaultRecipients'];
    $defaultPreText = $mailConfig['defaultPreText'];
    $defaultPostText = $mailConfig['defaultPostText'];

    // E-Mail Details aus den POST-Daten oder Standardwerte verwenden
    $emailSubject = isset($postData['email_subject']) ? $postData['email_subject'] : $defaultSubject;
    $emailRecipients = isset($postData['email_recipients']) ? explode(',', $postData['email_recipients']) : $defaultRecipients;
    $emailPreText = isset($postData['email_pretext']) ? $postData['email_pretext'] : $defaultPreText;
    $emailPostText = isset($postData['email_posttext']) ? $postData['email_posttext'] : $defaultPostText;
    $userEmail = isset($postData['email']) ? $postData['email'] : '';

    // E-Mail Text zusammenstellen
    $emailBody = $emailPreText;

    // Definiere die Namen der versteckten Felder, die nicht in die E-Mail aufgenommen werden sollen
    $hiddenFields = ['file_attachments', 'email_subject', 'email_recipients', 'email_pretext', 'email_posttext'];

    foreach ($postData as $key => $value) {
        if (!in_array($key, $hiddenFields)) {
            if (is_array($value)) {
                $value = implode(', ', $value); // Array zu String konvertieren für E-Mail
            }
            $emailBody .= "$key: $value\n";
        }
    }

    $emailBody .= $emailPostText;

    // PHPMailer initialisieren
    $mail = new PHPMailer(true);

    try {
        // Server-Einstellungen
        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->SMTPAuth = $mailConfig['SMTPAuth'];
        $mail->Username = $mailConfig['username'];
        $mail->Password = $mailConfig['password'];
        $mail->SMTPSecure = $mailConfig['encryption'];
        $mail->Port = $mailConfig['port'];
        $mail->CharSet = 'UTF-8';

        // Zertifikatsprüfung deaktivieren (für selbstsignierte oder ungültige Zertifikate)
        $mail->SMTPOptions = $mailConfig['SMTPOptions'];

        // Erste E-Mail an den Empfängerkreis im HTML-Formular
        $mail->setFrom($mailConfig['from']);
        foreach ($emailRecipients as $recipient) {
            $mail->addAddress(trim($recipient));
        }

        // Dateien aus dem /files/ Ordner hinzufügen, falls angegeben
        if (isset($postData['file_attachments']) && is_array($postData['file_attachments'])) {
            foreach ($postData['file_attachments'] as $file) {
                $filePath = __DIR__ . '/files/' . basename($file);
                if (file_exists($filePath)) {
                    $mail->addAttachment($filePath);
                }
            }
        }

        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = nl2br($emailBody);

        // Erste E-Mail senden
        $mail->send();
        $mail->clearAddresses();

        // Zweite E-Mail an die im Formular angegebene E-Mail-Adresse des Benutzers
        if (!empty($userEmail)) {
            $mail->addAddress($userEmail);
            $mail->send();
        }

        echo 'E-Mails wurden gesendet.';
    } catch (Exception $e) {
        echo "E-Mail konnte nicht gesendet werden. Fehler: {$mail->ErrorInfo}";
    }

    // Server URL aus der Konfiguration
    $url = $mailConfig['server_url'];

    // cURL initialisieren
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // JSON unverändert senden
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Ausführen und Antwort speichern
    $response = curl_exec($ch);
    curl_close($ch);

    // Antwort an den Benutzer
    echo "Formulardaten wurden übermittelt.";
} else {
    echo "Ungültige Anforderung.";
}
?>
