<?php

class ListServices extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Services');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }

        $this->load->model('commonModel', 'opd');
        $this->opd->setTableName('opd_services');
        $this->_pageData['opdservices'] = $this->opd->getAll();

        $this->load->model('commonModel', 'inp');
        $this->inp->setTableName('inpd_services');
        $this->_pageData['inpservices'] = $this->inp->getAll();

        $this->load->model('commonModel', 'emer');
        $this->emer->setTableName('emergency_services');
        $this->_pageData['emerservices'] = $this->emer->getAll();

        $this->load->model('commonModel', 'xray');
        $this->xray->setTableName('xray_services');
        $this->_pageData['xrayservices'] = $this->xray->getAll();

        $this->load->model('commonModel', 'test');
        $this->test->setTableName('test_services');
        $this->_pageData['testservices'] = $this->test->getAll();

        $this->load->model('commonModel', 'dental');
        $this->dental->setTableName('dental_services');
        $this->_pageData['dentalservices'] = $this->dental->getAll();

        $this->load->model('commonModel', 'ultra');
        $this->ultra->setTableName('ultrasound_services');
        $this->_pageData['ultraservices'] = $this->ultra->getAll();

        $this->load->model('commonModel', 'recestation_services');
        $this->recestation_services->setTableName('recestation_services');
        $this->_pageData['recestationservices'] =  $this->recestation_services->getAll();

        $this->_pageData['title'] = 'services';
        $this->_pageData['module'] = 'services';
        $html = $this->load->makeViewWithOutTemplate('list_services',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

