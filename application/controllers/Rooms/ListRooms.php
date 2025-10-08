<?php

class ListRooms extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Rooms');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }

        $this->load->model('commonModel', 'rooms');
        $this->rooms->setTableName('inpd_rooms');
        $this->_pageData['rooms'] = $this->rooms->getAll();


        $this->_pageData['title'] = 'rooms';
        $this->_pageData['module'] = 'rooms';
        $html = $this->load->makeViewWithOutTemplate('list_rooms',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

