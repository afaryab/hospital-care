<?php

class IncomeStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Income Statements';
            $this->_pageData['module'] = 'Income Statements';
            $this->_pageData['report_transactions'] = [];

            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transactions');

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $this->_pageData['report_transactions'] = $this->transaction->findBy(['CAST(created_on AS DATE) = ' => $date ]);

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

                $this->_pageData['report_transactions'] = $this->transaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);

            }

            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('incomestatement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }


}

