<?php

class InpatientStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {
            
            $this->_pageData['title'] = 'Inpatient Statement';
            $this->_pageData['module'] = 'Inpatient Statement';
            
            $this->load->model('commonModel', 'commonModel');
            $this->load->model('commonModel', 'transaction');

            $this->_pageData['inpstatement'] = [];
            
           


                    $this->load->model('commonModel','inpatient_file');
                    $this->inpatient_file->setTableName('inpatient_file');
                   // $this->_pageData['files'] = $this->inpatient_file->getAll();
                    $this->load->model('commonModel','inpatient_transactions');
                    $this->inpatient_transactions->setTableName('inpatient_transactions');
                    $this->_pageData['transactions'] = $this->inpatient_transactions->getAll();
                    $this->load->model('commonModel','recestation_transactions');
                    $this->recestation_transactions->setTableName('recestation_transactions');
                    $this->_pageData['recestrans'] = $this->recestation_transactions->getAll();
                    $this->load->model('commonModel','patients');
                    $this->patients->setTableName('patients');
                    $this->_pageData['patients'] = $this->patients->getAll();

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) = ' => $date ]);

            }else{

                if(array_key_exists('date_range',$_GET)){
                    
                    $date_r = explode('-',$_GET['date_range']);
                    $start_date = date('Y-m-d',strtotime($date_r[0]));
                    $end_date = date('Y-m-d',strtotime($date_r[1]));

                }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-10 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                $this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);

            }

                   
                    //incomplete logic

                //     $inp = $this->_pageData['files'];
                   
                //     // $id = $inp['id'];
                //     // $inpd = $inp['patient_id'];
                //     // $id = array_column($in, 'id');
                //     // $inpd = array_column($in, 'patient_id');
                //     foreach($inp as $in)
                //     {
                  
                //     // $id = array_column($in, 'id');
                //     // $inpd = array_column($in, 'patient_id');
                //     $id = $in['id'];
                //     $inpd = $in['patient_id'];
                    
                //     $this->load->model('commonModel','inpatient_transactions');
                //     $this->inpatient_transactions->setTableName('inpatient_transactions');
                //     $this->_pageData['trans'] = $this->inpatient_transactions->findBy(['file_id' => $id]);
                   

                //     $this->load->model('commonModel','patients');
                //     $this->patients->setTableName('patients');
                //     $this->_pageData['patient'] = $this->patients->findOneBy(['id' => $inpd]);
                //     //print_array($this->_pageData['patient']);
                //    // $this->_pageData['files'] = $in;

                //     }
                $this->_pageData['date'] = $date;

            $html = $this->load->makeViewWithOutTemplate('inpatient_statement', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    

}

