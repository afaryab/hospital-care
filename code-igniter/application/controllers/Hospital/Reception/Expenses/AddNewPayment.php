<?php

class AddNewPayment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){
// && $this->aauth->is_allowed('Expense Payment','Reception')
        if($this->isLoggedIn()) {
            $this->load->model('commonModel', 'expenses');
            if($this->havePost()){

                try {

                    $this->expenses->setTableName('expenses');

                    $errors = [];

                    if($_POST['amount_received_num'] === 0 || $_POST['amount_received_num'] === ''){
                        $errors[] = 'Amount Payed in numbers cannot be empty.';
                    }

                    if($_POST['amount_received_words'] === 0 || $_POST['amount_received_words'] === ''){
                        $errors[] = 'Amount Payed in words cannot be empty.';
                    }
                    if($_POST['payment_type'] === 0 || $_POST['payment_type'] === ''){
                        $errors[] = 'Payment Type Cannot be empty.';
                    }
                    if($_POST['voucher_no'] === 0 || $_POST['voucher_no'] === ''){
                        $errors[] = 'Please do provide voucher of expense payment.';
                    }

                    $this->expenses->setTableName('expense_vouchers');
                    $voucher = $this->expenses->findOneBy(['id' => $_POST['voucher_no']]);
                    $this->expenses->setTableName('expenses');

                    if(empty($voucher)){
                        $errors[] = 'Voucher number is not valid';
                    }

                    if(array_key_exists('patient_id' , $voucher) && $voucher['inpatient_file_id'] != ""){
                        $this->load->model('commonModel', 'inpatient_file');
                        $this->inpatient_file->setTableName('inpatient_file');
                        $file = $this->inpatient_file->findOneBy(['id' => $voucher['inpatient_file_id']]);

                        if($file['status'] != "OPEN"){
                            $errors[] = 'Voucher is no longer valid, Inpatient Case #'.$voucher['inpatient_file_id'].' is closed.';
                        }
                    }

                    if(empty($errors) && !empty($voucher)) {

                        $receptionTransaction = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'amount' => $_POST['amount_received_num'],
                            'orignal_amount'=> $_POST['amount_received_num'],
                            'customer_payed' => $_POST['customer_payed_amount'],
                            'change' => $_POST['change_amount'],
                            'income_or_expence' => 'EXPENSE',
                            'patient_id' => $patient['id'],
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
                            'payment_type' => $_POST['payment_type'],
                            'payment_reference' => $_POST['payment_reference'],
                            'voucher_id' => $_POST['voucher_no'],
                            'cleared_by_accounts' => 0,
                            'cleared_by_accounts_on' => NULL,
                            'cleared_by_accounts_by' => 0,
                            'submitted_for_accounts' => 0,
                            'submitted_for_accounts_on' => NULL,
                            'receaved_by' => $this->aauth->get_user_id(),
                            'payed_to' => $voucher['payed_to_employee'] == 1 ? $voucher['employee_id'] : $voucher['payed_to_others'],
                        ];

                        $voucherID = $this->expenses->addNew($dbArray);

                        $ReceptionTransactionElement = [
                            'counter_id' => $this->_pageData['counter']['id'],
                            'closing_transaction_id' => $receptionTransactionId,
                            'patient_id' => $patient['id'],
                            'user_id' => $this->_pageData['counter']['user_id'],
                            'amount' => (int)$_POST['amount_received_num'],
                            'department_transaction_id' => $transactionId ,
                            'type' => 'INPT', 
                        ];
                        $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                        $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);



                        $this->setMessage('success', 'Expense Payment #' . $voucherID . ' is added!');
                        $this->activityLog('Expense Payment #' . $voucherID . ' is added!');

                        redirect($this->_pageData['urlsToRemember']['PAYMENT_PRINT'] . $voucherID);
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

            $this->_pageData['title'] = 'Hospital Counter Expense Voucher Payment';
            $this->_pageData['module'] = 'Hospital Counter Expense Voucher Payment';
            $this->_pageData['users'] = $this->aauth->list_users();
            $html = $this->load->makeViewWithOutTemplate('expense/inpatient_voucher_payment', $this->_pageData, true);
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
            $html = $this->load->makeViewWithOutTemplate('expense/print_voucher', $this->_pageData, true);

            echo json_encode([
                'status' => !empty($voucher) ? 1 : 0,
                'data' => $voucher,
                'html' => $html
            ]);



        }else{
            $this->redirectUnauthorized();
        }


    }
    public function PrintToken($expenseId = 0){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expenses');
            $Voucher = $this->expenses->findOneBy(['id' => $expenseId]);

            $this->_pageData['transaction'] = $Voucher;
            $this->_pageData['title'] = 'Expense Voucher #'.$expenseId;
            $this->_pageData['module'] = 'PayVoucher';
            $html = $this->load->makeViewWithOutTemplate('expense/print_voucher_', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

