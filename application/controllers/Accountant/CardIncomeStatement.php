<?php

class CardIncomeStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'Card Income Statement';
            $this->_pageData['module'] = 'Card Income Statement';
            $this->_pageData['report_transactions'] = [];

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
            $this->_pageData['ultrasound_services'] =  $this->commonModel->getAll();
            $this->_pageData['ultusers'] = $this->aauth->getOpdDoctors('is_ultrasound_doc');

            
            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transactions');

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $transactions = $this->transaction->findBy(['type'=>'CREDITCARD','CAST(created_on AS DATE) = ' => $date ]);

            }else{

                if(array_key_exists('date_range',$_GET)){
                    
                    $date_r = explode('-',$_GET['date_range']);
                    $start_date = date('Y-m-d',strtotime($date_r[0]));
                    $end_date = date('Y-m-d',strtotime($date_r[1]));

                }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-1 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                $transactions = $this->transaction->findBy(['type'=>'CREDITCARD','CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);

            }
            
            $transacIds = [];

            foreach($transactions as $transac){

                if($transac['income_or_expence'] == 'INCOME'){
                
                }else{
                    $expenseTransactions[] = $transac['id'];
                }
                $transacIds[] = $transac['id'];
                $this->_pageData['report_transactions'][$transac['id']] = $transac;
            }

            $this->transaction->setTableName("reception_counters_closings_transaction_elements");
            $rowsRaw = $this->transaction->findBy(['closing_transaction_id' => $transacIds]);
            
            foreach($rowsRaw as $row){
                $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                //foreach($transactions as $transac){
                    //print_array($transac);
                    if($row['service_id'] != 0){
                        if($row['type'] == 'OPD'){

                            $selected_service = array_filter(
                                $this->_pageData['opd_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }elseif($row['type'] == 'INPT'){

                            $selected_service = array_filter(
                                $this->_pageData['inpatient_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }elseif($row['type'] == 'EMER'){

                            $selected_service = array_filter(
                                $this->_pageData['emergency_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }elseif($row['type'] == 'RADP'){

                            $selected_service = array_filter(
                                $this->_pageData['xray_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }elseif($row['type'] == 'DENTAL'){

                            $selected_service = array_filter(
                                $this->_pageData['dental_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }elseif($row['type'] == 'ULTRA'){

                            $selected_service = array_filter(
                                $this->_pageData['ultrasound_services'],
                                function ($e) use (&$row) {
                                    return $e['id'] == $row['service_id'];
                                }
                            );
                        }
                        $selected_service = reset($selected_service);
                        if($selected_service == false){
                            print_array($row);
                            print_array($selected_service,1);
                        }
                        //print_array($this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name'],
			//print_array($selected_service,1);
                $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name'] = isset($selected_service) && is_array($selected_service) && array_key_exists('name', $selected_service) ? $selected_service['name'] : 'Undefined';
                    }elseif($row['type'] == 'INPT-EXP'){
                        
                        $this->load->model('commonModel', 'inpexpenses');
                        $this->inpexpenses->setTableName('inpatient_expense_transactions');
                       
                        $expenseRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        
                        $expArray = $this->inpexpenses->findOneBy($expenseRecordArray);
                        
                        $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['inpExp'] = $expArray;

                        
                        
                    }else{
                        
                        $this->load->model('commonModel', 'expenses');
                        $this->expenses->setTableName('expenses');
                        $expenseRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        $expArray = $this->expenses->findOneBy($expenseRecordArray);
                        
                        $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_array'] = $expArray;

                        
                        
                    }
                    // elseif($transac['income_or_expence'] == 'EXPENSE')
                    // {
                    //     $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                
                    // }
               // }
            }
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('card_income_statement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
    
}

