<?php

class CloseCounter extends MY_Controller
{



    public function __construct()
    {
        parent::__construct();
        define('MODULE','Reception');

    }



    public function index(){


        if($this->isLoggedIn()) {
            
            $this->load->model('commonModel', 'receptionModel');
            $this->receptionModel->setTableName('reception_counters');
            $this->recptionArray = $this->receptionModel->findOneBy(['id' => $this->_user->reception_id]);

            $this->load->model('commonModel', 'receptionClosingModel');
            $this->receptionClosingModel->setTableName('reception_counters_closings');

            $this->recptionClosingArray = $this->receptionClosingModel->findOneBy(['user_id' => $this->_user->id,'reception_id' => $this->_user->reception_id,'status' => 'OPEN']);

            if($this->havePost()){

                if(array_key_exists('do_agree',$_POST) && $_POST['do_agree'] == 'on'){
                    $this->receptionClosingModel->updateRecord($this->recptionClosingArray['id'],['status' => "CLOSED"]);

                    redirect($this->_pageData['urlsToRemember']['LOGOUT']);
                }

            }
            
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
            $this->commonModel->setTableName('dental_services');
            $this->_pageData['dental_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('ultrasound_services');
            $this->_pageData['ultrasound_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('recestation_services');
            $this->_pageData['recestation_services'] =  $this->commonModel->getAll();


            $transacIds = [];
            $expenseTransactions = [];

            $transactons = $this->_pageData['counter_transactions'];
            $this->_pageData['counter_transactions'] = [];
            foreach($transactons as $transac){
                if($transac['income_or_expence'] == 'INCOME'){
                    $transacIds[] = $transac['id'];
                }else{
                    $expenseTransactions[] = $transac['id'];
                }
                $this->_pageData['counter_transactions'][$transac['id']] = $transac;
            }


            $this->transaction->setTableName("reception_counters_closings_transaction_elements");
            
            $rowsRaw = [];
            
            if(count($expenseTransactions) > 0){
                $rowsRaw = $this->transaction->findBy(['closing_transaction_id' => array_merge($transacIds,$expenseTransactions)]);    
            }
            foreach($rowsRaw as $row){
                
                $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                
                if(in_array($row['closing_transaction_id'], $expenseTransactions) == false){
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
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_id'] = isset($patient) && $patient['id'] ? $patient['id'] : '';

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
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['patient_id'] = isset($opd_transactions) && $opd_transactions['id'] ? $opd_transactions['id'] : '--';
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                    }elseif($row['type'] == 'EMER'){

                        $this->load->model('commonModel', 'emer_transactions');
                        $this->emer_transactions->setTableName('emergency_transactions');
                        $inptRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        $emer_transactions = $this->emer_transactions->findOneBy($inptRecordArray);
                        
                        $patientID = $emer_transactions['patient_id'];
                        $this->load->model('commonModel', 'patients');
                        $this->patients->setTableName('patients');
                        $patient = $this->patients->findOneBy(['id' => $patientID]);
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['patient_id'] = isset($emer_transactions) && $emer_transactions['id'] ? $emer_transactions['id'] : '--';
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
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['patient_id'] = isset($dental_transactions) && $dental_transactions['id'] ? $dental_transactions['id'] : '--';
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
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['patient_id'] = isset($ultrasound_transactions) && $ultrasound_transactions['id'] ? $ultrasound_transactions['id'] : '--';
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
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['patient_id'] = isset($recestation_transactions) && $recestation_transactions['id'] ? $recestation_transactions['id'] : '--';
                        $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['pateint_name'] = isset($patient) && $patient['pateint_name'] ? $patient['pateint_name'] : 'Patient No Found';

                    }
                }else{
                    
                    $this->load->model('commonModel', 'expenses');
                    $this->expenses->setTableName('expenses');
                    $expenseRecordArray = [
                        'id' => $row['department_transaction_id']
                    ];
                    $expArray = $this->expenses->findOneBy($expenseRecordArray);
                    
                    $this->_pageData['counter_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_array'] = $expArray;

                    
                    
                }
            }
            
            $this->_pageData['title'] = 'Closing Time';
            $this->_pageData['module'] = 'Closing Time';

            $html = $this->load->makeViewWithOutTemplate('closing_transactions', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }

        

        
    }

    
}


