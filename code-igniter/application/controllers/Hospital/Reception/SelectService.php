<?php

class SelectService extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){

        $id = array_key_exists('patient_id',$_GET) ? ($_GET['patient_id'] == '' ? 0 : $_GET['patient_id']) : 0;

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['opd_doctors'] =  []; // $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->_pageData['inpatient_doctors'] =  []; // $this->aauth->getOpdDoctors('is_inpatient_doctor');
            $this->_pageData['xray_tech'] =  []; // $this->aauth->getOpdDoctors('is_xray_tech');
            $this->_pageData['dentists'] =  []; // $this->aauth->getOpdDoctors('is_dentist');
            $this->_pageData['ultradocs'] =  []; // $this->aauth->getOpdDoctors('is_ultrasound_doc');

            $this->_pageData['title'] = 'Hospital Counter';
            $this->_pageData['module'] = 'Hospital Counter';

            // $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  []; // $this->commonModel->getAll();
            $this->commonModel->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  []; // $this->commonModel->getAll();
            $this->commonModel->setTableName('emergency_services');
            $this->_pageData['emergency_services'] =  []; //$this->commonModel->getAll();
            $this->commonModel->setTableName('xray_services');
            $this->_pageData['xray_services'] =  []; //$this->commonModel->getAll();
            $this->commonModel->setTableName('test_services');
            $this->_pageData['test_services'] =  []; //$this->commonModel->getAll();
            // $this->_pageData['patient_id'] = $id;
            // $this->_pageData['closingArray'] = $this->receptionClosingArray;
            $this->commonModel->setTableName('inpd_rooms');
            $this->_pageData['inpd_rooms'] =  []; //$this->commonModel->getAll();
            $this->commonModel->setTableName('panel_companies');
            $this->_pageData['panel_companies'] =  []; //$this->commonModel->getAll();
            $this->commonModel->setTableName('dental_services');
            $this->_pageData['dental_services'] =  []; // $this->commonModel->getAll();
            $this->commonModel->setTableName('ultrasound_services');
            $this->_pageData['ultrasound_services'] = []; // $this->commonModel->getAll();
            
          
            
            
            $html = $this->load->makeViewWithOutTemplate('expose', $this->_pageData, true);
            
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

