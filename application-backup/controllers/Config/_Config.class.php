<?php

class Config{
    
    protected $_options_url = 'Config/Options';
    protected $_install_url = 'Config/Install';

    
    function getNavigation(){
        return [
            'top_nav' => [
                [
                    'label' => 'Options',
                    'group' => 'ROLE_DEVELOPER',
                    'perm' => 'ALL',
                    'priority' => 'role',
                    'module' => 'Config',
                    'icon' => 'fas fa-sliders-h',
                    'path' => $this->_options_url,
                    'order' => 90
                ]
            ],
            'urlsToRemember' => [
                'OPTIONS_INSTALL' => $this->_install_url,
                'OPTIONS_EDIT' => $this->_options_url,
            ]
        ];
    }
    
    
}


$con = new Config();
$jav1 = $con->getNavigation();

$con2 = new Config();
$jav1 = $con2->getNavigation();

Config::class;