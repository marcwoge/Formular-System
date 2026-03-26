<?php
return [
    'email_domain' => null,
    'field_labels' => [
        'section_title' => 'Angemeldeter Windows-Benutzer',
        'display_name' => 'Benutzername',
        'username' => 'Windows-Login',
        'email' => 'E-Mail-Adresse',
    ],
    'ldap' => [
        'enabled' => false,
        'host' => 'ldap://dc.example.local',
        'port' => 389,
        'base_dn' => 'DC=example,DC=local',
        'bind_dn' => null,
        'bind_password' => null,
        'user_filter' => '(sAMAccountName=%s)',
        'display_name_attribute' => 'displayName',
        'mail_attribute' => 'mail',
    ],
];
