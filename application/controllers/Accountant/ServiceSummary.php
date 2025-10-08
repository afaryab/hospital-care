<?php

class ServiceSummary extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Service Summary';
            $this->_pageData['module'] = 'Service Summary';
            $this->_pageData['report_transactions'] = [];
            $this->_pageData['reports'] = [];
            $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('emergency_services');
            $this->_pageData['emergency_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('xray_services');
            $this->_pageData['xray_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('test_services');
            $this->_pageData['test_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('patients');
            $this->_pageData['patients'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('inpatient_file');
            $this->_pageData['inpatients'] =  $this->commonModel->getAll();
            $this->_pageData['users'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->commonModel->setTableName('inpatient_transactions');
            $this->_pageData['inptrans'] = $this->commonModel->getAll();
            $this->commonModel->setTableName('dental_services');
            $this->_pageData['dental_services'] =  $this->commonModel->getAll();
            $this->_pageData['dusers'] = $this->aauth->getOpdDoctors('is_dentist');
            $this->commonModel->setTableName('ultrasound_services');
            $this->_pageData['ultra_services'] =  $this->commonModel->getAll();
            $this->_pageData['ultusers'] = $this->aauth->getOpdDoctors('is_ultrasound_doc');
            $this->commonModel->setTableName('recestation_services');
            $this->_pageData['reces_services'] =  $this->commonModel->getAll();

            
            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transactions');

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 

            $servicopd = array_key_exists('oserviceid',$_GET) ? $_GET['oserviceid'] : 0;
            $servicdnt = array_key_exists('dserviceid',$_GET) ? $_GET['dserviceid'] : 0;
            $servicult = array_key_exists('userviceid',$_GET) ? $_GET['userviceid'] : 0;
            $servicinp = array_key_exists('iserviceid',$_GET) ? $_GET['iserviceid'] : 0;
            $servicemr = array_key_exists('eserviceid',$_GET) ? $_GET['eserviceid'] : 0;
            $servicres = array_key_exists('rserviceid',$_GET) ? $_GET['rserviceid'] : 0;
            $servctype = array_key_exists('service',$_GET) ? $_GET['service'] : NULL;

            // print_array($servctype);
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $transactions = $this->transaction->findBy(['CAST(created_on AS DATE) = ' => $date ]);

            }else{

                if(array_key_exists('date_range',$_GET)){
                    
                    $date_r = explode('-',$_GET['date_range']);
                    $start_date = date('Y-m-d',strtotime($date_r[0]));
                    $end_date = date('Y-m-d',strtotime($date_r[1]));

                }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-2 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                $transactions = $this->transaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);

            }
            
            $transacIds = [];

            foreach($transactions as $transac){
                $transacIds[] = $transac['id'];
                $this->_pageData['report_transactions'][$transac['id']] = $transac;
            }

            
            if($servctype == 'opd'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'OPD','service_id' => $servicopd]);
                //$servctype = 'opd';
                // print_array($this->_pageData['opd_services']);
            }else if($servctype == 'emr'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'EMER','service_id' => $servicemr]);
                //print_array($this->_pageData['reports']);
                $servctype = 'emr';
            }else if($servctype == 'inpd'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'INPT','service_id' => $servicinp]);
                //print_array($this->_pageData['reports']);
                $servctype = 'inpd';
            }else if($servctype == 'dental'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'DENTAL','service_id' => $servicdnt]);
                //print_array($this->_pageData['reports']);
                $servctype = 'dental';
            }else if($servctype == 'ultra'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'ULTRA','service_id' => $servicult]);
                $servctype = 'ultra';
                //print_array($this->_pageData['reports']);
            }else if($servctype == 'reces'){
                $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                $this->_pageData['reports'] = $this->transaction->findBy(['closing_transaction_id' => $transacIds,'type' => 'RECES','service_id' => $servicres]);
                $servctype = 'reces';
                //print_array($this->_pageData['reports']);
            }

            //$this->_pageData['reports'] = reset($this->_pageData['reports']);
            

            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('servicesummary', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }


}

