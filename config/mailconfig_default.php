<?php
return [
    'host' => 'SERVER',
    'SMTPAuth' => true,  // Boolean-Wert, daher ohne Anführungszeichen
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'port' => 587,
    'encryption' => '',  // Für keine Verschlüsselung leer lassen
    'from' => 'email@example.com',

    // Zertifikatsprüfung deaktivieren (für selbstsignierte oder ungültige Zertifikate)
    'SMTPOptions' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
        ],

        'defaultSubject' => 'Neue Formularübermittlung',
        'defaultRecipients' => ['mail@example.com'],
        'defaultPreText' => 'Hallo,<br>vielen Dank für Ihre Nachricht.<br>Folgende Daten wurden in unserem System gespeichert:<p>',
        'defaultPostText' => '</p> <br>Mit freundlichen Grüßen<br>Ihr Team',
    
// Optionale Server-URL, an den die Daten zusaetzlich als JSON gesendet werden
'server_url' => '',
];
?>
