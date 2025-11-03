<?php

class EditPanel extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Panel');
    }
    
    public function index($id){
        if ($id != 0) {
           
        
            $this->load->model('commonModel', 'panels');
            $this->panels->setTableName('panel_companies');
            $this->_pageData['panels'] = $this->panels->findOneBy(['id' => $id]);
            
            if ($this->havePost()) {
                $panel = [
                    'name' => $_POST['name'],
                    ];
                    $this->panels->updateRecord($id,$panel);
    
                    $this->setMessage('success', 'Panel company edited successfully!');
                    $this->activityLog('Panel company edited successfully');
                    redirect($this->_pageData['PANELS_LIST']);
            }

        
        }
        $this->_pageData['module'] = 'panel';
        $this->_pageData['title'] = 'Edit Panel';
        $html = $this->load->makeViewWithOutTemplate('edit_panel',$this->_pageData,true);
        
        $this->makeView($html);
    }

    


}

