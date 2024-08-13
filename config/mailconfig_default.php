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
    ]
];
?>
