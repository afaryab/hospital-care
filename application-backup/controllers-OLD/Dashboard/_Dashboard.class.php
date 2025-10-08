<?php

class Dashboard{
    
    protected $_dashboard = 'Dashboard/ViewDashboard/index';
    protected $_dashboard_json = 'Dashboard/ViewDashboard/dashboardJS';
    protected $_dashboard_user_status = 'Dashboard/ViewDashboard/getUsersStatus';
    
    function getNavigation(){
        return [
            'navigations' => [
                'Home|<i class="icon icon-home"></i>' => [
                    [
                        'label' => 'Dashboard',
                        'no-group' => true,
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN',
                        'perm' => 'all',
                        'priority' => 'group',
                        'module' => 'dashboard',
                        'icon' => 'fas fa-tachometer-alt',
                        'path' => $this->_dashboard,
                        'order' => 0
                    ]
                ]
            ],
            'urlsToRemember' => [
                'DASHBOARD' => $this->_dashboard,
                'DASHBOARD_JSON' => $this->_dashboard_json,
                'DASHBOARD_USERS_STATUS' => $this->_dashboard_user_status
            ]
        ];
    }
    
    
}