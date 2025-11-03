<?php

class InptPaymentsCounter extends MY_Controller
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
            
            $this->load->model('commonModel', 'inpatient_ps_numbers');
            $this->inpatient_ps_numbers->setTableName('inpatient_ps_numbers');
            $patientPSNumberRecord = $this->inpatient_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);
            

            $this->_pageData['inpatient_ps_number'] = $patientPSNumberRecord ? $patientPSNumberRecord['ps_number'] : '';

            $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');

            $this->_pageData['title'] = 'Hospital Inpatient Payments Counter';
            $this->_pageData['module'] = 'Hospital Inpatient Payments Counter';

            $this->commonModel->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  $this->commonModel->getAll();
            $this->_pageData['closingArray'] = $this->receptionClosingArray;
            
            if($this->havePost()){

                $this->load->model('commonModel', 'inpatient_file');
                $this->inpatient_file->setTableName('inpatient_file');
                $file = $this->inpatient_file->findOneBy(['id' => $_POST['medical_record']]);

                $FileUpdate = [ 'file_charges_paid' => $file['file_charges_paid'] + $_POST['amount_received_num']];

                $this->inpatient_file->updateRecord($file['id'],$FileUpdate);

                $this->load->model('commonModel', 'patients');
                $this->patients->setTableName('patients');
                $patient = $this->patients->findOneBy(['id' => $file['patient_id']]);

                $receptionTransaction = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'amount' => $_POST['amount_received_num'],
                    'orignal_amount'=> $_POST['amount_received_num'],
                    'customer_payed' => $_POST['customer_payed_amount'],
                    'change' => $_POST['change_amount'],
                    'income_or_expence' => 'INCOME',
                    'patient_id' => $patient['id'],
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

                $arrayToDB = [  
                    'patient_id' => $patient['id'],
                    'doctor_id' => $file['treatment_by'],
                    'amount_in_num' => (int)$_POST['amount_received_num'],
                    'amount_in_figure' => $_POST['amount_received_words'],
                    'payment_type' => $_POST['payment_type'],
                    'payment_refference' => $_POST['payment_reference'],
                    'receaved_by' => $this->aauth->get_user_id(),
                    'submitted_for_accounts' => 0,
                    'cleared_by_accounts' => 0,
                    'file_id' => $file['id'],
                    'units' => array_key_exists('quantity', $_POST) ? $_POST['quantity'] : 0,
                    'reception_transaction_id' => $receptionTransactionId
                ];
                $this->load->model('commonModel', 'inpatient_transactions');
                $this->inpatient_transactions->setTableName('inpatient_transactions');
                $transactionId = $this->inpatient_transactions->addNew($arrayToDB);

                $ReceptionTransactionElement = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'closing_transaction_id' => $receptionTransactionId,
                    'service_id' => $file['service_id'],
                    'patient_id' => $patient['id'],
                    'user_id' => $this->_pageData['counter']['user_id'],
                    'amount' => (int)$_POST['amount_received_num'],
                    'department_transaction_id' => $transactionId ,
                    'type' => 'INPT', 
                ];
                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);

                redirect($this->_pageData['PRINT_RECEIPT'].$receptionTransactionId);

            }
            
            
            $html = $this->load->makeViewWithOutTemplate('counter/counter_inpt_payments', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    public function FileSearch(){
        if($this->isLoggedIn()) {

            $table = 'inpatient_file';
           


            // Table's primary key
            $primaryKey = 'id';
            
            $doctors = $this->aauth->getOpdDoctors('is_inpatient_doctor');

            $this->load->model('commonModel', 'commonModel');
            $this->commonModel->setTableName('inpd_services');
            $services =  $this->commonModel->getAll();
            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id'),
                array('db' => 'pateint_name', 'table' => 'patients','as' => 'pateint_name', 'dt' => 1),
                array('db' => 'service_id', 'table' => $table, 'dt' => 2,'as' => 'service_id','formatter' => function($d) use ($services) {
                    
                    $selected_service = array_filter(
                        $services,
                        function ($e) use (&$d) {
                            return $e['id'] == $d;
                        }
                    );
                    return $selected_service[array_key_first($selected_service)]['name'];
                    
                }),
                array('db' => 'treatment_by', 'table' => $table, 'dt' => 3,'as' => 'treatment_by','formatter' => function($d) use ($doctors) {
                    
                    $selected_doctor = array_filter(
                        $doctors,
                        function ($e) use (&$d) {
                            return $e->id == $d;
                        }
                    );
                    if(isset($selected_doctor[array_key_first($selected_doctor)]->name)){
                    	return $selected_doctor[array_key_first($selected_doctor)]->name;
                    }else{
                    	$doctor_empt='';
                    	return $doctor_empt;
                    }
                    
                }),
                array(
                    'db' => 'created_on',
                    'as' => 'created_on',
                    'dt' => 4,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        $html='';
                        $html .= '<a onclick="selectPatient(\''.$row['id'].'\',\''.$row['patient_id'].'\',\''.$row['pateint_name'].'\',\''.$row['patient_contact_mobile'].'\',\''.$row['patient_cnic'].'\',\''.$row['service_id'].'\',\''.$row['file_charges'].'\',\''.$row['file_charges_paid'].'\')" class="btn btn-sm btn-default pull-right" title="Select '. $row['pateint_name'] .'" ><i class="fas fa-bolt" style="color:green;"></i></a>';
                        

                        return $html;
                    }
                ),
                array('db' => 'patient_contact_mobile', 'table' => 'patients','as' => 'patient_contact_mobile', 'dt' => 5),
                array('db' => 'patient_cnic', 'table' => 'patients','as' => 'patient_cnic', 'dt' => 6),
                array('db' => 'patient_id', 'table' => $table,'as' => 'patient_id', 'dt' => 7),
                array('db' => 'file_charges', 'table' => $table,'as' => 'file_charges', 'dt' => 8),
                array('db' => 'file_charges_paid', 'table' => $table,'as' => 'file_charges_paid', 'dt' => 9),
                array('db' => 'status', 'table' => $table,'as' => 'status', 'dt' => 10, 'searchkey' => 'OPEN'),
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

            require(__DIR__.'/../../../../third_party/ssp.class.php');

            echo json_encode(
                SSP::simplewithjoin($_GET, $sql_details, $table, $primaryKey, $columns,'patients','inpatient_file`.`patient_id` = `patients`.`id')
            );
        }else{
            echo json_encode([]);
        }
    }
    
}

