<?php

class LabCounter extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){

        $id = array_key_exists('patient_id',$_GET) ? ($_GET['patient_id'] == '' ? 0 : $_GET['patient_id']) : 0;

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['opd_doctors'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');
            $this->_pageData['xray_tech'] = $this->aauth->getOpdDoctors('is_xray_tech');
            $this->_pageData['dentists'] = $this->aauth->getOpdDoctors('is_dentist');
            $this->_pageData['ultradocs'] = $this->aauth->getOpdDoctors('is_ultrasound_doc');

            $this->_pageData['title'] = 'Hospital Counter';
            $this->_pageData['module'] = 'Hospital Counter';

            $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  []; $this->commonModel->getAll();
            $this->commonModel->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  []; $this->commonModel->getAll();
            $this->commonModel->setTableName('emergency_services');
            $this->_pageData['emergency_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('xray_services');
            $this->_pageData['xray_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('test_services');
            $this->_pageData['test_services'] =  $this->commonModel->getAll();
            $this->_pageData['patient_id'] = $id;
            $this->_pageData['closingArray'] = $this->receptionClosingArray;
            $this->commonModel->setTableName('inpd_rooms');
            $this->_pageData['inpd_rooms'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('panel_companies');
            $this->_pageData['panel_companies'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('dental_services');
            $this->_pageData['dental_services'] =  []; $this->commonModel->getAll();
            $this->commonModel->setTableName('ultrasound_services');
            $this->_pageData['ultrasound_services'] =  $this->commonModel->getAll();
            
            if($this->havePost() && array_key_exists('cart_services',$_POST)){
                
                $this->commonModel->setTableName('patients');
                
                $patient = [];
                $patientId = null;
                $opdPatient = [];
                $opdPatientId = null;
                $inptPatient = [];
                $inptPatientId = null;
                $emergencyPatient = [];
                $emergencyPatientId = null;
                $xrayPatient = [];
                $xrayPatientId = null;
                $testPatient = [];
                $testPatientId = null;
                $dentalPatient = [];
                $dentalPatientId = null;
                $ultraPatient = [];
                $ultraPatientId = null;
                
                $patientArray = [
                    'pateint_name' => $_POST['patient_name'],
                    'patient_contact_mobile' => $_POST['patient_contact'],
                    'patient_cnic' => $_POST['patient_cnic'],
                    'gender' => $_POST['gender'],
                    'age_days' => $_POST['age_days'],
                    'guardian' => $_POST['guardian'],
                    'patient_address' => $_POST['patient_address'],
                    'relation' => $_POST['relation'],
                ];

                if(array_key_exists('patient_id', $_POST) && $_POST['patient_id'] != 0){
                    
                    $patientId = $_POST['patient_id'];
                    $patient =  $this->commonModel->findOneBy(['id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('opd_patients');
                    // $opdPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('inpt_patients');
                    // $inptPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('emergency_patients');
                    // $emergencyPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('xray_patients');
                    // $xrayPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    $this->commonModel->setTableName('laboratory_patients');
                    $testPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('dental_patients');
                    // $dentalPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);

                    // $this->commonModel->setTableName('ultrasound_patients');
                    // $ultraPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);


                }else{
                    
                    $patientId = $this->commonModel->addNew($patientArray);
                    $patient =  $this->commonModel->findOneBy(['id' => $patientId]);

                    $this->load->model('commonModel', 'opd_patients');
                    $this->opd_patients->setTableName('opd_patients');

                    $opdPatientId = $this->opd_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $opdPatient =  $this->opd_patients->findOneBy(['id' => $opdPatientId]);

                    $this->load->model('commonModel', 'inpt_patients');
                    $this->inpt_patients->setTableName('inpt_patients');

                    $inptPatientId = $this->inpt_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $inptPatient =  $this->inpt_patients->findOneBy(['id' => $inptPatientId]);

                    $this->load->model('commonModel', 'emergency_patients');
                    $this->emergency_patients->setTableName('emergency_patients');

                    $emerPatientId = $this->emergency_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $emergencyPatient =  $this->emergency_patients->findOneBy(['id' => $emerPatientId]);

                    $this->load->model('commonModel', 'xray_patients');
                    $this->xray_patients->setTableName('xray_patients');

                    $xrayPatientId = $this->xray_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $xrayPatient =  $this->xray_patients->findOneBy(['id' => $xrayPatientId]);
                    
                    $this->load->model('commonModel', 'laboratory_patients');
                    $this->laboratory_patients->setTableName('laboratory_patients');

                    $testPatientId = $this->laboratory_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $testPatient =  $this->laboratory_patients->findOneBy(['id' => $testPatientId]);

                    $this->load->model('commonModel', 'dental_patients');
                    $this->dental_patients->setTableName('dental_patients');

                    $dentalPatientId = $this->dental_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $dentalPatient =  $this->dental_patients->findOneBy(['id' => $dentalPatientId]);

                    ////
                    $this->load->model('commonModel', 'ultrasound_patients');
                    $this->ultrasound_patients->setTableName('ultrasound_patients');

                    $ultraPatientId = $this->ultrasound_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $ultraPatient =  $this->ultrasound_patients->findOneBy(['id' => $ultraPatientId]);
                    
                    
                }

                $receptionTransaction = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'amount' => $_POST['amount_received_num'],
                    'orignal_amount'=> $_POST['amount_received_num'],
                    'customer_payed' => $_POST['customer_payed_amount'],
                    'change' => $_POST['change_amount'],
                    'income_or_expence' => 'INCOME',
                    'patient_id' => $patientId,
                    'user_id' => $this->_pageData['counter']['user_id'],
                    'type' => $_POST['payment_type']
                ];

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $receptionTransactionId = $this->reception_counters_closings_transactions->addNew($receptionTransaction);

                $updateToCounter = [
                    'closing_amount' => $this->_pageData['counter']['closing_amount'] + $_POST['amount_received_num']
                ];

                if($_POST['payment_type'] == "CARD"){
                    $updateToCounter['closing_amount_card'] = $this->_pageData['counter']['closing_amount_card'] + $_POST['amount_received_num'];
                }elseif($_POST['payment_type'] == "CREDITCARD"){
                    $updateToCounter['closing_amount_creditcard'] = $this->_pageData['counter']['closing_amount_creditcard'] + $_POST['amount_received_num'];
                }elseif($_POST['payment_type'] == "CHECK"){
                    $updateToCounter['closing_amount_atm'] = $this->_pageData['counter']['closing_amount_atm'] + $_POST['amount_received_num'];
                }else{
                    $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] + $_POST['amount_received_num'];
                }

                $this->load->model('commonModel', 'reception_counters_closings');
                $this->reception_counters_closings->setTableName('reception_counters_closings');
                $this->reception_counters_closings->updateRecord($this->_pageData['counter']['id'],$updateToCounter);

                

                foreach($_POST['cart_services'] as $cartService){
                    
                    
                    if($cartService['servicetype'] == 'OPD'){

                        $selected_service = array_filter(
                            $this->_pageData['opd_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );

                        $treatmentArray = [
                            'status' => 'OPEN',
                            'patient_id' => $patient['id'],
                            'opd_patient_id' => $opdPatient['id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => $cartService['serviceid'],
                            'service_name' => $selected_service[array_key_first($selected_service)]['name']
                        ];
    
                        $this->load->model('commonModel', 'opd_treatments');
                        $this->opd_treatments->setTableName('opd_treatments');
                        $treatmentId = $this->opd_treatments->addNew($treatmentArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'treatment_id' => $treatmentId,
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'opd_transactions');
                        $this->opd_transactions->setTableName('opd_transactions');
                        $transactionId = $this->opd_transactions->addNew($arrayToDB);

                    }elseif($cartService['servicetype'] == 'INPT'){

                        $selected_service = array_filter(
                            $this->_pageData['inpatient_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );

                        $roomid = 0;
                        if($cartService['selected_room'] != NULL)
                        {
                            $roomid = $cartService['selected_room'];

                            $this->load->model('commonModel','inprooms');
                            $this->inprooms->setTableName('inpd_rooms');
                            $Room =  $this->inprooms->findOneBy(['id' => $roomid]);
                            $rname = $Room['name'];

                            $roomArray = [

                                'is_allotted' => 1,
                            ];
                            $this->inprooms->updateRecord($roomid,$roomArray);

                        }else{
                            $rname = NULL;
                        }
                        
                        $fileArray = [
                            'status' => 'OPEN',
                            'patient_id' => $patient['id'],
                            'inpatient_patient_id' => $opdPatient['id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'room_id' => array_key_exists('selected_room', $cartService) && $cartService['selected_room'] ? $cartService['selected_room'] : null,
                            'panel_name' => array_key_exists('selected_panel', $cartService) && $cartService['selected_panel'] ? $cartService['selected_panel'] : null,
                            'will_occure_on' => null,
                            'service_id' => $cartService['serviceid'],
                            'service_name' => $selected_service[array_key_first($selected_service)]['name'],
                            'file_charges' => $cartService['pakage_amount'],
                            'file_orignal_charges' => $selected_service[array_key_first($selected_service)]['charges'],
                            'file_charges_paid' => (int)$cartService['billedamount'],
                            'room_name' => $rname,
                            'is_visiting' => array_key_exists('is_visiting', $cartService) && $cartService['is_visiting'] ? $cartService['is_visiting'] : 0,
                        ];
    
                        $this->load->model('commonModel', 'inpatient_file');
                        $this->inpatient_file->setTableName('inpatient_file');
                        $fileId = $this->inpatient_file->addNew($fileArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'file_id' => $fileId,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'inpatient_transactions');
                        $this->inpatient_transactions->setTableName('inpatient_transactions');
                        $transactionId = $this->inpatient_transactions->addNew($arrayToDB);

                    }elseif($cartService['servicetype'] == 'EMER'){

                        $selected_service = array_filter(
                            $this->_pageData['emergency_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );

                        $treatmentArray = [
                            'patient_id' => $patient['id'],
                            'emergency_patient_id' => $opdPatient['id'],
                            'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => $cartService['serviceid'],
                            'service_name' => $selected_service[array_key_first($selected_service)]['name'],
                            'treatment_charges' => (int)$cartService['billedamount']
                        ];
    
                        $this->load->model('commonModel', 'emergency_treatments');
                        $this->emergency_treatments->setTableName('emergency_treatments');
                        $treatmentId = $this->emergency_treatments->addNew($treatmentArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'treatment_id' => $treatmentId,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'emergency_transactions');
                        $this->emergency_transactions->setTableName('emergency_transactions');
                        $transactionId = $this->emergency_transactions->addNew($arrayToDB);

                    }elseif($cartService['servicetype'] == 'RADP'){

                        $selected_service = array_filter(
                            $this->_pageData['xray_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );

                        $treatmentArray = [
                            'status' => 'OPEN',
                            'patient_id' => $patient['id'],
                            'xray_patient_id' => $opdPatient['id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => $cartService['serviceid'],
                            'service_name' => $selected_service[array_key_first($selected_service)]['name'],
                            'treatment_charges' => (int)$cartService['billedamount']
                        ];
    
                        $this->load->model('commonModel', 'xray_treatments');
                        $this->xray_treatments->setTableName('xray_treatments');
                        $treatmentId = $this->xray_treatments->addNew($treatmentArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'treatment_id' => $treatmentId,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'xray_transactions');
                        $this->xray_transactions->setTableName('xray_transactions');
                        $transactionId = $this->xray_transactions->addNew($arrayToDB);
                    }elseif($cartService['servicetype'] == 'DENTAL'){

                        $selected_service = array_filter(
                            $this->_pageData['dental_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );
                        $this->load->model('commonModel', 'dental_services');
                        $this->dental_services->setTableName('dental_services');
                        $dentalservice =  $this->dental_services->findOneBy(['id' => $cartService['serviceid']]);
                        $dentalfileId = null;
                        if($dentalservice['is_fileable'] == 1)
                        {
                            $fileArray = [
                                'status' => 'OPEN',
                                'patient_id' => $patient['id'],
                                'dental_patient_id' => $opdPatient['id'],
                                'patient_is_first_visit' => 1,
                                'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                                'will_occure_on' => null,
                                'service_id' => $cartService['serviceid'],
                                'service_name' => $selected_service[array_key_first($selected_service)]['name'],
                                'file_charges' => $cartService['pakage_amount'],
                                'file_orignal_charges' => $selected_service[array_key_first($selected_service)]['charges'],
                                'file_charges_paid' => (int)$cartService['billedamount'],
                                
                            ];
        
                            $this->load->model('commonModel', 'dental_patient_file');
                            $this->dental_patient_file->setTableName('dental_patient_file');
                            $dentalfileId = $this->dental_patient_file->addNew($fileArray);

                            
                        
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'treatment_id' => NULL,
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId,
                            'file_id' => $dentalfileId,
                        ];
                        $this->load->model('commonModel', 'dental_transactions');
                        $this->dental_transactions->setTableName('dental_transactions');
                        $transactionId = $this->dental_transactions->addNew($arrayToDB);

                        }else{
                            $treatmentArray = [
                                'status' => 'OPEN',
                                'patient_id' => $patient['id'],
                                'dental_patient_id' => $dentalPatient['id'],
                                'patient_is_first_visit' => 1,
                                'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                                'will_occure_on' => date("Y-m-d h:i:s"),
                                'service_id' => $cartService['serviceid'],
                                'service_name' => $selected_service[array_key_first($selected_service)]['name'],
                                'file_id' => $dentalfileId,
                            ];
        
                            $this->load->model('commonModel', 'dental_treatments');
                            $this->dental_treatments->setTableName('dental_treatments');
                            $treatmentId = $this->dental_treatments->addNew($treatmentArray);

                            
                        
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'treatment_id' => $treatmentId,
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId,
                            'file_id' => $dentalfileId,
                        ];
                        $this->load->model('commonModel', 'dental_transactions');
                        $this->dental_transactions->setTableName('dental_transactions');
                        $transactionId = $this->dental_transactions->addNew($arrayToDB);

                        }


                    }elseif($cartService['servicetype'] == 'ULTRA'){

                        $selected_service = array_filter(
                            $this->_pageData['ultrasound_services'],
                            function ($e) use (&$cartService) {
                                return $e['id'] == $cartService['serviceid'];
                            }
                        );

                        $treatmentArray = [
                            'status' => 'OPEN',
                            'patient_id' => $patient['id'],
                            'ultrasound_patient_id' => $ultraPatient['id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => $cartService['serviceid'],
                            'service_name' => $selected_service[array_key_first($selected_service)]['name']
                        ];
    
                        $this->load->model('commonModel', 'ultrasound_treatments');
                        $this->ultrasound_treatments->setTableName('ultrasound_treatments');
                        $treatmentId = $this->ultrasound_treatments->addNew($treatmentArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null,
                            'service_id' => $cartService['serviceid'],
                            'treatment_id' => $treatmentId,
                            'amount_in_num' => (int)$cartService['billedamount'],
                            'amount_in_figure' => '',
                            'payment_type' => $_POST['payment_type'],
                            'payment_refference' => $_POST['payment_reference'],
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'units' => array_key_exists('quantity', $cartService) ? $cartService['quantity'] : 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'ultrasound_transactions');
                        $this->ultrasound_transactions->setTableName('ultrasound_transactions');
                        $transactionId = $this->ultrasound_transactions->addNew($arrayToDB);

                    }
                    if($cartService['servicetype'] == 'OPD'){

                        $doctorId = array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null;
                        $serialNumber = 1;
                        if($doctorId != null){

                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                                'doctor_id' =>  $doctorId,
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }elseif($doctorId == NULL){
                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                               
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }
                        
                
                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'service_id' => $cartService['serviceid'],
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patientId,
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => (int)$cartService['billedamount'],
                            'department_transaction_id' => $transactionId ,
                            'type' => $cartService['servicetype'],
                            'original_amount' => $selected_service[array_key_first($selected_service)]['charges'],
                            'doctor_id' =>  $doctorId,
                            'serial_number_doctor' => $serialNumber
                    
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);
                    }elseif($cartService['servicetype'] == 'DENTAL'){

                        $doctorId = array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null;
                        $serialNumber = 1;
                        if($doctorId != null){

                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                                'doctor_id' =>  $doctorId,
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }elseif($doctorId == NULL){
                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                               
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }
                        
                
                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'service_id' => $cartService['serviceid'],
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patientId,
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => (int)$cartService['billedamount'],
                            'department_transaction_id' => $transactionId ,
                            'type' => $cartService['servicetype'],
                            'original_amount' => $selected_service[array_key_first($selected_service)]['charges'],
                            'doctor_id' =>  $doctorId,
                            'serial_number_doctor' => $serialNumber
                    
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);
                    }elseif($cartService['servicetype'] == 'ULTRA'){

                        $doctorId = array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null;
                        $serialNumber = 1;
                        if($doctorId != null){

                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                                'doctor_id' =>  $doctorId,
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }elseif($doctorId == NULL){
                            $date = new DateTime("now");

                            $curr_date = $date->format('Y-m-d ');

                            $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                            $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                            $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                'type' => $cartService['servicetype'],
                               
                                'service_id' => $cartService['serviceid'],
                                'DATE(created_on)'=> $curr_date,
                                'counter_id' => $this->_pageData['counter']['id']
                            ]);
                            if(count($previousTransactions) > 0){
                                $max = max(array_column($previousTransactions, 'serial_number_doctor'));
                                if($max == null || $max == 0){
                                    $serialNumber = 1;
                                }else{
                                    $serialNumber = $max + 1;
                                }
                            }
                        }
                        
                
                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'service_id' => $cartService['serviceid'],
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patientId,
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => (int)$cartService['billedamount'],
                            'department_transaction_id' => $transactionId ,
                            'type' => $cartService['servicetype'],
                            'original_amount' => $selected_service[array_key_first($selected_service)]['charges'],
                            'doctor_id' =>  $doctorId,
                            'serial_number_doctor' => $serialNumber
                    
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);
                    }else{
                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'service_id' => $cartService['serviceid'],
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patientId,
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => (int)$cartService['billedamount'],
                            'department_transaction_id' => $transactionId ,
                            'type' => $cartService['servicetype'],
                            'original_amount' => $selected_service[array_key_first($selected_service)]['charges'],
                    
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);


                    }
                    
                    
                } 

                redirect($this->_pageData['PRINT_RECEIPT'].$receptionTransactionId);
            }
            
            
            $html = $this->load->makeViewWithOutTemplate('counter', $this->_pageData, true);
            
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function PatientSearch(){
        if($this->isLoggedIn()) {

            $table = 'patients';
           


            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id'),
                array('db' => 'pateint_name', 'table' => $table, 'dt' => 1,'as' => 'pateint_name'),
                array('db' => 'patient_contact_mobile', 'table' => $table, 'dt' => 2,'as' => 'patient_contact_mobile'),
                array('db' => 'patient_cnic', 'table' => $table, 'dt' => 3,'as' => 'patient_cnic'),
                array(
                    'db' => 'created_on',
                    'as' => 'created_on',
                    'dt' => 4,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        $html='';
                        
                        
                        $html .= '<a onclick="selectPatient(\''.$row['id'].'\',\''.$row['pateint_name'].'\',\''.$row['guardian'].'\',\''.$row['relation'].'\',\''.$row['patient_contact_mobile'].'\',\''.$row['patient_cnic'].'\',\''.$row['patient_address'].'\',\''.$row['age_days'].'\',\''.$row['gender'].'\')" class="btn btn-sm btn-default pull-right" title="Select '. $row['pateint_name'] .'" ><i class="fas fa-bolt" style="color:green;"></i></a>';
                        

                        return $html;
                    }
                ),
                array('db' => 'age_days', 'table' => $table, 'dt' => 5,'as' => 'age_days'),
                array('db' => 'gender', 'table' => $table, 'dt' => 6,'as' => 'gender'),
                array('db' => 'guardian', 'table' => $table, 'dt' => 7,'as' => 'guardian'),
                array('db' => 'patient_address', 'table' => $table, 'dt' => 8,'as' => 'patient_address'),
                array('db' => 'relation', 'table' => $table, 'dt' => 9,'as' => 'relation')
            );

            // SQL server connection information
            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );


            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             * If you just want to use the basic configuration for DataTables with PHP
             * server-side, there is no need to edit below this line.
             */

            require(__DIR__.'/../../../third_party/ssp.class.php');

            echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
            );
        }else{
            echo json_encode([]);
        }
    }
    
}

