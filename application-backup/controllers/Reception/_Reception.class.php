<?php

class Reception{
    
    protected $_open_counter = 'Reception/OpenCounter/Index';
    protected $_close_counter = 'Reception/CloseCounter/Index';


    protected $_expense_voucher_payment = 'Reception/VoucherPayment/Index';
    protected $_expense_voucher_payment_json = 'Reception/VoucherPayment/getVoucherDetailJSON/';
    protected $_inpatient_expense_payment = 'Reception/InpatientExpensePayment/Index';
    protected $_inpatient_expense_payment_inpt_search = 'Reception/InpatientExpensePayment/FileSearch';
    protected $_expense_payment = 'Reception/ExpensePayment/Index';

    protected $_my_transactions = 'Reception/MyTransactions';
    protected $_my_transactions_history = 'Reception/MyTransactions/history';

    protected $_my_statements = 'Reception/MyStatements';
    protected $_my_statements_json = 'Reception/MyStatements/JSON';

    protected $_print_reciept = 'Reception/PrintReciept/Index/';
    protected $_print_reciept_dup = 'Reception/PrintRecieptDuplicate/Index/';
    protected $_print_inpatient_file = 'Reception/PrintInpatientFile/Index/';
    protected $_list_patients = 'Reception/ListPatients';
    protected $_json_patients = 'Reception/ListPatients/JSON';

    protected $_add_voucher = 'Accountant/AddNewVoucher';
    protected $_add_inp_voucher = 'Accountant/AddNewInpVoucher';
    protected $_list_healthcard_patients = 'Reception/ListHealthCardPatients';
    protected $_json_healthcard_patients = 'Reception/ListHealthCardPatients/JSON';
    
    function getNavigation(){
        return [
            'navigations' => [
                'Reception|REC' => [
                    [
                        'label' => 'Expense Payment',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'Expense Payment',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_add_voucher,
                        'order' => 100,
                        'children' => [
                            // [
                            //     'label' => 'Non Voucher',
                            //     'perm' => 'all',
                            //     'user_config' => 'is_receptionist',
                            //     'module' => 'Expense Payment',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'path' => $this->_expense_payment,
                            //     'order' => 100
                            // ],
                            [
                                'label' => 'Voucher Payment',
                                'perm' => 'all',
                                'user_config' => 'is_receptionist',
                                'module' => 'Voucher Payment',
                                'icon' => 'fas fa-file-invoice',
                                'path' => $this->_expense_voucher_payment,
                                'order' => 101
                            ],
                            // [
                            //     'label' => 'Inpatient Expense',
                            //     'perm' => 'all',
                            //     'user_config' => 'is_receptionist',
                            //     'module' => 'Inpatient Expense',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'path' => $this->_inpatient_expense_payment,
                            //     'order' => 102
                            // ]
                            [
                                'label' => 'Inpatient Expense Payment',
                                'perm' => 'all',
                                'user_config' => 'is_receptionist',
                                'module' => 'InpatientExpenseVouchers',
                                'icon' => 'fas fa-file-invoice',
                                'path' => $this->_add_inp_voucher,
                                'order' => 102
                            ]
                            
                        ]
                    ],
                    
                    [
                        'label' => 'Receipt Print',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'Receipt Print',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_print_reciept_dup,
                        'order' => 102
                    ],
                    [
                        'label' => 'My Transactions',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'My Transactions',
                        'icon' => 'fas fa-file-invoice',
                        'path' => $this->_my_transactions,
                        'order' => 100
                    ],
                    [
                        'label' => 'My Statements',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'My Statements',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_my_statements,
                        'order' => 101
                    ],
                    [
                        'label' => 'Patients',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'patients',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_list_patients,
                        'order' => 103
                    ],
                    [
                        'label' => 'Health Card Patients',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'patients',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_list_healthcard_patients,
                        'order' => 104
                    ],
                    [
                        'label' => 'Print Inpatient File',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'patients',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_print_inpatient_file,
                        'order' => 105
                    ]
                ]
            ],
            'urlsToRemember' => [
                'OPEN_COUNTER' => $this->_open_counter,
                'CLOSE_COUNTER' => $this->_close_counter,
                'MY_STATEMENTS_JSON' => $this->_my_statements_json,
                'EXPENSE_FILE_SEARCH' => $this->_inpatient_expense_payment_inpt_search,
                'PRINT_RECEIPT' => $this->_print_reciept,
                'EXPENSE_VOUCHER' => $this->_expense_voucher_payment_json,
                'PATIENTS_LIST' => $this->_list_patients,
                'PATIENTS_JSON_URL' => $this->_json_patients,
                'HEALTHCARD_PATIENTS_LIST' => $this->_list_healthcard_patients,
                'HEALTHCARD_PATIENTS_JSON_URL' => $this->_json_healthcard_patients,
                'PRINT_RECEIPT_DUP' => $this->_print_reciept_dup,
                'PRINT_INPATIENT_FILE' => $this->_print_inpatient_file,

                
            ]
        ];
    }
    
    
}
