<?php

class HealthcardCounter extends MY_Controller
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

            $this->_pageData['title'] = 'Hospital HealthCard Counter';
            $this->_pageData['module'] = 'Hospital HealthCard Counter';

            $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  $this->commonModel->getAll();
            $this->commonModel->setTableName('inpd_services');
            
            $this->_pageData['patient_id'] = $id;
            $this->_pageData['closingArray'] = $this->receptionClosingArray;
            
            if($this->havePost() ){

                $this->commonModel->setTableName('health_card_patients');
                
                $patient = [];
                $patientId = null;
                $opdPatient = [];
                $opdPatientId = null;
                $healthcardPatient = [];
                $healthcardPatientId = null;
                $inptPatient = [];
                $inptPatientId = null;
                $emergencyPatient = [];
                $emergencyPatientId = null;
                $xrayPatient = [];
                $xrayPatientId = null;
                $testPatient = [];
                $testPatientId = null;
                
                $patientArray = [
                    'pateint_name' => $_POST['patient_name'],
                    'patient_contact_mobile' => $_POST['patient_contact'],
                    'patient_cnic' => $_POST['patient_cnic'],
                    
                ];

                if(array_key_exists('patient_id', $_POST) && $_POST['patient_id'] != 0){
                    
                    $hid = $_POST['patient_id'];
                    $this->load->model('commonModel', 'health_card_patients');
                    $this->health_card_patients->setTableName('health_card_patients');
                    $healthcardPatient =  $this->health_card_patients->findOneBy(['id' => $hid]);
                    
                    $healthadd = [
                        'antenatal_status' => $healthcardPatient['antenatal_status']+1,
                        'last_visit' => date("Y-m-d h:i:s"),
                    ];
                    $this->health_card_patients->updateRecord($healthcardPatient['id'],$healthadd);
     
                    
                    $this->commonModel->setTableName('patients');
                    $patientId = $healthcardPatient['site_patient_id'];
                    $patient =  $this->commonModel->findOneBy(['id' => $patientId]);

                    // $this->commonModel->setTableName('health_card_patients');
                    // $this->load->model('commonModel', 'health_card_patients');
                    // $this->health_card_patients->setTableName('health_card_patients');

                    


                    $this->commonModel->setTableName('opd_patients');
                    $opdPatient =  $this->commonModel->findOneBy(['site_patient_id' => $patientId]);

                    $this->commonModel->setTableName('inpt_patients');
                    $inptPatient =  $this->commonModel->findOneBy(['site_patient_id' => $patientId]);

                    $this->commonModel->setTableName('emergency_patients');
                    $emergencyPatient =  $this->commonModel->findOneBy(['site_patient_id' => $patientId]);

                    $this->commonModel->setTableName('xray_patients');
                    $xrayPatient =  $this->commonModel->findOneBy(['site_patient_id' => $patientId]);

                    $this->commonModel->setTableName('laboratory_patients');
                    $testPatient =  $this->commonModel->findOneBy(['site_patient_id' => $patientId]);

                }else{


                    $this->load->model('commonModel', 'patients');
                    $this->patients->setTableName('patients');
                    $patientId = $this->patients->addNew($patientArray);
                    $patient =  $this->patients->findOneBy(['id' => $patientId]);

                    $this->load->model('commonModel', 'opd_patients');
                    $this->opd_patients->setTableName('opd_patients');

                    $opdPatientId = $this->opd_patients->addNew([
                        'site_patient_id' => $patientId
                    ]);
                    $opdPatient =  $this->opd_patients->findOneBy(['id' => $opdPatientId]);
//////
                    $this->load->model('commonModel', 'health_card_patients');
                    $this->health_card_patients->setTableName('health_card_patients');

                    $healthcardPatientId = $this->health_card_patients->addNew([
                        'site_patient_id' => $patientId,
                        'pateint_name' => $_POST['patient_name'],
                        'patient_contact_mobile' => $_POST['patient_contact'],
                        'patient_cnic' => $_POST['patient_cnic'],
                        'antenatal_status' => 1
                    ]);
                    $healthcardPatient =  $this->health_card_patients->findOneBy(['id' => $healthcardPatientId]);
///
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
                    
                }

                $receptionTransaction = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'amount' => 0,
                    'orignal_amount'=> 0,
                    'customer_payed' => 0,
                    'change' => 0,
                    'income_or_expence' => 'INCOME',
                    'patient_id' => $patientId,
                    'user_id' => $this->_pageData['counter']['user_id'],
                    'type' => 'CASH'
                ];

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $receptionTransactionId = $this->reception_counters_closings_transactions->addNew($receptionTransaction);


                        

                        $treatmentArray = [
                            'status' => 'OPEN',
                            'patient_id' => $patient['id'],
                            'opd_patient_id' => $opdPatient['id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => null,
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => 12,
                            'service_name' => 'Health Card Service'
                        ];
    
                        $this->load->model('commonModel', 'opd_treatments');
                        $this->opd_treatments->setTableName('opd_treatments');
                        $treatmentId = $this->opd_treatments->addNew($treatmentArray);
                        
                        $arrayToDB = [  
                            'patient_id' => $patient['id'],
                            'doctor_id' => null,
                            'service_id' => 12,
                            'treatment_id' => $treatmentId,
                            'amount_in_num' => 0,
                            'amount_in_figure' => '',
                            'payment_type' => 'CASH',
                            'payment_refference' => '',
                            'receaved_by' => $this->aauth->get_user_id(),
                            'submitted_for_accounts' => 0,
                            'cleared_by_accounts' => 0,
                            'units' => 0,
                            'reception_transaction_id' => $receptionTransactionId
                        ];
                        $this->load->model('commonModel', 'opd_transactions');
                        $this->opd_transactions->setTableName('opd_transactions');
                        $transactionId = $this->opd_transactions->addNew($arrayToDB);

                   // }
                   
                   $serialNumber = 1;
                    $date = new DateTime("now");

                    $curr_date = $date->format('Y-m-d ');

                    $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                    $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');

                    $previousTransactions = $this->reception_counters_closings_transaction_elements->findBy([
                        'type' => 'OPD',
                       
                        'service_id' => 12,
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
                    
        
                        
                
                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'service_id' => 12,
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patientId,
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => 0,
                            'department_transaction_id' => $transactionId ,
                            'type' => 'OPD',
                            'original_amount' => 0,
                            'doctor_id' =>  null,
                            'serial_number_doctor' => $serialNumber,
                    
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);
                    


                redirect($this->_pageData['PRINT_RECEIPT'].$receptionTransactionId);
            }
            
            
            $html = $this->load->makeViewWithOutTemplate('counter/counter_healthcard', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function PatientSearch(){
        if($this->isLoggedIn()) {

            $table = 'health_card_patients';
           


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
                        
                        
                        $html .= '<a onclick="selectPatient(\''.$row['id'].'\',\''.$row['patient_cnic'].'\',\''.$row['pateint_name'].'\',\''.$row['patient_contact_mobile'].'\',\''.$row['antenatal_status'].'\')" class="btn btn-sm btn-default pull-right" title="Select '. $row['patient_cnic'] .'" ><i class="fas fa-bolt" style="color:green;"></i></a>';
                        

                        return $html;
                    }
                ),
                array('db' => 'antenatal_status', 'table' => $table, 'dt' => 5,'as' => 'antenatal_status'),
                // array('db' => 'gender', 'table' => $table, 'dt' => 6,'as' => 'gender'),
                // array('db' => 'guardian', 'table' => $table, 'dt' => 7,'as' => 'guardian'),
                // array('db' => 'patient_address', 'table' => $table, 'dt' => 8,'as' => 'patient_address'),
                // array('db' => 'relation', 'table' => $table, 'dt' => 9,'as' => 'relation')
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

