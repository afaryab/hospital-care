<?php

class OpdCounter extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){

        $id = array_key_exists('pid',$_GET) ? ($_GET['pid'] == '' ? 0 : $_GET['pid']) : 0;

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            // Get emergency PS number
            
            $this->load->model('commonModel', 'opd_ps_numbers');
            $this->opd_ps_numbers->setTableName('opd_ps_numbers');
            $patientPSNumberRecord = $this->opd_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            $this->_pageData['opd_ps_number'] = $patientPSNumberRecord ? $patientPSNumberRecord['ps_number'] : '';



            $this->_pageData['logged_in_user'] = $this->aauth->get_user()->email;
            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['opd_doctors'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');
            $this->_pageData['xray_tech'] = $this->aauth->getOpdDoctors('is_xray_tech');

            $this->_pageData['title'] = 'Hospital OPD Counter';
            $this->_pageData['module'] = 'Hospital OPD Counter';

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
            $this->_pageData['patient_id'] = $id;


            $this->commonModel->setTableName('patients');
            $this->_pageData['patient'] =  $this->commonModel->findOneBy(['id' => $id]);
            
            $this->_pageData['closingArray'] = $this->receptionClosingArray;
            
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

                if(array_key_exists('patient_id', $_POST) && $_POST['patient_id'] != 0){
                    
                    $patientId = $_POST['patient_id'];
                    $patient =  $this->commonModel->findOneBy(['id' => $_POST['patient_id']]);

                    $this->commonModel->setTableName('opd_patients');
                    $opdPatient =  $this->commonModel->findOneBy(['site_patient_id' => $_POST['patient_id']]);


                    if(!$opdPatient){
                        
                        $ym_escaped = $this->db->escape(date("Y-m")); // returns quoted & escaped string
                        $createdOPDPatientsThisMonth = $this->commonModel->countBy("DATE_FORMAT(created_on, '%Y-%m') = " . $ym_escaped);

                        $opdPatientId = $this->commonModel->addNew([
                            'site_patient_id' => $patientId
                        ]);

                        $opdPatient =  $this->commonModel->findOneBy(['id' => $opdPatientId]);
                        $opdPatientPSNumber = date("Y/m").'/OPD/'.str_pad($createdOPDPatientsThisMonth++, 6, '0', STR_PAD_LEFT);

                        $this->load->model('commonModel', 'opd_ps_numbers');
                        $this->opd_ps_numbers->setTableName('opd_ps_numbers');
                        $this->opd_ps_numbers->addNew([
                            'patient_id' => $patientId,
                            'opd_patient_id' => $opdPatientId,
                            'ps_number' => $opdPatientPSNumber
                        ]);
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

                        }
                        
                        if($cartService['servicetype'] == 'OPD'){
                            $doctorId = array_key_exists('service_provider', $cartService) && $cartService['service_provider'] ? $cartService['service_provider'] : null;
                            $serialNumber = 1;
                            if($doctorId != null){

                                $date = new DateTime("now");

                                $curr_date = $date->format('Y-m-d ');

                                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                                if($this->aauth->get_user()->email=='drnadeemcounter@hamzahospital.com'){
                                    $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                        'type' => $cartService['servicetype'],
                                        'doctor_id' =>  $doctorId,
                                        'DATE(created_on)'=> $curr_date,
                                        'counter_id' => $this->_pageData['counter']['id']
                                    ]);
                                }else{
                                    $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                                        'type' => $cartService['servicetype'],
                                        'doctor_id' =>  $doctorId,
                                        'service_id' => $cartService['serviceid'],
                                        'DATE(created_on)'=> $curr_date,
                                        'counter_id' => $this->_pageData['counter']['id']
                                    ]);
                                }
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
                        }
                    } 

                    redirect($this->_pageData['PRINT_RECEIPT'].$receptionTransactionId);

                }
            }
            
            
            $html = $this->load->makeViewWithOutTemplate('counter/counter_opd', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

