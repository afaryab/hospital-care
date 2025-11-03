<?php

class ListPanelCompanies extends MY_Controller
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


        $this->_pageData['title'] = 'panel';
        $this->_pageData['module'] = 'panel';
        $html = $this->load->makeViewWithOutTemplate('list_panel_companies',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

