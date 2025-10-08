<?php

class AddNewInpVoucher extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }

    public function index(){
//&& $this->aauth->is_allowed('Add New Expense Voucher','Accounts')
        if($this->isLoggedIn() ) {
            $this->load->model('commonModel', 'expenses');
            if($this->havePost()){

                try {

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

                    if(empty($errors)) {
                        $dbArray = [
                            'exp_category_id' => $_POST['categories_id'],
                            'exp_amount_numbers' => $_POST['amount_received_num'],
                            'exp_amount_words' => $_POST['amount_received_words'],
                            'payed_to_employee' => $_POST['payed_to'] != 'others' ? 1 : 0 ,
                            'employee_id' => $_POST['payed_to'] != 'others' ? $_POST['payed_to'] : NULL ,
                            'payed_to_others' => $_POST['payed_to_other'],
                            'inpatient_file_id' => $_POST['inpt_id_selection'],
                            'expense_notes' => $_POST['payment_reference'],
                        ];

                        $voucherID = $this->expenses->addNew($dbArray);

                        ///////////////
                        $user = $this->aauth->get_user();
        
                        if($user->is_receptionist == 1){
                            try {
                                $this->load->model('commonModel', 'expenses');
                                $this->expenses->setTableName('expense_vouchers');
                                $voucher = $this->expenses->findOneBy(['id' => $voucherID]);
                                $errors = [];
                                
   
            
                                if(empty($errors) && !empty($voucher)) {
                                    print_array($voucher);
            
                                    $receptionTransaction = [
                                        'counter_id' => $this->_pageData['counter']['id'],
                                        'amount' => $_POST['amount_received_num'],
                                        'orignal_amount'=> $_POST['amount_received_num'],
                                        'customer_payed' => $_POST['amount_received_num'],
                                        'change' => 0,
                                        'income_or_expence' => 'EXPENSE',
                                        'user_id' => $this->_pageData['counter']['user_id'],
                                        'type' => "Cash",
                                    ];
                    
                                    $this->load->model('commonModel', 'reception_counters_closings_transactions');
                                    $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                                    $receptionTransactionId = $this->reception_counters_closings_transactions->addNew($receptionTransaction);
                    
                                    $updateToCounter = [
                                        'closing_amount' => $this->_pageData['counter']['closing_amount'] - $_POST['amount_received_num'],
                                        'expense_payed' => $this->_pageData['counter']['expense_payed'] + $_POST['amount_received_num'],
                                    ];
                    
                                    $updateToCounter['closing_amount_cash'] = $this->_pageData['counter']['closing_amount_cash'] - $_POST['amount_received_num'];
                                    
                    
                                    $this->load->model('commonModel', 'reception_counters_closings');
                                    $this->reception_counters_closings->setTableName('reception_counters_closings');
                                    $this->reception_counters_closings->updateRecord($this->_pageData['counter']['id'],$updateToCounter);
            
                                    $dbArray = [
                                        'amount_received_num' => $_POST['amount_received_num'],
                                        'amount_received_words' => $_POST['amount_received_words'],
                                        'payment_type' => 'CASH',
                                        'payment_reference' => $_POST['payment_reference'],
                                        'voucher_id' => $voucherID,
                                        'cleared_by_accounts' => 0,
                                        'cleared_by_accounts_on' => NULL,
                                        'cleared_by_accounts_by' => 0,
                                        'submitted_for_accounts' => 0,
                                        'submitted_for_accounts_on' => NULL,
                                        'receaved_by' => $this->aauth->get_user_id(),
                                        'payed_to' => $voucher['payed_to_employee'] == 1 ? $voucher['employee_id'] : $voucher['payed_to_others'],
                                    ];
                                    $this->load->model('commonModel', 'expen');
                                    $this->expen->setTableName('expenses');
            
                                    $voucherIDexp = $this->expen->addNew($dbArray);
            
                                    $ReceptionTransactionElement = [
                                        'counter_id' => $this->_pageData['counter']['id'],
                                        'closing_transaction_id' => $receptionTransactionId,
                                        'user_id' => $this->_pageData['counter']['user_id'],
                                        'amount' => (int)$_POST['amount_received_num'],
                                        'type' => 'VOUCHER-PAYMENT',
                                        'department_transaction_id' => $voucherIDexp,  
                                    ];
                                    $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                                    $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                                    $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);
            
            
            
                                    $this->setMessage('success', 'Inpatient Expense Payment #' . $voucherIDexp . ' is payed!');
                                    $this->activityLog('Inpatient Expense Payment #' . $voucherIDexp . ' is payed!');
            
                                    redirect($this->_pageData['urlsToRemember']['PRINT_RECEIPT'] . $receptionTransactionId);
                                }else{
                                    foreach ($errors as $ky => $error){
                                        $this->setMessage('error', $error);
                                    }
                                }
                            }catch (Exception $e){
                                $this->setMessage('error', $e->getMessage());
                            }
                            $this->setMessage('success', 'New Inpatient Expense Voucher #' . $voucherID . ' is added!');
                            $this->activityLog('New Inpatient Expense Voucher #' . $voucherID . ' is added!');

                        }

                        //////////////
                        // $this->setMessage('success', 'New Expense Voucher #' . $voucherID . ' is added!');
                        // $this->activityLog('New Expense Voucher #' . $voucherID . ' is added!');
                        else{
                        redirect($this->_pageData['urlsToRemember']['PRINT_EXPENSE_TOKEN_URL'] . $voucherID);
                        }
                    }else{
                        foreach ($errors as $ky => $error){
                            $this->setMessage('error', $error);
                        }
                    }
                }catch (Exception $e){
                    $this->setMessage('errors', $e->getMessage());
                }
            }
            $this->expenses->setTableName('expenses_categories');
            $this->_pageData['categories'] = $this->expenses->getAll();

            $this->_pageData['title'] = 'Make Inpatient Expense Voucher';
            $this->_pageData['module'] = 'InpatientExpenseVouchers';
            $this->_pageData['users'] = $this->aauth->list_users();
            $html = $this->load->makeViewWithOutTemplate('new_inp_voucher', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function PrintVoucher($expenseId = 0){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expense_vouchers');
            $Voucher = $this->expenses->findOneBy(['id' => $expenseId]);

            if($Voucher['payed_to_employee'] == 1)
            {
                $id = $Voucher['employee_id'];
                $users = $this->getUser($id);
                $users = (array) $users;
                $this->_pageData['user'] = $users;

            // $this->_pageData['user'] = $users;
            }

            $this->expenses->setTableName('expenses');
            $payments = $this->expenses->findBy(['voucher_id' => $expenseId]);
            $this->_pageData['payments'] = $payments;

            $this->_pageData['transaction'] = $Voucher;
            $this->_pageData['module'] = 'InpatientExpenseVouchers';
            $this->_pageData['title'] = 'Inpatient Expense Voucher #'.$expenseId;

            $html = $this->load->makeViewWithOutTemplate('print_voucher', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function PrintVoucherADV($expenseId = 0){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expense_vouchers');
            $voucher = $this->expenses->findOneBy(['id' => $expenseId]);
            $this->expenses->setTableName('expenses');
            $payments = $this->expenses->findBy(['voucher_id' => $expenseId]);
            $this->_pageData['transaction'] = $voucher;
            $this->_pageData['module'] = 'InpatientExpenseVouchers';
            $this->_pageData['payments'] = $payments;
            $html = $this->load->makeViewWithOutTemplate('vouchers/adv_exp_voucher_summery', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

}

