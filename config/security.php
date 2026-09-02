<?php

return [
    'two_factor' => [
        // HIPAA/PHC compliance expects MFA on admin/accountant accounts (full
        // patient and financial access on password alone otherwise), so this
        // defaults to enforced. Only disable for environments where the
        // 2FA setup step is genuinely unwanted (e.g. local development).
        'enforced' => (bool) env('SECURITY_ENFORCE_TWO_FACTOR', true),
    ],

    'breach' => [
        'failed_login_threshold' => (int) env('SECURITY_FAILED_LOGIN_THRESHOLD', 5),
        'failed_login_window_minutes' => (int) env('SECURITY_FAILED_LOGIN_WINDOW_MINUTES', 10),
        'bulk_patient_access_threshold' => (int) env('SECURITY_BULK_PATIENT_ACCESS_THRESHOLD', 20),
        'bulk_patient_window_minutes' => (int) env('SECURITY_BULK_PATIENT_WINDOW_MINUTES', 5),
        'notification_emails' => array_values(array_filter(array_map(
            static fn (string $email): string => trim($email),
            explode(',', (string) env('SECURITY_CONTACT_EMAILS', (string) env('BACKUP_SECURITY_EMAIL', env('MAIL_FROM_ADDRESS', 'hello@example.com'))))
        ))),
    ],
];
