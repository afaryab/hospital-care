<?php

class Processton{
    
    protected $_install_licence = 'Processton/InstallLicence';
    protected $_apply_licence = 'Processton/ApplyLicence';
    protected $_backupandrestore = 'Processton/BackupNRestore';
    protected $_take_backup = 'Processton/BackupNRestore/Backup';
    protected $_sync_process = 'Processton/ApplyLicence';
    
    function getNavigation(){
        return [
            'navigations' => [
                'Advance|ADV' => [
                    [
                        'label' => 'Backup & Restore',
                        'group' => 'ROLE_RECEPTION|ROLE_DOCTOR',
                        'perm' => 'Processton Backup & Restore',
                        'perm_group' => 'Processton Configuration',
                        'priority' => 'group',
                        'module' => 'Processton Backup & Configuration',
                        'icon' => 'fas fa-database',
                        'path' => $this->_backupandrestore,
                        'order' => 1
                    ],
                    [
                        'label' => 'Processton Sync',
                        'group' => 'ROLE_RECEPTION|ROLE_DOCTOR',
                        'perm' => 'List Payments',
                        'perm_group' => 'Patients Treatments',
                        'priority' => 'group',
                        'module' => 'my_payments',
                        'icon' => 'fas fa-dollar-sign',
                        'path' => $this->_sync_process,
                        'active_at' => 'my_payments',
                        'order' => 4
                    ]
                ]
            ],
            'top_nav' => [
            ],
            'urlsToRemember' => [
                
            ]
        ];
    }
    
    
}