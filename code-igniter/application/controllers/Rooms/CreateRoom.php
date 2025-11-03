<?php

class CreateRoom extends MY_Controller
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


        
        if ($this->havePost()) {
            
               $room=[
               'name' => $_POST['name'],
               'charges' => 0,
               'entered_by' => 1,
               ];
               $this->rooms->addNew($room);
        
            $this->setMessage('success', 'Room created successfully!');
            $this->activityLog('Room created successfully');
            redirect($this->_pageData['ROOMS_LIST']);
  
        }
        
       
        $this->_pageData['module'] = 'rooms';
        $this->_pageData['title'] = 'Create Room';
        $html = $this->load->makeViewWithOutTemplate('create_room',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

