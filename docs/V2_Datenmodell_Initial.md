# V2 Datenmodell Initial

## Identitaet und Rechte

- users
- identity_providers
- external_identities
- groups
- roles
- permissions
- user_group_assignments
- group_role_assignments
- user_role_assignments
- role_permission_assignments

## Formulare

- forms
- form_versions
- form_fields
- form_rules
- form_visibility_rules
- form_context_rules
- form_response_bindings

## Submissionen und Vorgangshistorie

- submissions
- submission_versions
- submission_field_values
- submission_messages
- submission_attachments
- submission_status_history
- submission_assignments
- submission_watchers

## Sicherheit und Audit

- audit_logs
- sensitive_field_policies
- encryption_key_metadata

## Integrationen

- webhook_endpoints
- mail_connectors
- directory_connectors
- oidc_connectors
- plugin_manifests
- plugin_installations
- plugin_configurations

## Kiosk und Branding

- kiosk_devices
- themes
- theme_assets

## Hinweise

- Submissionen werden als Vorgaenge modelliert.
- Formulare und Formdefinitionen sind versioniert.
- Antworten sind entweder freie Nachrichten oder strukturierte Antwortformulare.
- Feldwerte werden nicht als ein grosses JSON-Feld versteckt, sondern strukturiert und auswertbar abgelegt.
- Fuer sensible Formulare muessen Klartextsicht und verschluesselte Speicherung getrennt betrachtet werden.

## Lizenzierung

- license_connectors
- license_validations
- license_features
- branding_settings

## Hinweise zur Lizenzierung

- Lizenzstatus und letzte erfolgreiche Validierung muessen persistierbar sein.
- Free-Edition-Hinweise sollen aus Branding- und Lizenzstatus abgeleitet werden.
