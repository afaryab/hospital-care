<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Folder Zip Download Thresholds
    |--------------------------------------------------------------------------
    |
    | Folders at or under these limits are zipped synchronously and streamed
    | back immediately. Anything larger is built by a queued job instead, so
    | a large-but-legitimate folder doesn't hit a web request timeout.
    |
    */

    'zip' => [
        'sync_max_bytes' => (int) env('DMS_ZIP_SYNC_MAX_BYTES', 100 * 1024 * 1024), // 100MB
        'sync_max_files' => (int) env('DMS_ZIP_SYNC_MAX_FILES', 300),

        /*
        | Zip-bomb defense for *uploaded* zip files that get extracted into
        | a folder. Every entry is inspected with ZipArchive::statIndex()
        | before anything is extracted.
        */
        'max_entries' => (int) env('DMS_ZIP_MAX_ENTRIES', 2000),
        'max_uncompressed_bytes' => (int) env('DMS_ZIP_MAX_UNCOMPRESSED_BYTES', 500 * 1024 * 1024), // 500MB
        'max_compression_ratio' => (int) env('DMS_ZIP_MAX_COMPRESSION_RATIO', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Sharing
    |--------------------------------------------------------------------------
    |
    | Documents at or under this size are attached directly to the share
    | email. Larger documents get a signed, time-boxed download link instead.
    |
    */

    'email_attachment_max_bytes' => (int) env('DMS_EMAIL_ATTACHMENT_MAX_BYTES', 10 * 1024 * 1024), // 10MB

    'share_link_expires_days' => (int) env('DMS_SHARE_LINK_EXPIRES_DAYS', 7),

    /*
    |--------------------------------------------------------------------------
    | Scratch Space
    |--------------------------------------------------------------------------
    |
    | Working directory for zip building/extraction, relative to the default
    | filesystem disk's root — mirrors storage/app/pdf-tmp's existing role.
    |
    */

    'tmp_path' => env('DMS_TMP_PATH', 'dms-tmp'),
];
