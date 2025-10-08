<?php

class EditPatientInfo extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($receiptId = 0){
        if($this->isLoggedIn()) {

            if($receiptId != 0){

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $this->_pageData['receptionTransaction'] = $this->reception_counters_closings_transactions->findOneBy(['id' => $receiptId]);
                $rT = $this->_pageData['receptionTransaction'];
                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $this->_pageData['receptionTransactionElements'] = $this->reception_counters_closings_transaction_elements->findBy(['closing_transaction_id' => $this->_pageData['receptionTransaction']['id']]);
                $rte = $this->_pageData['receptionTransactionElements'];

                $this->load->model('commonModel', 'patients');
                $this->patients->setTableName('patients');
                $this->_pageData['patients'] = $this->patients->findOneBy(['id' => $this->_pageData['receptionTransaction']['patient_id']]);
                $id = $this->_pageData['receptionTransaction']['patient_id'];
                

                if ($this->havePost()) {
                    $edit = [
                        'pateint_name' => $_POST['name'],
                        'gender' => $_POST['gender'],
                        'age_days' => array_key_exists('age',$_POST) ? $_POST['age'] : 0,
                        'guardian' => array_key_exists('guardian',$_POST) ? $_POST['guardian'] : NULL,
                        'patient_contact_mobile' => array_key_exists('contact',$_POST) ? $_POST['contact'] : NULL,
                        'patient_cnic' => array_key_exists('cnic',$_POST) ? $_POST['cnic'] : NULL, 
                        'patient_address' => array_key_exists('address',$_POST) ? $_POST['address'] : NULL,  
                        ];
                        $this->patients->updateRecord($id,$edit);
        
                        $this->setMessage('success', 'Patient Info edited successfully!');
                        $this->activityLog('Patient Info edited successfully');
                        redirect($this->_pageData['PATIENT_RECPT_NO']);
                }

            }
            $this->_pageData['title'] = 'Edit Patient Info';
            $this->_pageData['module'] = 'Edit Patient Info';

            $html = $this->load->makeViewWithOutTemplate('edit_patient_info', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}