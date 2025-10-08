<?php

class ExpensePayment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');

            $this->_pageData['title'] = 'Expense Payment';
            $this->_pageData['module'] = 'Expense Payment';

            
            $this->_pageData['closingArray'] = $this->receptionClosingArray;
            
            if($this->havePost()){

                    

                $receptionTransaction = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'amount' => $_POST['amount_received_num'],
                    'orignal_amount'=> $_POST['amount_received_num'],
                    'customer_payed' => $_POST['amount_received_num'] * -1,
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

                $this->load->model('commonModel', 'expenses');
                $this->expenses->setTableName('expenses');
                $expenseRecordArray = [
                    'amount_received_num' => (int)$_POST['amount_received_num'],
                    'amount_received_words' => $_POST['amount_received_words'],
                    'payment_type' => 'CASH',
                    'payment_reference' =>  $_POST['payment_reference'],
                    'category_id' => 1,
                    'cleared_by_accounts_by' => 0
                ];
                $expID = $this->expenses->addNew($expenseRecordArray);
                
                

                $ReceptionTransactionElement = [
                    'counter_id' => $this->_pageData['counter']['id'],
                    'closing_transaction_id' => $receptionTransactionId,
                    'user_id' => $this->_pageData['counter']['user_id'],
                    'amount' => (int)$_POST['amount_received_num'],
                    'department_transaction_id' => $expID,
                    'type' => 'EXP'
                ];
                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $elementID = $this->reception_counters_closings_transaction_elements->addNew($ReceptionTransactionElement);


                redirect($this->_pageData['PRINT_RECEIPT'].$receptionTransactionId);

            
            }
            
            
            $html = $this->load->makeViewWithOutTemplate('expense_counter', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

