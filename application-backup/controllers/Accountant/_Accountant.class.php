<?php

class Accountant{
    
    protected $_cash_flow = 'Accountant/CashFlowStatements';
    protected $_expense_statements = 'Accountant/ExpenseStatement';
    protected $_income_statements = 'Accountant/IncomeStatement';
    protected $_counter_statements = 'Accountant/ReceptionStatements';
    protected $_counter_statements_json = 'Accountant/ReceptionStatements/JSON';
    protected $_other_reports = 'Accountant/OtherReports';
    protected $_add_voucher = 'Accountant/AddNewVoucher';
    protected $_list_vouchers = 'Accountant/ListVouchers';
    protected $_add_voucher_print = 'Accountant/AddNewVoucher/PrintVoucher/';
    protected $_doc_statement = 'Accountant/DoctorStatements';
    protected $_create_opd_voucher = 'Accountant/DoctorStatements/generateOPDVoucher';
    protected $_panel_payments = 'Accountant/PanelPayments';
    protected $_inp_statement = 'Accountant/InpatientStatement';
    protected $_service_statements = 'Accountant/ServiceStatement';
    protected $_reception_transactions = 'Accountant/ReceptionTransaction/Index/';
    protected $_panel_payments_json = 'Accountant/PanelPayments/JSON';
    protected $_inp_panel_payment = 'Accountant/InpatientPanelPayment/Index/';
    protected $_pay_exp_admin = 'Accountant/PayExpence';
    protected $_doc_share_statement = 'Accountant/DoctorShareStatements';
    protected $_exp_book = 'Accountant/ExpenseCashBook';
    protected $_rec_activity = 'Accountant/ReceptionActivity';
    protected $_add_inp_voucher = 'Accountant/AddNewInpVoucher';
    protected $_edit_voucher = 'Accountant/EditVoucher/Index/';
    protected $_edit_voucher_json = 'Accountant/EditVoucher/getVoucherDetailJSON/';
    protected $_service_summary = 'Accountant/ServiceSummary';
    protected $_panel_pat_statement = 'Accountant/PanelPatientStatement';
    protected $_counter_recieving = 'Accountant/CounterReceiving';
    protected $_counter_recieving_summary = 'Accountant/CounterReceivingSummary';
    protected $_submit_bill = 'Accountant/InpatientPanelPayment/submit_bill/';
    
    function getNavigation(){
        return [
            'navigations' => [
                'Accounts|AC' => [
                    [
                        'label' => 'Cash Flow',
                        'perm' => 'all',
                        'user_config' => 'is_accountant|is_super_admin',
                        'module' => 'Cash FLow Statement',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_cash_flow,
                        'order' => 100
                    ],
                    [
                        'label' => 'List Voucher',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Expense Vouchers',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_list_vouchers,
                        'order' => 100
                    ],
                    [
                        'label' => 'Income Statement',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Income Statements',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_income_statements,
                        'order' => 101
                    ],
                    [
                        'label' => 'Expense Statement',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Expense Statement',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_expense_statements,
                        'order' => 101
                    ],
                    [
                        'label' => 'Counter Statements',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Counter Statement',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_counter_statements,
                        'order' => 101
                    ],
                    [
                        'label' => 'Reception Activity',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Reception Activity',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_rec_activity,
                        'order' => 101
                    ],
                    [
                        'label' => 'Doctors Statement',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Doctor Statement',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_doc_statement,
                        'order' => 102
                    ],
                    [
                        'label' => 'Panel Payments',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Panel Payments',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_panel_payments,
                        'order' => 103
                    ],
                    [
                        'label' => 'Inpatient Statement',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Inpatient Statement',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_inp_statement,
                        'order' => 104
                    ],
                    [
                        'label' => 'Detailed Income Statement',
                        'perm' => 'all',
                        'user_config' => 'is_accountant',
                        'module' => 'Service Statements',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_service_statements,
                        'order' => 105
                    ],
                    [
                        'label' => 'Pay Expence',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Pay Expence',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_pay_exp_admin,
                        'order' => 106
                    ],
                    [
                        'label' => 'Doctor Share Statement',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Doctor Share Statement',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_doc_share_statement,
                        'order' => 107
                    ],
                    [
                        'label' => 'Expense Book',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Expense Book',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_exp_book,
                        'order' => 108
                    ],
                    [
                        'label' => 'Service Statement',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Service Summary',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_service_summary,
                        'order' => 109
                    ],
                    [
                        'label' => 'Panel Patient Statement',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Panel Patient Statement',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_panel_pat_statement,
                        'order' => 110
                    ],
                    [
                        'label' => 'Counter Receiving',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Counter Receiving',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_counter_recieving,
                        'order' => 111
                    ],
                    [
                        'label' => 'Counter Receiving Summary',
                        'perm' => 'all',
                        'user_config' => 'is_super_admin',
                        'module' => 'Counter Receiving Summary',
                        'icon' => 'far fa-file-alt',
                        'path' => $this->_counter_recieving_summary,
                        'order' => 112
                    ],
                    // [
                    //     'label' => 'Other Reports',
                    //     'perm' => 'all',
                    //     'user_config' => 'is_accountant',
                    //     'module' => 'Hospital My Statements',
                    //     'icon' => 'fas fas fa-history',
                    //     'path' => $this->_other_reports,
                    //     'order' => 101
                    // ]
                ]
            ],
            'urlsToRemember' => [
                'COUNTER_STATEMENTS' => $this->_counter_statements_json,
                'PRINT_EXPENSE_TOKEN_URL' => $this->_add_voucher_print,
                'ADD_VOUCHER' => $this->_add_voucher,
                'CREATE_OPD_VOUCHER' => $this->_create_opd_voucher,
                'REC_TRANS' => $this->_reception_transactions,
                'PANEL_PAYMENTS' => $this->_panel_payments,
                'PANEL_PAYMENTS_JSON' => $this->_panel_payments_json,
                'INP_PANEL_PAY' => $this->_inp_panel_payment,
                'ADD_INP_VOUCHER' => $this->_add_inp_voucher,
                'EDIT_VOUCHER' => $this->_edit_voucher,
                'EDIT_VOUCHER_JSON' => $this->_edit_voucher_json,
                'LIST_VOUCHER' => $this->_list_vouchers,
                'COUNTER_RECIEVING' => $this->_counter_recieving,
                'SUBMIT_BILL' => $this->_submit_bill,
                
            ]
        ];
    }
    
    
}
