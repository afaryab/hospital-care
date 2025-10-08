<?php

class RemindPassword extends MY_Controller
{
    protected $_userID = 0;
    protected $_user = null;
    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index($userId){
        
        $this->_userID = $userId;
        $this->_user = $this->aauth->get_user($userId);
        $this->aauth->remind_password($this->_user->email,$this->_pageData['licenceKey']);
        
        $this->setMessage('success', 'Password reset Request send for User#'.$this->_userID.'!');
        $this->activityLog('Reset Password Request Send By Admin for User#'.$this->_userID.'!');
        
        redirect($this->_pageData['USERS_LIST']);
    }
}

