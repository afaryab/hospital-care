<?php

class ListAppointments extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Appointments');
    }
    
    public function index(){

        if($this->isLoggedIn()) {
            
            $this->_pageData['title'] = 'List Appointments';
            $this->_pageData['module'] = 'List Appointments';
            
            $this->load->model('commonModel', 'commonModel');
            $this->load->model('commonModel', 'transaction');

            // $this->_pageData['inpstatement'] = [];
            $this->_pageData['appointment'] = [];
            
            $user = $this->getUser();
            if($user->is_dentist == 1){    

                    $this->load->model('commonModel','dental_appointments');
                    $this->dental_appointments->setTableName('dental_appointments');
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

                    $this->_pageData['appointment'] = $this->dental_appointments->findBy(['CAST(start_date AS DATE) = ' => $date ,'doctor_id'=>$user->id ]);

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

                    $this->_pageData['appointment'] = $this->dental_appointments->findBy(['CAST(start_date AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'doctor_id'=>$user->id ]);

                }
            }

                $this->_pageData['date'] = $date;

            $html = $this->load->makeViewWithOutTemplate('list_appointment', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    

}

