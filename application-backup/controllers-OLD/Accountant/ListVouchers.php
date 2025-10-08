<?php

class ListVouchers extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
//&& $this->aauth->is_allowed('Expense Vouchers','Accounts')
        if($this->isLoggedIn() ) {
            $this->load->model('commonModel', 'expenses');
            $this->expenses->setTableName('expense_vouchers');

            $transactions = $this->expenses->getAll();

            $this->_pageData['module'] = 'Expense Vouchers';
            $this->_pageData['transactions'] = array_orderby($transactions, 'id', SORT_DESC);
            $this->_pageData['title'] = 'Transactions';

            $html = $this->load->makeViewWithOutTemplate('list_vouchers', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }
    
}