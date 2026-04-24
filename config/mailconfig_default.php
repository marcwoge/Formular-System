<?php

return [
    'host' => 'SERVER',
    'SMTPAuth' => true,
    'username' => 'USERNAME',
    'password' => 'PASSWORD',
    'port' => 587,
    'encryption' => '',
    'from' => 'email@example.com',
    'timeout' => 10,
    'smtp_keepalive' => true,
    'smtp_auto_tls' => true,
    'SMTPOptions' => [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ],
    'defaultSubject' => 'Neue Formularuebermittlung',
    'defaultRecipients' => ['mail@example.com'],
    'defaultPreText' => 'Hallo,<br>vielen Dank fuer Ihre Nachricht.<br>Folgende Daten wurden in unserem System gespeichert:<p>',
    'defaultPostText' => '</p><br>Mit freundlichen Gruessen<br>Ihr Team',
    'server_url' => '',
];
