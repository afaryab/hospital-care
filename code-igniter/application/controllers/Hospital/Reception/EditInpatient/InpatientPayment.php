<?php

class InpatientPayment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($id = 0){

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');

            $this->_pageData['title'] = 'Inpatient Panel Payment';
            $this->_pageData['module'] = 'Inpatient Panel Payment';

            $this->commonModel->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  $this->commonModel->getAll();
            
            
            if($this->havePost()){

                $this->load->model('commonModel', 'inpatient_file');
                $this->inpatient_file->setTableName('inpatient_file');
                $file = $this->inpatient_file->findOneBy(['id' => $id]);

                $FileUpdate = [ 'file_charges_paid' => $file['file_charges_paid'] + $_POST['amount_received_num']];

                $this->inpatient_file->updateRecord($file['id'],$FileUpdate);

                $this->load->model('commonModel', 'patients');
                $this->patients->setTableName('patients');
                $patient = $this->patients->findOneBy(['id' => $file['patient_id']]);

                $this->load->model('commonModel', 'receptionClosingModel');
                $this->receptionClosingModel->setTableName('reception_counters_closings');

                $newcounter = [
                    'counter_id' => $this->_user->id,
                    'user_id' => $this->_user->id,
                    'reception_id' => $this->_user->id,
                    'status' => 'CLOSED',
                    'opening_amount' => 0,
                    'closing_amount_cash' => $_POST['amount_received_num'],
                    'closing_amount' => $_POST['amount_received_num'],
                ];
                $closingCounterId = $this->receptionClosingModel->addNew($newcounter);
                //$closingCounterId = $this->receptionClosingArray;

                // $this->load->model('commonModel','panel_payments');
                // $this->panel_payments->setTableName('panel_payments');
                // $panelID = $this->panel_payments->findOneBy(['mr_no' => $file['id']]);

                //             $panelarray = [
                //                 'amount_recieved' => $_POST['amount_received_num'],
                //                 'payment_reference' => $_POST['payment_reference'],
                //                 'status' => 'RECIEVED',
                //             ];

                //             $this->panel_payments->updateRecord($panelID['id'],$panelarray);

                $receptionTransaction = [
                    'counter_id' => $closingCounterId,
                    'amount' => $_POST['amount_received_num'],
                    'orignal_amount'=> $_POST['amount_received_num'],
                    'customer_payed' => 0,
                    'change' => 0,
                    'income_or_expence' => 'INCOME',
                    'patient_id' => $patient['id'],
                    'user_id' => $this->_user->id,
                    'type' => 'CASH',
                ];

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $receptionTransactionId = $this->reception_counters_closings_transactions->addNew($receptionTransaction);

               
                $arrayToDB = [  
                    'patient_id' => $patient['id'],
                    'doctor_id' => $file['treatment_by'],
                    'amount_in_num' => (int)$_POST['amount_received_num'],
                    'amount_in_figure' => $_POST['amount_received_words'],
                    'payment_type' => 'CASH',
                    'receaved_by' => $this->_user->id,
                    'submitted_for_accounts' => 0,
                    'cleared_by_accounts' => 0,
                    'file_id' => $file['id'],
                    'units' => 0,
                    'reception_transaction_id' => $receptionTransactionId
                ];
                $this->load->model('commonModel', 'inpatient_transactions');
                $this->inpatient_transactions->setTableName('inpatient_transactions');
                $transactionId = $this->inpatient_transactions->addNew($arrayToDB);

                $ReceptionTransactionElement = [
                    'counter_id' => $closingCounterId,
                    'closing_transaction_id' => $receptionTransactionId,
                    'service_id' => $file['service_id'],
                    'patient_id' => $patient['id'],
                    'user_id' => $this->_user->id,
                    'amount' => (int)$_POST['amount_received_num'],
                    'department_transaction_id' => $transactionId ,
                    'type' => 'INPT', 
                ];
                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);



                redirect($this->_pageData['INPATIENT_MR_NO']);

            }
            
            
            $html = $this->load->makeViewWithOutTemplate('inp_payments', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

