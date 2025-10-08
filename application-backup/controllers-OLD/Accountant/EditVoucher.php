<?php

class EditVoucher extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index($id){
// && $this->aauth->is_allowed('Expense Payment','Reception')
        if($this->isLoggedIn() && $id != 0) {
            
            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expense_vouchers');
            $voucher = $this->expenses->findOneBy(['id' => $id]);
            $this->_pageData['voucherdetails'] = $voucher;
            $this->load->model('commonModel', 'category');
            $this->category->setTableName('expenses_categories');
            $this->_pageData['expCat'] = $this->category->findOneBy(['id' => $voucher['exp_category_id']]);
            $this->_pageData['categories'] = $this->category->getAll();
            
            if($this->havePost()){

                try {

                    // print_array($_POST);
                    $this->expenses->setTableName('expense_vouchers');

                    $errors = [];

                    if($_POST['amount_received_num'] === 0 || $_POST['amount_received_num'] === ''){
                        $errors[] = 'Amount Payed in numbers cannot be empty.';
                    }

                    if($_POST['amount_received_words'] === 0 || $_POST['amount_received_words'] === ''){
                        $errors[] = 'Amount Payed in words cannot be empty.';
                    }
                    if($_POST['categories_id'] === 0 || $_POST['categories_id'] === ''){
                        $errors[] = 'Please do provide category of expense.';
                    }
                    if($_POST['categories_id'] === 2 && $_POST['payed_to'] == ''){
                        $errors[] = 'Please do provide user for this category of expense.';
                    }

                    $this->load->model('commonModel', 'expen');
                    $this->expen->setTableName('expenses');
                    $voucherIDexp = $this->expen->findOneBy(['voucher_id' => $id]);
                    
                    $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                    $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                    $recTransEleId = $this->reception_counters_closings_transaction_elements->findOneBy(['type' => 'VOUCHER-PAY','department_transaction_id' => $voucherIDexp['id']]);

                    $this->load->model('commonModel', 'reception_counters_closings_transactions');
                    $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                    $recTransId = $this->reception_counters_closings_transactions->findOneBy(['id' => $recTransEleId['closing_transaction_id']]);

                    $this->load->model('commonModel', 'reception_counters_closings');
                    $this->reception_counters_closings->setTableName('reception_counters_closings');
                    $recClosing = $this->reception_counters_closings->findOneBy(['id' => $recTransId['counter_id']]);

                    if($recClosing['status'] == 'CLOSED'){
                        $errors[] = 'Counter Is Closed ';
                    }


                    if(empty($errors)) {
                        $dbArray = [
                            'exp_category_id' => $_POST['categories_id'],
                            'exp_amount_numbers' => $_POST['amount_received_num'],
                            'exp_amount_words' => $_POST['amount_received_words'],
                            'payed_to_employee' => $_POST['payed_to'] != 'others' ? 1 : 0 ,
                            'employee_id' => $_POST['payed_to'] != 'others' ? $_POST['payed_to'] : NULL ,
                            'payed_to_others' => array_key_exists('payed_to_other',$_POST) ? $_POST['payed_to_other'] : NULL,
                            'inpatient_file_id' => array_key_exists('inpt_id_selection',$_POST) ? $_POST['inpt_id_selection'] : NULL,
                            'expense_notes' => $_POST['payment_reference'],
                        ];
                        

                        $voucherID = $this->expenses->updateRecord($id,$dbArray);

                        $dbArr = [
                            'amount_received_num' => $_POST['amount_received_num'],
                            'amount_received_words' => $_POST['amount_received_words'],
                            'payment_type' => 'CASH',
                            'payment_reference' => $_POST['payment_reference'],
                            'payed_to' => $_POST['payed_to'] != 'others' ? $_POST['payed_to'] : NULL,
                        ];
                        $this->load->model('commonModel', 'expen');
                        $this->expen->setTableName('expenses');
                        $voucherIDexp = $this->expen->findOneBy(['voucher_id' => $id]);
                        $this->expen->updateRecord($voucherIDexp['id'],$dbArr);

                        
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $recTransEleId = $this->reception_counters_closings_transaction_elements->findOneBy(['type' => 'VOUCHER-PAY','department_transaction_id' => $voucherIDexp['id']]);
                        //print_array($recTransEleId);
                        if($recTransEleId['edited_amount'] == NULL)
                        {
                            $rteorg = $recTransEleId['amount'];
                          
                        }else{
                            $rteorg = $recTransEleId['edited_amount'];
                          
                        }
                        
                        $ReceptionTransactionElement = [
                            //'closing_transaction_id' => $receptionTransactionId,
                            'amount' => (int)$_POST['amount_received_num'],
                            'edited_amount' => $rteorg,
                        ];
                        $this->reception_counters_closings_transaction_elements->updateRecord($recTransEleId['id'],$ReceptionTransactionElement);

                        $this->load->model('commonModel', 'reception_counters_closings_transactions');
                        $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                        $recTransId = $this->reception_counters_closings_transactions->findOneBy(['id' => $recTransEleId['closing_transaction_id']]);
                        
                        if($recTransId['edited_amount'] == NULL)
                        {
                            $rtorg = $recTransId['amount'];
                        }else{
                            $rtorg = $recTransId['edited_amount'];
                        }

                
                        if($_POST['amount_received_num'] < $recTransId['amount']){

                            $result = $recTransId['amount'] - $_POST['amount_received_num'];
                            //$rec = $rT['amount'] - $result;
    
                            $receptionTransaction = [
                                'amount' => $_POST['amount_received_num'],
                                'orignal_amount'=> $_POST['amount_received_num'],
                                'customer_payed' => $_POST['amount_received_num'],
                                'edited_amount' => $rtorg,
                            ];
                            $this->reception_counters_closings_transactions->updateRecord($recTransId['id'],$receptionTransaction);
    
                            $this->load->model('commonModel', 'reception_counters_closings');
                            $this->reception_counters_closings->setTableName('reception_counters_closings');
                            $recClosing = $this->reception_counters_closings->findOneBy(['id' => $recTransId['counter_id']]);
                        
                            $camount = $recClosing['closing_amount'];
                            $exppay = $recClosing['expense_payed'];
                            $camntcash = $recClosing['closing_amount_cash'];

                            $updateToCounter = [
                                'closing_amount' => $camount + $result,
                                'expense_payed' => $exppay - $result,
                                'closing_amount_cash' => $camntcash + $result,
                            ];
            
                        
                            $this->reception_counters_closings->updateRecord($recClosing['id'],$updateToCounter);
    
    
                        }elseif($_POST['amount_received_num'] >= $recTransId['amount']){
    
                            $result =  $_POST['amount_received_num'] - $recTransId['amount']; 
                            //$rec = $rT['amount'] + $result;
    
                            $receptionTransaction = [
                                'amount' => $_POST['amount_received_num'],
                                'orignal_amount'=> $_POST['amount_received_num'],
                                'customer_payed' => $_POST['amount_received_num'],
                                'edited_amount' => $rtorg,
                            ];
                            $this->reception_counters_closings_transactions->updateRecord($recTransId['id'],$receptionTransaction);
    
                            $this->load->model('commonModel', 'reception_counters_closings');
                            $this->reception_counters_closings->setTableName('reception_counters_closings');
                            $recClosing = $this->reception_counters_closings->findOneBy(['id' => $recTransId['counter_id']]);
                        
                            $camount = $recClosing['closing_amount'];
                            $exppay = $recClosing['expense_payed'];
                            $camntcash = $recClosing['closing_amount_cash'];

                            $updateToCounter = [
                                'closing_amount' => $camount - $result,
                                'expense_payed' => $exppay + $result,
                                'closing_amount_cash' => $camntcash - $result,
                            ];
            
                            $this->reception_counters_closings->updateRecord($recClosing['id'],$updateToCounter);
    
                        }                 
                            

                        $this->setMessage('success', 'Expense Voucher edited');
                        $this->activityLog('Expense Voucher edited');
                        redirect($this->_pageData['LIST_VOUCHER']);

                       
                    }else{
                        foreach ($errors as $ky => $error){
                            $this->setMessage('error', $error);
                        }
                    }
                }catch (Exception $e){
                    $this->setMessage('errors', $e->getMessage());
                }
            }

            

            $this->_pageData['title'] = 'Edit Voucher';
            $this->_pageData['module'] = 'Edit Voucher';
            $this->_pageData['p_module'] = 'Expense Payment';
            $this->_pageData['users'] = $this->aauth->list_users();
            $html = $this->load->makeViewWithOutTemplate('edit_voucher', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function getVoucherDetailJSON($id){

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expense_vouchers');
            $voucher = $this->expenses->findOneBy(['id' => $id]);
            $this->expenses->setTableName('expenses');
            $payments = $this->expenses->findBy(['voucher_id' => $id]);
            $this->_pageData['transaction'] = $voucher;
            $this->_pageData['payments'] = $payments;
            $html = $this->load->makeViewWithOutTemplate('print_voucher', $this->_pageData, true);

            echo json_encode([
                'status' => !empty($voucher) ? 1 : 0,
                'data' => $voucher,
                'html' => $html
            ]);



        }else{
            $this->redirectUnauthorized();
        }


    }
    
}


