<?php
return [
    // Optional: Wenn der Webserver nur den Windows-Login liefert,
    // kann daraus eine Mailadresse aufgebaut werden, z. B. vorname.nachname@example.local
    'email_domain' => null,

    'field_labels' => [
        'section_title' => 'Angemeldeter Windows-Benutzer',
        'display_name' => 'Benutzername',
        'username' => 'Windows-Login',
        'email' => 'E-Mail-Adresse',
    ],

    'ldap' => [
        // Auf true setzen, wenn Name und Mail per LDAP/Active Directory aufgelöst werden sollen.
        'enabled' => false,
        'host' => 'ldap://dc.example.local',
        'port' => 389,
        'base_dn' => 'DC=example,DC=local',

        // Optionales Service-Konto für den LDAP-Bind.
        'bind_dn' => null,
        'bind_password' => null,

        // Standard für Active Directory anhand des Windows-Logins.
        'user_filter' => '(sAMAccountName=%s)',
        'display_name_attribute' => 'displayName',
        'mail_attribute' => 'mail',
    ],
];
