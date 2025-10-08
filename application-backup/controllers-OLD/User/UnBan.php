<?php

class UnBan extends MY_Controller
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
        $this->aauth->unban_user($this->_userID);
        
        $this->setMessage('success', 'Account Un Locked for User#'.$this->_userID.'!');
        $this->activityLog('Account Un Locked By Admin for User#'.$this->_userID.'!');
        
        redirect($this->_pageData['USERS_LIST']);
    }
}

