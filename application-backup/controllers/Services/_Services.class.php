<?php

class Services{
    
    protected $_list_services = 'Services/ListServices';
    
    protected $_create_service = 'Services/CreateServices';

    protected $_edit_service = 'Services/EditService/Index/';

    protected $_edit_opd_service = 'Services/EditService/editOpd/';

    protected $_edit_inp_service = 'Services/EditService/editInp/';

    protected $_edit_emer_service = 'Services/EditService/editEmer/';

    protected $_edit_xray_service = 'Services/EditService/editXray/';

    protected $_edit_test_service = 'Services/EditService/editTest/';

    protected $_edit_dental_service = 'Services/EditService/editDental/';    
    
    protected $_edit_ultra_service = 'Services/EditService/editUltra/';

    protected $_edit_reces_service = 'Services/EditService/editReces/';
    // protected $_edit_url = 'Services/EditServices';
    
     
    
    function getNavigation(){
        return [
            'navigations' => [
                'Users Management | <i class="fas fa-users"></i>' => [
                    [
                        'label' => 'Services',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Services',
                        'perm_group' => 'Services Management',
                        'priority' => 'perm',
                        'module' => 'services',
                        'icon' => 'fab fa-servicestack',
                        'path' => $this->_list_services,
                        'order' => 0
                    ]
                    
                ]
            ],
            'urlsToRemember' => [
                'SERVICES_LIST' => $this->_list_services,
                'CREATE_SERVICE' =>  $this->_create_service,
                'EDIT_SERVICE' =>  $this->_edit_service,
                'EDIT_OPD_SERVICE' =>  $this->_edit_opd_service,
                'EDIT_INP_SERVICE' =>  $this->_edit_inp_service,
                'EDIT_EMER_SERVICE' =>  $this->_edit_emer_service,
                'EDIT_XRAY_SERVICE' =>  $this->_edit_xray_service,
                'EDIT_TEST_SERVICE' =>  $this->_edit_test_service,
                'EDIT_DENTAL_SERVICE' =>  $this->_edit_dental_service,
                'EDIT_ULTRA_SERVICE' =>  $this->_edit_ultra_service,
                'EDIT_RECES_SERVICE' =>  $this->_edit_reces_service,
                // 'PROFILE_USER' =>  $this->_profile_url,
                // 'EDIT_USER' =>  $this->_edit_url,
                // 'CHANGE_USER_PASS' =>  $this->_change_user_pass,
                // 'RESET_USER' => $this->_reset_mail_url,
                // 'BLOCK_USER' => $this->_ban_user_url,
                // 'ALLOW_USER' => $this->_allow_url,
                // 'CHANGE_PICTURE' => $this->_changeProfilePicture,
                // 'LIST_NOTIFICATIONS' => $this->_list_notifications,
                // 'LIST_NOTIFICATIONS_JSON' => $this->_list_notifications_json,
                // 'LIST_GROUPS' => $this->_list_groups,
                // 'LIST_PERMISSION' => $this->_list_permissions,
                // 'IMPORT_EXPORT_DATABASE' => $this->_import_export,
                // 'EXPORT_DATABASE' => $this->_export,
                // 'IMPORT_DATABASE' => $this->_import,
            ]
        ];
    }
    
    
}
