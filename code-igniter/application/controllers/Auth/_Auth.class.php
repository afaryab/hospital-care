<?php

class Auth{
    
    protected $_logout_url = 'Auth/Logout/Index';
    
    protected $_validate_url = 'Auth/Validate';
    
    protected $_login_url = 'Auth/Login';
    
    protected $_forgot_password_url = 'Auth/FogotPassword';
    
    protected $_change_pass_url = 'Auth/ChangePassword';
    
    protected $_verify_email_url = 'Auth/verifyEmail';
    
    
    function getNavigation(){
        return [
            'top_nav' => [
                [
                    'label' => 'Log Out',
                    'group' => 'ALL',
                    'perm' => 'ALL',
                    'priority' => 'perm',
                    'module' => 'users',
                    'icon' => 'fas fa-sign-out-alt',
                    'path' => $this->_logout_url,
                    'order' => 100
                ],
                [
                    'label' => 'Change Password',
                    'group' => 'ALL',
                    'perm' => 'ALL',
                    'priority' => 'perm',
                    'module' => 'users',
                    'icon' => 'fas fa-key',
                    'path' => $this->_change_pass_url,
                    'order' => 95
                ]
            ],
            'urlsToRemember' => [
                'INDEX_PATH' => $this->_validate_url,
                'LOGIN' => $this->_login_url,
                'LOGOUT' => $this->_logout_url,
                'FORGOT_PASSWORD' => $this->_forgot_password_url,
                'VERIFY_EMAIL' => $this->_verify_email_url,
                'CHANGE_PASSWORD' => $this->_change_pass_url
            ]
        ];
    }
    
    
}