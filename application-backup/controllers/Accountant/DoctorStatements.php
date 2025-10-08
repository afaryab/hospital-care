<?php

class DoctorStatements extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'Doctor Statement';
            $this->_pageData['module'] = 'Doctor Statement';
            $this->_pageData['report_transactions'] = [];
            $this->_pageData['opd_trans'] = [];
            $this->_pageData['dental_trans'] = [];
            $this->_pageData['ultra_trans'] = [];

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
            //$this->_pageData['users'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            // $this->_pageData['users'] = $this->aauth->getUserByType('is_dentist');
            $this->_pageData['users'] = $this->aauth->list_users();
            $this->commonModel->setTableName('dental_services');
            $this->_pageData['dental_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('ultrasound_services');
            $this->_pageData['ultrasound_services'] =  $this->commonModel->getAll();

            $this->load->model('commonModel', 'opdtransaction');
            $this->opdtransaction->setTableName('opd_transactions');
            //$this->_pageData['opd_transactions'] =  $this->commonModel->getAll();
            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transactions');
            $this->load->model('commonModel', 'dentaltransaction');
            $this->dentaltransaction->setTableName('dental_transactions');
            $this->load->model('commonModel', 'ultrasoundtransaction');
            $this->ultrasoundtransaction->setTableName('ultrasound_transactions');
            $seldoc = NULL;
            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            $doctor = array_key_exists('doc',$_GET) ? $_GET['doc'] : 0;

            $departemnt = array_key_exists('service',$_GET) ? $_GET['service'] : '';
            
             $opdtransactions = [];
             $dentaltransactions = [];
             $ultratransactions = [];
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
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");
                if($seldoc['is_opd_doctor'] == 1)
                {
                    $opdtransactions = $this->opdtransaction->findBy(['CAST(created_on AS DATE) = ' => $date, 'doctor_id' => $doctor ]);
                }elseif($seldoc['is_dentist'] == 1){
                    $dentaltransactions = $this->dentaltransaction->findBy(['CAST(created_on AS DATE) = ' => $date, 'doctor_id' => $doctor ]);
                }
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
                 if($seldoc != NULL){
                    if($this->_pageData['selectedDoctor']->is_opd_doctor == 1 && $departemnt == 'opd')
                    {
                        $opdtransactions = $this->opdtransaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'doctor_id' => $doctor ]);
                    }elseif($this->_pageData['selectedDoctor']->is_dentist == 1 && $departemnt == 'dental'){
                        $dentaltransactions = $this->dentaltransaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'doctor_id' => $doctor ]);
                    }elseif($this->_pageData['selectedDoctor']->is_ultrasound_doc == 1 && $departemnt == 'ultra'){
                        $ultratransactions = $this->ultrasoundtransaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'doctor_id' => $doctor ]);
                    }
                }
            }
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
            foreach($dentaltransactions as $dtransac){
                // $transacIds[] = $transac['id'];
                //$pids[] = $transac['patient_id'];
                $this->_pageData['dental_trans'][$dtransac['id']] = $dtransac;

                if($dtransac['service_id'] != 0){
                        

                    $selected_service = array_filter(
                        $this->_pageData['dental_services'],
                        function ($e) use (&$dtransac) {
                            return $e['id'] == $dtransac['service_id'];
                        }
                    );
                $selected_service = reset($selected_service);
                
                //print_array($this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name']);
                $this->_pageData['dental_trans'][$dtransac['id']]['service_name'] = isset($selected_service) && $selected_service['name'] ? $selected_service['name'] : 'Undefined';
               }
            }
            foreach($ultratransactions as $dtransac){
                // $transacIds[] = $transac['id'];
                //$pids[] = $transac['patient_id'];
                $this->_pageData['ultra_trans'][$dtransac['id']] = $dtransac;

                if($dtransac['service_id'] != 0){
                        

                    $selected_service = array_filter(
                        $this->_pageData['ultrasound_services'],
                        function ($e) use (&$dtransac) {
                            return $e['id'] == $dtransac['service_id'];
                        }
                    );
                $selected_service = reset($selected_service);
                
                //print_array($this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name']);
                $this->_pageData['ultra_trans'][$dtransac['id']]['service_name'] = isset($selected_service) && $selected_service['name'] ? $selected_service['name'] : 'Undefined';
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
            
            $html = $this->load->makeViewWithOutTemplate('doctorstatement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
    public function generateOPDVoucher(){


        if($this->isLoggedIn() ) {
            
            if($this->havePost()){
                $this->load->model('commonModel', 'expenses');
                $this->expenses->setTableName('expense_vouchers');
                $user = $this->aauth->get_user($_POST['doctor_id']);
                $dbArray = [
                    'exp_category_id' => 1,
                    'exp_amount_numbers' => $_POST['voucher_amount'],
                    'exp_amount_words' => $_POST['voucher_amount_in_words'],
                    'payed_to_employee' =>  1 ,
                    'employee_id' => $_POST['doctor_id'] ,
                    'payed_to_others' => '',
                    'expense_notes' => "OPD Payment to ".$user->name,
                ];

                $voucherID = $this->expenses->addNew($dbArray);

                $this->aauth->update_user_type($_POST['doctor_id'],['opd_charges_amount'=> $_POST['percentage']]);
                
                if($user->is_opd_doctor == 1 && $user->is_dentist == 0 && $user->is_ultrasound_doc == 0){
                    $this->load->model('commonModel', 'opd_transactions');
                    $this->opd_transactions->setTableName('opd_transactions');
                    $docid = $_POST['doctor_id'];
                    $share = $this->opd_transactions->findBy(['doctor_id' => $docid]);
                    $array = [
                        'submitted_for_accounts' => 1
                    ];
                    
                    foreach($share as $shr){
                        
                        $this->opd_transactions->updateRecord($shr['id'], $array);
                    }
                }elseif($user->is_dentist == 1){
                    $this->load->model('commonModel', 'dental_transactions');
                    $this->dental_transactions->setTableName('dental_transactions');
                    $docid = $_POST['doctor_id'];
                    $share = $this->dental_transactions->findBy(['doctor_id' => $docid]);
                    $array = [
                        'submitted_for_accounts' => 1
                    ];
                    
                    foreach($share as $shr){
                        
                        $this->dental_transactions->updateRecord($shr['id'], $array);
                    }
                    // $this->dental_transactions->updateRecord($_POST['payed_ids'], [
                    //     'submitted_for_accounts' => 1
                    // ]);
                }elseif($user->is_ultrasound_doc == 1){
                    $this->load->model('commonModel', 'ultrasound_transactions');
                    $this->ultrasound_transactions->setTableName('ultrasound_transactions');
                    $docid = $_POST['doctor_id'];
                    $share = $this->ultrasound_transactions->findBy(['doctor_id' => $docid]);
                    $array = [
                        'submitted_for_accounts' => 1
                    ];
                    
                    foreach($share as $shr){
                        
                        $this->ultrasound_transactions->updateRecord($shr['id'], $array);
                    }
                }

                $this->setMessage('success', 'New Expense Voucher #' . $voucherID . ' is added!');
                $this->activityLog('New Expense Voucher #' . $voucherID . ' is added!');

                redirect($this->_pageData['urlsToRemember']['PRINT_EXPENSE_TOKEN_URL'] . $voucherID);


            }
        }

    }
}

