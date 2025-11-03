<?php

class EditRoom extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Rooms');
    }
    
    public function index($id){
        if ($id != 0) {
           
        
            $this->load->model('commonModel', 'rooms');
            $this->rooms->setTableName('inpd_rooms');
            $this->_pageData['rooms'] = $this->rooms->findOneBy(['id' => $id]);
            
            if ($this->havePost()) {
                $room = [
                    'name' => $_POST['name'],
                    ];
                    $this->rooms->updateRecord($id,$room);
    
                    $this->setMessage('success', 'Room edited successfully!');
                    $this->activityLog('Room edited successfully');
                    redirect($this->_pageData['ROOMS_LIST']);
            }

        
        }
        $this->_pageData['module'] = 'rooms';
        $this->_pageData['title'] = 'Edit Room';
        $html = $this->load->makeViewWithOutTemplate('edit_room',$this->_pageData,true);
        
        $this->makeView($html);
    }

    


}

