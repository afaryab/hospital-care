<?php

class DoctorShareStatements extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'Doctor Share Statement';
            $this->_pageData['module'] = 'Doctor Share Statement';
            $this->_pageData['voucher_payments'] = [];

            $this->load->model('commonModel', 'vouchers');
            $this->vouchers->setTableName('expense_vouchers');
            $this->_pageData['users'] = $this->aauth->list_users();
            $seldoc = NULL;
            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            $doctor = array_key_exists('doc',$_GET) ? $_GET['doc'] : 0;
            
            if($doctor != 0){
                $this->_pageData['selectedDoctor'] = array_filter(
                    $this->_pageData['users'],
                    function ($e) use (&$doctor) {
                        return $e->id == $doctor;
                    }
                );
                // $seldoc = [];
                $seldoc = $this->_pageData['selectedDoctor'];
                $this->_pageData['selectedDoctor'] = reset($this->_pageData['selectedDoctor']);
                
            }
            //$seldoc = $this->_pageData['selectedDoctor'];
            //print_array($seldoc);
            
            // print_array($opdtransactions);
            // $this->_pageData['opd_trans']=$opdtransactions;
            // $transacIds = [];
            //$pids = [];
           

            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $this->_pageData['voucher_payments'] = $this->vouchers->findBy(['CAST(created_on AS DATE) = ' => $date , 'employee_id' => $doctor]);

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

                $this->_pageData['voucher_payments'] = $this->vouchers->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ,  'employee_id' => $doctor]);

            }
            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('doctorsharestatement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

