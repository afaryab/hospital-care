<?php

class MyStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {
ini_set('memory_limit', '-1');
            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'My Statement';
            $this->_pageData['module'] = 'My Statement';
            $this->_pageData['report_transactions'] = [];
            $this->_pageData['opd_trans'] = [];
            $this->_pageData['dental_trans'] = [];
            $this->_pageData['ultra_trans'] = [];

            $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('patients');
            $this->_pageData['patients'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('reception_counters_closings_transaction_elements');
            $this->_pageData['closing_transactions'] =  $this->commonModel->getAll();

            //$this->_pageData['users'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            // $this->_pageData['users'] = $this->aauth->getUserByType('is_dentist');
            $this->_pageData['users'] = $this->aauth->list_users();


            $this->load->model('commonModel', 'opdtransaction');
            $this->opdtransaction->setTableName('opd_transactions');
            //$this->_pageData['opd_transactions'] =  $this->commonModel->getAll();
            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transaction_elements');
            $seldoc = NULL;
           
            
            $doctor =  22;

            $departemnt = array_key_exists('service',$_GET) ? $_GET['service'] : '';
            
             $opdtransactions = [];
             $dentaltransactions = [];
             $ultratransactions = [];
            //$seldoc = $this->_pageData['selectedDoctor'];
            //print_array($seldoc);
            

                $date =  date("Y-m-d");

                    $opdtransactions = $this->transaction->findBy(['CAST(created_on AS DATE) = ' => $date, 'doctor_id' => $doctor ]);

            
            // print_array($opdtransactions);
            // $this->_pageData['opd_trans']=$opdtransactions;
            // $transacIds = [];
            //$pids = [];
           

            foreach($opdtransactions as $transac){
                // $transacIds[] = $transac['id'];
                //$pids[] = $transac['patient_id'];
                $this->_pageData['opd_trans'][$transac['id']] = $transac;

                if($transac['service_id'] != 0){
                        

                    $selected_service = array_filter(
                        $this->_pageData['opd_services'],
                        function ($e) use (&$transac) {
                            return $e['id'] == $transac['service_id'];
                        }
                    );
                $selected_service = reset($selected_service);
                
                //print_array($this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name']);
                $this->_pageData['opd_trans'][$transac['id']]['service_name'] = isset($selected_service) && $selected_service['name'] ? $selected_service['name'] : 'Undefined';
               }
            }

            // $this->transaction->setTableName("reception_counters_closings_transaction_elements");
            // $rowsRaw = $this->transaction->findBy(['closing_transaction_id' => $transacIds]);
            // $this->load->model('commonModel', 'patients');
            // $this->patients->setTableName("patients");
            // $rowsRaw = $this->patients->findBy(['id' => $pids]);
           // foreach($rowsRaw as $row){
                //$this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                //foreach($transactions as $transac){
                    //print_array($transac);
                    // if($row['service_id'] != 0){
                        

                    //         $selected_service = array_filter(
                    //             $this->_pageData['opd_services'],
                    //             function ($e) use (&$row) {
                    //                 return $e['id'] == $row['service_id'];
                    //             }
                    //         );
                    //     $selected_service = reset($selected_service);
                    //     if($selected_service == false){
                    //         print_array($row);
                    //         print_array($selected_service,1);
                    //     }
                    //     //print_array($this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name']);
                    //     $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name'] = isset($selected_service) && $selected_service['name'] ? $selected_service['name'] : 'Undefined';
                    // }
                    // elseif($transac['income_or_expence'] == 'EXPENSE')
                    // {
                    //     $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                
                    // }
               // }
           // }
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('mystatement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    

}

