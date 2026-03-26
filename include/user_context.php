<?php

require_once __DIR__ . '/config_loader.php';

function resolveCurrentUserContext(array $config = []): array
{
    $identityCandidates = [
        'REMOTE_USER',
        'AUTH_USER',
        'LOGON_USER',
        'PHP_AUTH_USER',
        'REDIRECT_REMOTE_USER',
        'HTTP_X_REMOTE_USER',
        'HTTP_X_FORWARDED_USER',
        'USERNAME',
        'USER',
    ];

    $displayNameCandidates = [
        'AUTHENTICATED_DISPLAY_NAME',
        'DISPLAY_NAME',
        'REMOTE_DISPLAY_NAME',
        'HTTP_X_REMOTE_USER_NAME',
    ];

    $emailCandidates = [
        'AUTHENTICATED_EMAIL',
        'REMOTE_EMAIL',
        'MAIL',
        'HTTP_X_REMOTE_USER_EMAIL',
    ];

    $rawIdentity = firstAvailableValue($identityCandidates);
    $normalizedIdentity = normalizeUserIdentity($rawIdentity);

    $context = [
        'available' => false,
        'raw_identity' => $rawIdentity,
        'username' => $normalizedIdentity['username'],
        'domain' => $normalizedIdentity['domain'],
        'display_name' => firstAvailableValue($displayNameCandidates),
        'email' => firstAvailableValue($emailCandidates),
        'labels' => getUserContextLabels($config),
    ];

    if ($context['username'] !== '') {
        $ldapContext = resolveUserContextFromLdap($context['username'], $config);
        if (!empty($ldapContext['display_name']) && empty($context['display_name'])) {
            $context['display_name'] = $ldapContext['display_name'];
        }
        if (!empty($ldapContext['email']) && empty($context['email'])) {
            $context['email'] = $ldapContext['email'];
        }
    }

    if (empty($context['email']) && !empty($config['email_domain']) && $context['username'] !== '') {
        $context['email'] = $context['username'] . '@' . ltrim((string) $config['email_domain'], '@');
    }

    if (empty($context['display_name']) && $context['username'] !== '') {
        $context['display_name'] = prettifyUsername($context['username']);
    }

    $context['available'] = $context['username'] !== '' || $context['display_name'] !== '' || $context['email'] !== '';

    return $context;
}

function getUserContextLabels(array $config): array
{
    $defaults = [
        'section_title' => 'Angemeldeter Windows-Benutzer',
        'display_name' => 'Benutzername',
        'username' => 'Windows-Login',
        'email' => 'E-Mail-Adresse',
    ];

    if (!isset($config['field_labels']) || !is_array($config['field_labels'])) {
        return $defaults;
    }

    return array_merge($defaults, $config['field_labels']);
}

function firstAvailableValue(array $keys): string
{
    foreach ($keys as $key) {
        if (isset($_SERVER[$key]) && trim((string) $_SERVER[$key]) !== '') {
            return trim((string) $_SERVER[$key]);
        }

        $envValue = getenv($key);
        if ($envValue !== false && trim((string) $envValue) !== '') {
            return trim((string) $envValue);
        }
    }

    return '';
}

function normalizeUserIdentity(string $identity): array
{
    $identity = trim($identity);
    if ($identity === '') {
        return [
            'username' => '',
            'domain' => '',
        ];
    }

    if (strpos($identity, '\\') !== false) {
        $parts = explode('\\', $identity, 2);
        return [
            'domain' => trim($parts[0]),
            'username' => trim($parts[1]),
        ];
    }

    if (strpos($identity, '@') !== false) {
        $parts = explode('@', $identity, 2);
        return [
            'domain' => trim($parts[1]),
            'username' => trim($parts[0]),
        ];
    }

    return [
        'domain' => '',
        'username' => $identity,
    ];
}

function prettifyUsername(string $username): string
{
    $parts = preg_split('/[._-]+/', $username);
    $parts = array_filter($parts, static function ($part) {
        return trim((string) $part) !== '';
    });

    if (empty($parts)) {
        return $username;
    }

    $parts = array_map(static function ($part) {
        return mb_convert_case($part, MB_CASE_TITLE, 'UTF-8');
    }, $parts);

    return implode(' ', $parts);
}

function resolveUserContextFromLdap(string $username, array $config): array
{
    $ldapConfig = $config['ldap'] ?? [];

    if (empty($ldapConfig['enabled']) || !function_exists('ldap_connect')) {
        return [];
    }

    $host = $ldapConfig['host'] ?? '';
    $baseDn = $ldapConfig['base_dn'] ?? '';

    if ($host === '' || $baseDn === '') {
        return [];
    }

    $connection = @ldap_connect($host, $ldapConfig['port'] ?? 389);
    if ($connection === false) {
        return [];
    }

    ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);

    $bindDn = $ldapConfig['bind_dn'] ?? null;
    $bindPassword = $ldapConfig['bind_password'] ?? null;
    $bindSuccess = $bindDn ? @ldap_bind($connection, $bindDn, (string) $bindPassword) : @ldap_bind($connection);

    if ($bindSuccess === false) {
        ldap_unbind($connection);
        return [];
    }

    $filterTemplate = $ldapConfig['user_filter'] ?? '(sAMAccountName=%s)';
    $escapedUsername = function_exists('ldap_escape')
        ? ldap_escape($username, '', LDAP_ESCAPE_FILTER)
        : addcslashes($username, '\\*()' . chr(0));
    $filter = sprintf($filterTemplate, $escapedUsername);

    $displayNameAttribute = $ldapConfig['display_name_attribute'] ?? 'displayName';
    $mailAttribute = $ldapConfig['mail_attribute'] ?? 'mail';

    $search = @ldap_search($connection, $baseDn, $filter, [$displayNameAttribute, $mailAttribute]);
    if ($search === false) {
        ldap_unbind($connection);
        return [];
    }

    $entries = ldap_get_entries($connection, $search);
    ldap_unbind($connection);

    if (empty($entries['count'])) {
        return [];
    }

    $entry = $entries[0];
    $displayNameKey = strtolower($displayNameAttribute);
    $mailKey = strtolower($mailAttribute);

    return [
        'display_name' => $entry[$displayNameKey][0] ?? '',
        'email' => $entry[$mailKey][0] ?? '',
    ];
}
