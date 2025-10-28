<?php

class Expose extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){

        $id = array_key_exists('patient_id',$_GET) ? ($_GET['patient_id'] == '' ? 0 : $_GET['patient_id']) : 0;
        
        if($this->isLoggedIn() && $id > 0) {

            $this->_pageData['title'] = 'Hospital Counter';
            $this->_pageData['module'] = 'Hospital Counter';
            
            $this->load->model('commonModel', 'patients');
            $this->patients->setTableName('patients');
            // $this->_pageData['patient'] = $this->patients->findOneBy(['id' => $id]);
            
            $this->load->model('commonModel', 'ps_numbers');
            $this->ps_numbers->setTableName('ps_numbers');
            $patientPSNumberRecord = $this->ps_numbers->findOneBy([
                'patient_id' => $id
            ]);
            

            if(!$patientPSNumberRecord){




            }

            $patientPSNumber = $patientPSNumberRecord['ps_number'];
            
            // $this->_pageData['patient']['ps_number'] = $patientPSNumber;
            
            $html = $this->load->makeViewWithOutTemplate('counter', $this->_pageData, false);

            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

