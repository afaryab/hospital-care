<?php

class Ban extends MY_Controller
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
        
        $msg = array_key_exists('msg',$_GET) ? $_GET['msg'] : "";
        $this->aauth->ban_user($this->_userID,$msg);
        
        $this->setMessage('success', 'Account Locked for User#'.$this->_userID.'!');
        $this->activityLog('Account Locked By Admin for User#'.$this->_userID.'!');
        
        redirect($this->_pageData['USERS_LIST']);
    }
}

