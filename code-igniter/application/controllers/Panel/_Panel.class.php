<?php

class Panel{
    
    protected $_list_panel_companies = 'Panel/ListPanelCompanies';
    
    protected $_create_panel_company = 'Panel/CreatePanel';

    protected $_edit_panel_company = 'Panel/EditPanel/Index/';

    
     
    
    function getNavigation(){
        return [
            'navigations' => [
                'Users Management | <i class="fas fa-users"></i>' => [
                    [
                        'label' => 'Panel Companies',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Panel',
                        'perm_group' => 'Panel Management',
                        'priority' => 'perm',
                        'module' => 'panel',
                        'icon' => 'fas fa-clipboard-list',
                        'path' => $this->_list_panel_companies,
                        'order' => 0
                    ]
                    
                ]
            ],
            'urlsToRemember' => [
                'PANELS_LIST' => $this->_list_panel_companies,
                'CREATE_PANEL' =>  $this->_create_panel_company,
                'EDIT_PANEL' =>  $this->_edit_panel_company,
               
            ]
        ];
    }
    
    
}