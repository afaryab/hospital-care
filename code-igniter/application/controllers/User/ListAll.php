<?php

class ListAll extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        
        $this->_pageData['users'] = $this->aauth->list_users(FALSE,FALSE,FALSE,TRUE);
        $this->_pageData['title'] = 'Users';
        $this->_pageData['module'] = 'user';
        $html = $this->load->makeViewWithOutTemplate('list',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

