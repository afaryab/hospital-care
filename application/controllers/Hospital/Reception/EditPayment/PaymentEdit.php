<?php

class PaymentEdit extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($receiptId = 0){
        if($this->isLoggedIn()) {

            if($receiptId != 0){


                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $this->_pageData['receptionTransactionElements'] = $this->reception_counters_closings_transaction_elements->findOneBy(['id' => $receiptId]);
                $rte = $this->_pageData['receptionTransactionElements'];

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $this->_pageData['receptionTransaction'] = $this->reception_counters_closings_transactions->findOneBy(['id' => $this->_pageData['receptionTransactionElements']['closing_transaction_id']]);
                $rT = $this->_pageData['receptionTransaction'];
                $transaction_type=$rT['type'];
                if($rte['edited_amount'] == NULL)
                {
                    $rteorg = $rte['amount'];
                  
                }else{
                    $rteorg = $rte['edited_amount'];
                  
                }

                if($rT['edited_amount'] == NULL)
                {
                    $rtorg = $rT['amount'];
                }else{
                    $rtorg = $rT['edited_amount'];
                }
                    

                if ($this->havePost()) {

                    $edit = [
                        'amount' => $_POST['refund_amount'],
                        'edited_amount' => $rteorg,
                    ];
                    $this->reception_counters_closings_transaction_elements->updateRecord($rte['id'],$edit);

                    if($rte['type'] == 'OPD'){
                   
                    
                        $this->load->model('commonModel', 'opdtransactions');
                        $this->opdtransactions->setTableName('opd_transactions');
                        $opd = $this->opdtransactions->findOneBy(['id' => $rte['department_transaction_id']]);

                        if($opd['edited_amount'] == NULL)
                        {
                            $org = $opd['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->opdtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->opdtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

    
                        
                    }elseif($rte['type'] == 'INPT'){
    
                        $this->load->model('commonModel', 'inptransactions');
                        $this->inptransactions->setTableName('inpatient_transactions');
                        $inp = $this->inptransactions->findOneBy(['id' => $rte['department_transaction_id']]);

                        $this->load->model('commonModel', 'inpfile');
                        $this->inpfile->setTableName('inpatient_file');
                        $fil = $this->inpfile->findOneBy(['id' => $inp['file_id']]);

                        if($fil['edited_amount'] == NULL)
                        {
                            $filorg = $fil['file_charges_paid'];
                        }else{
                            $filorg = $fil['edited_amount'];
                        }


                        if($inp['edited_amount'] == NULL)
                        {
                            $org = $inp['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->inptransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->inptransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

                        if($_POST['refund_amount'] < $inp['amount_in_num']){

                            $res = $inp['amount_in_num'] - $_POST['refund_amount'];
                            $amnt = $fil['file_charges_paid'] - $res;
                        
                            $edit = [
                                'file_charges_paid' => $amnt,
                                'edited_amount' => $filorg,
                            ];
                            $this->inpfile->updateRecord($inp['file_id'],$edit);
                        
                        }elseif($_POST['refund_amount'] >= $inp['amount_in_num']){
                        
                            $res =  $_POST['refund_amount'] - $inp['amount_in_num']; 
                            $amnt = $fil['file_charges_paid'] + $res;
                        
                            $edit = [
                                'amount' => $amnt,
                                'edited_amount' => $filorg,
                            ];
                            $this->inpfile->updateRecord($inp['file_id'],$edit);
                        
                        }


                    }elseif($rte['type'] == 'EMER'){
    
                        $this->load->model('commonModel', 'emergencytransactions');
                        $this->emergencytransactions->setTableName('emergency_transactions');  
                        $emr = $this->emergencytransactions->findOneBy(['id' => $rte['department_transaction_id']]);
    
                        if($emr['edited_amount'] == NULL)
                        {
                            $org = $emr['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->emergencytransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->emergencytransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

                    }elseif($rte['type'] == 'DENTAL'){
                        
                        $this->load->model('commonModel', 'dentaltransactions');
                        $this->dentaltransactions->setTableName('dental_transactions');
                        $dntl = $this->dentaltransactions->findOneBy(['id' => $rte['department_transaction_id']]);
    
                        if($dntl['edited_amount'] == NULL)
                        {
                            $org = $dntl['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->dentaltransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->dentaltransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

                    }elseif($rte['type'] == 'ULTRA'){
                        
                        $this->load->model('commonModel', 'ultrasoundtransactions');
                        $this->ultrasoundtransactions->setTableName('ultrasound_transactions');  
                        $ult = $this->ultrasoundtransactions->findOneBy(['id' => $rte['department_transaction_id']]);
    
                        if($ult['edited_amount'] == NULL)
                        {
                            $org = $ult['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->ultrasoundtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->ultrasoundtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

                    }elseif($rte['type'] == 'RECES'){
                        
                        $this->load->model('commonModel', 'recestationtransactions');
                        $this->recestationtransactions->setTableName('recestation_transactions');  
                        $reces = $this->recestationtransactions->findOneBy(['id' => $rte['department_transaction_id']]);
    
                        if($reces['edited_amount'] == NULL)
                        {
                            $org = $reces['amount_in_num'];
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                                'edited_amount' => $org,
                            ];
                            $this->recestationtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }else{
                            $edit = [
                                'amount_in_num' => $_POST['refund_amount'],
                            ];
                            $this->recestationtransactions->updateRecord($rte['department_transaction_id'],$edit);
                        }

                    }
                    

                    if($_POST['refund_amount'] < $rte['amount']){

                        $result = $rte['amount'] - $_POST['refund_amount'];
                        $rec = $rT['amount'] - $result;

                        $edit = [
                            'amount' => $rec,
                            'edited_amount' => $rtorg,
                        ];
                        $this->reception_counters_closings_transactions->updateRecord($rte['closing_transaction_id'],$edit);

                        $updateToCounter = [
                            'closing_amount' => $this->_pageData['counter']['closing_amount'] - $result
                        ];
        
                        if($transaction_type=="CASH"){
                            $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] - $result;
                        }if($transaction_type=="CARD"){
                            $updateToCounter['closing_amount_card'] = $this->_pageData['counter']['closing_amount_card'] - $result;
                        }elseif($transaction_type=="CREDITCARD"){
                            $updateToCounter['closing_amount_creditcard'] = $this->_pageData['counter']['closing_amount_creditcard'] - $result;
                        }else{
                            $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] - $result;
                        }
        
                        $this->load->model('commonModel', 'reception_counters_closings');
                        $this->reception_counters_closings->setTableName('reception_counters_closings');
                        $this->reception_counters_closings->updateRecord($this->_pageData['counter']['id'],$updateToCounter);


                    }elseif($_POST['refund_amount'] >= $rte['amount']){

                        $result =  $_POST['refund_amount'] - $rte['amount']; 
                        $rec = $rT['amount'] + $result;

                        $edit = [
                            'amount' => $rec,
                            'edited_amount' => $rtorg,
                        ];
                        $this->reception_counters_closings_transactions->updateRecord($rte['closing_transaction_id'],$edit);

                        $updateToCounter = [
                            'closing_amount' => $this->_pageData['counter']['closing_amount'] + $result
                        ];
        
                        if($transaction_type=="CASH"){
                            $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] + $result;
                        }if($transaction_type=="CARD"){
                            $updateToCounter['closing_amount_card'] = $this->_pageData['counter']['closing_amount_card'] + $result;
                        }elseif($transaction_type=="CREDITCARD"){
                            $updateToCounter['closing_amount_creditcard'] = $this->_pageData['counter']['closing_amount_creditcard'] + $result;
                        }else{
                            $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] + $result;
                        }
        
                        $this->load->model('commonModel', 'reception_counters_closings');
                        $this->reception_counters_closings->setTableName('reception_counters_closings');
                        $this->reception_counters_closings->updateRecord($this->_pageData['counter']['id'],$updateToCounter);


                    }                



                    

                    
        
                    $this->setMessage('success', 'Patient Payment Refunded successfully!');
                    $this->activityLog('Patient Payment Refunded successfully');
                    redirect($this->_pageData['TRANSACTION_NO']);
                }

            }
            $this->_pageData['title'] = 'Payment Refund';
            $this->_pageData['module'] = 'Payment Refund';

            $html = $this->load->makeViewWithOutTemplate('edit_payment', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}