<?php

class ReceptionTransaction extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index($id = 0){
        $user = $this->aauth->get_user();
        
        if($user->is_receptionist == 1){
            $this->redirectUnauthorized();
        }

        if($this->isLoggedIn()) {
            if($id != 0 ){
                $this->_pageData['title'] = 'Reception Transaction';
                $this->_pageData['module'] = 'Reception Transaction';
                
                $this->load->model('commonModel', 'commonModel');
                $this->load->model('commonModel', 'transaction');

                
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
                $this->_pageData['test_services'] =  $this->commonModel->getAll();
                $this->commonModel->setTableName('patients');
                $this->_pageData['patients'] =  $this->commonModel->getAll();
                $this->commonModel->setTableName('inpatient_file');
                $this->_pageData['inpatients'] =  $this->commonModel->getAll();
                $this->_pageData['users'] = $this->aauth->getOpdDoctors('is_opd_doctor');
                $this->commonModel->setTableName('inpatient_transactions');
                $this->_pageData['inptrans'] = $this->commonModel->getAll();
                $this->_pageData['counterusers'] = $this->aauth->list_users();
                $this->commonModel->setTableName('dental_services');
                $this->_pageData['dental_services'] =  $this->commonModel->getAll();
                $this->_pageData['dusers'] = $this->aauth->getOpdDoctors('is_dentist');
                $this->commonModel->setTableName('ultrasound_services');
                $this->_pageData['ultrasound_services'] =  $this->commonModel->getAll();
                $this->_pageData['ultusers'] = $this->aauth->getOpdDoctors('is_ultrasound_doc');
                $this->commonModel->setTableName('recestation_services');
                $this->_pageData['recestation_services'] =  $this->commonModel->getAll();
                $this->commonModel->setTableName('recestation_transactions');
                $this->_pageData['restrans'] = $this->commonModel->getAll();
                $this->_pageData['resusers'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');

                $this->commonModel->setTableName("reception_counters_closings");
                $this->_pageData['recep'] = $this->commonModel->findOneBy(['id' => $id]);

            
                $transacIds = [];
                $expenseTransactions = [];

                $this->commonModel->setTableName("reception_counters_closings_transactions");
                $this->_pageData['counter_transactions'] = $this->commonModel->findBy(['counter_id' => $id]);

                
                $transactons = $this->_pageData['counter_transactions'];
                $this->_pageData['counter_transactions'] = [];
                foreach($transactons as $transac){
                    if($transac['income_or_expence'] == 'INCOME'){
                    
                    }else{
                        $expenseTransactions[] = $transac['id'];
                    }
                    $transacIds[] = $transac['id'];
                    $this->_pageData['counter_transactions'][$transac['id']] = $transac;
                }
            
                if(!empty($transacIds)){
                    $this->transaction->setTableName("reception_counters_closings_transaction_elements");
                    $rowsRaw = $this->transaction->findBy(['closing_transaction_id' => $transacIds]);
                    
                    foreach($rowsRaw as $row){
                        
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                        
                        if(!in_array($row['closing_transaction_id'], $expenseTransactions)){
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
                            }elseif($row['type'] == 'RECES'){

                                $selected_service = array_filter(
                                    $this->_pageData['recestation_services'],
                                    function ($e) use (&$row) {
                                        return $e['id'] == $row['service_id'];
                                    }
                                );
                            }
                            $selected_service = reset($selected_service);
                            
                            $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['service_name'] = isset($selected_service) && $selected_service['name'] ? $selected_service['name'] : 'Undefined';
                            if($row['type'] == 'INPT'){

                                $this->load->model('commonModel', 'inpatient_transactions');
                                $this->inpatient_transactions->setTableName('inpatient_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $inpatient_transactions = $this->inpatient_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $inpatient_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                            }elseif($row['type'] == 'OPD'){

                                $this->load->model('commonModel', 'opd_transactions');
                                $this->opd_transactions->setTableName('opd_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $opd_transactions = $this->opd_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $opd_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                            }elseif($row['type'] == 'EMER'){

                                $this->load->model('commonModel', 'emergency_transactions');
                                $this->emergency_transactions->setTableName('emergency_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $emergency_transactions = $this->emergency_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $emergency_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                            }elseif($row['type'] == 'DENTAL'){

                                $this->load->model('commonModel', 'dental_transactions');
                                $this->dental_transactions->setTableName('dental_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $dental_transactions = $this->dental_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $dental_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                            }elseif($row['type'] == 'ULTRA'){

                                $this->load->model('commonModel', 'ultrasound_transactions');
                                $this->ultrasound_transactions->setTableName('ultrasound_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $ultrasound_transactions = $this->ultrasound_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $ultrasound_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                            }elseif($row['type'] == 'RECES'){

                                $this->load->model('commonModel', 'recestation_transactions');
                                $this->recestation_transactions->setTableName('recestation_transactions');
                                $inptRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                $recestation_transactions = $this->recestation_transactions->findOneBy($inptRecordArray);
                                
                                $patientID = $recestation_transactions['patient_id'];
                                $this->load->model('commonModel', 'patients');
                                $this->patients->setTableName('patients');
                                $patient = $this->patients->findOneBy(['id' => $patientID]);
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['file_id'] = isset($inpatient_transactions) && $inpatient_transactions['file_id'] ? $inpatient_transactions['file_id'] : 'FileNotFound';
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';
        
                            }elseif($row['type'] == 'INPT-EXP'){
                            
                                $this->load->model('commonModel', 'inpexpenses');
                                $this->inpexpenses->setTableName('inpatient_expense_transactions');
                            
                                $expenseRecordArray = [
                                    'id' => $row['department_transaction_id']
                                ];
                                
                                $expArray = $this->inpexpenses->findOneBy($expenseRecordArray);
                                
                                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['inpExp'] = $expArray;
        
                                
                                
                            }
                        }elseif($row['type'] == 'INPT-EXP'){
                            
                            $this->load->model('commonModel', 'inpexpenses');
                            $this->inpexpenses->setTableName('inpatient_expense_transactions');
                        
                            $expenseRecordArray = [
                                'id' => $row['department_transaction_id']
                            ];
                            
                            $expArray = $this->inpexpenses->findOneBy($expenseRecordArray);
                            
                            $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['inpExp'] = $expArray;

                            
                            
                        }else{
                            
                            
                        $this->load->model('commonModel', 'expenses');
                        $this->expenses->setTableName('expenses');
                        $expenseRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        $expArray = $this->expenses->findOneBy($expenseRecordArray);
                        
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_array'] = $expArray;

                        $this->load->model('commonModel', 'expensevoucher');
                        $this->expensevoucher->setTableName('expense_vouchers');
                        $expenseVoucherArray = [
                            'id' => $expArray['voucher_id']
                        ];
                        $expVoucher = $this->expensevoucher->findOneBy($expenseVoucherArray);
                        
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_voucher_array'] = $expVoucher;
//print_array($expVoucher);
                        $this->load->model('commonModel', 'expensecategory');
                        $this->expensecategory->setTableName('expenses_categories');
                        $expenseCategoryArray = [
                            'id' => $expVoucher['exp_category_id']
                        ];
                        $expenseCategory = $this->expensecategory->findOneBy($expenseCategoryArray);
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_category'] = $expenseCategory;
                        //print_array($expenseCategory);
                        if($expVoucher['employee_id'] != 0)
                        {
                            $empid = [
                                'id' => $expVoucher['employee_id']
                            ];

                            $employee =(array) $this->aauth->get_user($expVoucher['employee_id']);
                            //print_array($employee);
                            $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['employee'] = $employee;

                        }
                            
                            
                        }
                    }
                }
                
                // print_array($this->_pageData['counter_transactions']);
                $html = $this->load->makeViewWithOutTemplate('reception_transactions', $this->_pageData, true);
                $this->makeView($html);
            }
        }else{
            $this->redirectUnauthorized();
        }
    }

}

