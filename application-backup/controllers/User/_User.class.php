<?php

class User{
    
    protected $_list_users = 'User/ListAll';
    
    protected $_profile_url = 'User/Profile/Index/';
    
    protected $_create_user_url = 'User/Create';
    
    protected $_edit_url = 'User/Edit/Index/';
    
    protected $_reset_mail_url = 'User/RemindPassword/Index/';
    
    protected $_change_user_pass = 'User/ChangePassword/Index/';
    
    protected $_ban_user_url = 'User/Ban/Index/';
    
    protected $_allow_url = 'User/UnBan/Index/';

    protected $_changeProfilePicture = 'User/Profile/changePicture/';

    protected $_list_notifications = 'User/ListNotifications/';

    protected $_list_notifications_json = 'User/ListNotifications/JSON';

    protected $_list_groups = 'User/Groups/Index';

    protected $_list_permissions = 'User/Permissions/Index';

    protected $_group_permissions = 'User/GroupPermissions/index';
    
    protected $_import_export = 'User/ImportExport';

    protected $_export = 'User/ExportDatabase/exportDatabase';

    protected $_import = 'User/ImportExport/importDatabase';   
    
    function getNavigation(){
        return [
            'navigations' => [
                'Users Management | <i class="fas fa-users"></i>' => [
                    [
                        'label' => 'Users',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Users',
                        'perm_group' => 'Users Management',
                        'priority' => 'perm',
                        'module' => 'user',
                        'icon' => 'fas fa-user',
                        'path' => $this->_list_users,
                        'order' => 0
                    ],
                    [
                        'label' => 'Groups',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Groups',
                        'perm_group' => 'Users Management',
                        'priority' => 'perm',
                        'module' => 'groups',
                        'icon' => 'fab fa-galactic-senate',
                        'path' => $this->_list_groups,
                        'order' => 1
                    ],
                    [
                        'label' => 'Permissions',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Permissions',
                        'perm_group' => 'Users Management',
                        'priority' => 'perm',
                        'module' => 'permissions',
                        'icon' => 'fab fa-accessible-icon',
                        'path' => $this->_list_permissions,
                        'order' => 2
                    ],
                    [
                        'label' => 'Group Permissions',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'Group Permissions',
                        'perm_group' => 'Users Management',
                        'priority' => 'perm',
                        'module' => 'GroupPermissions',
                        'icon' => 'fab fa-buromobelexperte',
                        'path' => $this->_group_permissions,
                        'order' => 3
                    ]
                ]
            ],
            'top_nav' => [
                [
                    'label' => 'My Profile',
                    'group' => '',
                    'perm' => '',
                    'priority' => '',
                    'module' => 'user',
                    'icon' => 'fas fa-user-circle',
                    'path' => $this->_profile_url,
                    'order' => 85
                ]
            ],
            'urlsToRemember' => [
                'USERS_LIST' => $this->_list_users,
                'CREATE_USER' =>  $this->_create_user_url,
                'PROFILE_USER' =>  $this->_profile_url,
                'EDIT_USER' =>  $this->_edit_url,
                'CHANGE_USER_PASS' =>  $this->_change_user_pass,
                'RESET_USER' => $this->_reset_mail_url,
                'BLOCK_USER' => $this->_ban_user_url,
                'ALLOW_USER' => $this->_allow_url,
                'CHANGE_PICTURE' => $this->_changeProfilePicture,
                'LIST_NOTIFICATIONS' => $this->_list_notifications,
                'LIST_NOTIFICATIONS_JSON' => $this->_list_notifications_json,
                'LIST_GROUPS' => $this->_list_groups,
                'LIST_PERMISSION' => $this->_list_permissions,
                'IMPORT_EXPORT_DATABASE' => $this->_import_export,
                'EXPORT_DATABASE' => $this->_export,
                'IMPORT_DATABASE' => $this->_import,
            ]
        ];
    }
    
    
}