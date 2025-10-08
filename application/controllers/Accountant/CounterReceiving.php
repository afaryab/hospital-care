<?php

class CounterReceiving extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    

    public function index(){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'Counter Receiving';
            $this->_pageData['module'] = 'Counter Receiving';
            $this->_pageData['counterrecieving'] = [];

            $this->load->model('commonModel', 'activity');
            $this->activity->setTableName('reception_counters_closings');

            $this->_pageData['users'] = $this->aauth->list_users();

            
            // $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            // $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            // if($dateType == 'S'){

            //     $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

            //     $this->_pageData['receptionactivity'] = $this->activity->findBy(['CAST(created_on AS DATE) = ' => $date ]);

            // }else{

                // if(array_key_exists('date_range',$_GET)){
                    
                //     $date_r = explode('-',$_GET['date_range']);
                //     $start_date = date('Y-m-d',strtotime($date_r[0]));
                //     $end_date = date('Y-m-d',strtotime($date_r[1]));

                // }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-2 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                // }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                $this->_pageData['counterrecieving'] = $this->activity->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);

            // }

            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('counter_recieving', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
    
}

