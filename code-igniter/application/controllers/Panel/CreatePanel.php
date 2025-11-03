<?php

class CreatePanel extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Panel');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        $this->load->model('commonModel', 'panels');
        $this->panels->setTableName('panel_companies');
        $this->_pageData['panels'] = $this->panels->getAll();


        
        if ($this->havePost()) {
            
               $panel=[
               'name' => $_POST['name'],
               'entered_by' => 1,
               ];
               $this->panels->addNew($panel);
        
            $this->setMessage('success', 'Panel company created successfully!');
            $this->activityLog('Panel company created successfully');
            redirect($this->_pageData['PANELS_LIST']);
  
        }
        
       
        $this->_pageData['module'] = 'panel';
        $this->_pageData['title'] = 'Create Panel';
        $html = $this->load->makeViewWithOutTemplate('create_panel',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

