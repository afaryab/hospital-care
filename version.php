<?php
function getApplicationFolder()
{
    $defaultAppFolder = 'application';
    $currentVersion = '1';

    $versionHistory = getVersionsHistory();

    foreach ($versionHistory as $history) {
        if (is_dir($history['app_folder'])) {
            return $history['app_folder'];
        }
    }
    return $defaultAppFolder;
}

function getVersionsHistory(){

    return [
        [
            'version' => 1,
            'app_folder' => 'application'
        ],
        [
            'version' => 0,
            'app_folder' => 'application'
        ]
    ];
}

?>