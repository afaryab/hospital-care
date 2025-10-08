<?php

class Rooms{
    
    protected $_list_rooms = 'Rooms/ListRooms';
    
    protected $_create_room = 'Rooms/CreateRoom';

    protected $_edit_room = 'Rooms/EditRoom/Index/';


    
    // protected $_edit_url = 'Services/EditServices';
    
     
    
    function getNavigation(){
        return [
            'navigations' => [
                'Users Management | <i class="fas fa-users"></i>' => [
                    [
                        'label' => 'Rooms',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Rooms',
                        'perm_group' => 'Rooms Management',
                        'priority' => 'perm',
                        'module' => 'rooms',
                        'icon' => 'fas fa-clipboard-list',
                        'path' => $this->_list_rooms,
                        'order' => 0
                    ]
                    
                ]
            ],
            'urlsToRemember' => [
                'ROOMS_LIST' => $this->_list_rooms,
                'CREATE_ROOM' =>  $this->_create_room,
                'EDIT_ROOM' =>  $this->_edit_room,
               
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