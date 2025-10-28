<?php

class Hospital{
    
    protected $_hospital_counter_url = 'Hospital/Reception/Counter/Index';
    protected $_hospital_patient_search_url = 'Hospital/Reception/Counters/PatientSearch/Index';

    protected $_hospital_emergency_counter = 'Hospital/Reception/Counters/EmergencyCounter/Index';
    protected $_hospital_opd_counter = 'Hospital/Reception/Counters/OpdCounter/Index';
    protected $_hospital_new_inpt_counter = 'Hospital/Reception/Counters/NewInptCounter/Index';
    protected $_hospital_dental_counter = 'Hospital/Reception/Counters/DentalCounter/Index';
    protected $_hospital_lab_counter = 'Hospital/Reception/Counters/LabCounter/Index';
    protected $_hospital_xray_counter = 'Hospital/Reception/Counters/XrayCounter/Index';
    protected $_hospital_ultrasound_counter = 'Hospital/Reception/Counters/UltrasoundCounter/Index';
    
    
    protected $_hospital_inpt_search_url = 'Hospital/Reception/Counters/PatientSearch/Index';
    protected $_hospital_inpt_payments_counter = 'Hospital/Reception/Counters/InptPaymentsCounter/Index';
    protected $_hospital_inpt_payments_counter_file_serach = 'Hospital/Reception/Counters/InptPaymentsCounter/FileSearch';

    

    protected $_hospital_opd_register = 'Hospital/Reception/Register/OPDRegister';
    protected $_hospital_opd_register_json = 'Hospital/Reception/Register/OPDRegister/JSON';

    protected $_hospital_inpt_register = 'Hospital/Reception/Register/INPTRegister';
    protected $_hospital_inpt_register_json = 'Hospital/Reception/Register/INPTRegister/JSON';

    protected $_hospital_inp_profile = 'Hospital/Reception/Register/Inpatient/Profile/Index/';

    protected $_hospital_emer_register = 'Hospital/Reception/Register/EMERRegister';
    protected $_hospital_emer_register_json = 'Hospital/Reception/Register/EMERRegister/JSON';

    protected $_hospital_path_register = 'Hospital/Reception/Register/PATHRegister';
    protected $_hospital_path_register_json = 'Hospital/Reception/Register/PATHRegister/JSON';

    protected $_hospital_rad_register = 'Hospital/Reception/Register/RADPRegister';
    protected $_hospital_rad_register_json = 'Hospital/Reception/Register/RADPRegister/JSON';
    


    protected $_hospital_voucher_expense_counter = 'Hospital/Reception/Expenses/AddNewPayment/Index/';
    protected $_hospital_inpt_expense_counter = 'Hospital/Reception/Expenses/InptExpenseCounter/Index/';
    protected $_hospital_expense_voucher_payment_json = 'Hospital/Reception/Expenses/AddNewPayment/getVoucherDetailJSON/';

    protected $_hospital_dental_register = 'Hospital/Reception/Register/DENTALRegister';
    protected $_hospital_dental_register_json = 'Hospital/Reception/Register/DENTALRegister/JSON';

    protected $_hospital_ultra_register = 'Hospital/Reception/Register/ULTRARegister';
    protected $_hospital_ultra_register_json = 'Hospital/Reception/Register/ULTRARegister/JSON';

    protected $_patient_receipt_id = 'Hospital/Reception/Edit/EditPatient/Index/';
    protected $_patient_edit = 'Hospital/Reception/Edit/EditPatientInfo/Index/';
    
    protected $_inpatient_mr_no = 'Hospital/Reception/EditInpatient/EditFile/Index/';
    protected $_inpatient_edit = 'Hospital/Reception/EditInpatient/EditInpatientInfo/Index/';
    protected $_inpatient_payment = 'Hospital/Reception/EditInpatient/InpatientPayment/Index/';

    protected $_transaction_id = 'Hospital/Reception/EditPayment/TransactionId/Index/';
    protected $_pay_edit = 'Hospital/Reception/EditPayment/PaymentEdit/Index/';

    protected $_hospital_recestation_counter = 'Hospital/Reception/Counters/RecestationCounter/Index';
    
    protected $_hospital_health_card_counter = 'Hospital/Reception/HealthcardCounter/Index';
      
    protected $_hospital_health_card_patient_search_url = 'Hospital/Reception/HealthcardCounter/PatientSearch';
    
    protected $_hospital_dntl_mrbook = 'Hospital/Reception/Register/DentalMRBook';
    protected $_hospital_dntl_mrbook_json = 'Hospital/Reception/Register/DentalMRBook/JSON';

    protected $_hospital_dental_profile = 'Hospital/Reception/Register/PatientProfile/DentalPatientProfile/Index/';

    protected $_hospital_dental_payments_counter = 'Hospital/Reception/Counters/DentalPaymentsCounter/Index';
    protected $_hospital_dental_payments_counter_file_serach = 'Hospital/Reception/Counters/DentalPaymentsCounter/FileSearch';
    protected $_create_dental_treatment = 'Treatments/CreateDentalTreatments/Index/';


    protected $_hospital_expose_patient_record = 'Hospital/Reception/Expose/Index/';



    function getNavigation(){
        return [
            'navigations' => [
                'Reception|REC' => [
                    [
                        'label' => 'Counter',
                        'perm' => 'all',
                        'module' => 'Hospital Counter',
                        'icon' => 'fas fa-cart-plus',
                        'path' => $this->_hospital_counter_url,
                        'order' => 1,
                        'user_config' => 'is_receptionist',
                        'children' => [
                            [
                                'label' => 'Emergency Counter',
                                'perm' => 'all',
                                'module' => 'Hospital Emergency Counter',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_expose_patient_record,
                                'order' => 100
                            ],
                            // [
                            //     'label' => 'OPD Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital OPD Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_opd_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'New Inpatient Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital New Inpatient Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_new_inpt_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Inpatient Payments',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Inpatient Payments Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_inpt_payments_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Recestation Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Recestation Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_recestation_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Dental Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Dental Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_dental_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Dental Payments',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Dental Payments Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_dental_payments_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Lab Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Lab Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_lab_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Xray Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Xray Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_xray_counter,
                            //     'order' => 100
                            // ],
                            // [
                            //     'label' => 'Ultrasound Counter',
                            //     'perm' => 'all',
                            //     'module' => 'Hospital Ultrasound Counter',
                            //     'icon' => 'fas fa-file-invoice',
                            //     'user_config' => 'is_receptionist',
                            //     'path' => $this->_hospital_ultrasound_counter,
                            //     'order' => 100
                            // ]
                        ]
                    ],
                    [
                        'label' => 'Edit Inpatient',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'Edit Inpatient',
                        'perm_group' => 'Inpatient Management',
                        'priority' => 'perm',
                        'module' => 'Inpatient Mr No',
                        'icon' => 'fas fa-clipboard-list',
                        'path' => $this->_inpatient_mr_no,
                        'order' => 2
                    ],
                    [
                        'label' => 'Register',
                        'perm' => 'all',
                        'module' => 'Register',
                        'icon' => 'fas fa-book',
                        'path' => $this->_hospital_opd_register,
                        'order' => 2,
                        'user_config' => 'is_receptionist',
                        'children' => [
                            [
                                'label' => 'OPD Register',
                                'perm' => 'all',
                                'module' => 'Hospital OPD Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_opd_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'INPT MR Book',
                                'perm' => 'all',
                                'module' => 'Hospital INPT Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_inpt_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Emergency Register',
                                'perm' => 'all',
                                'module' => 'Hospital EMER Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_emer_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Xray Register',
                                'perm' => 'all',
                                'module' => 'Hospital Xray Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_rad_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Tests Register',
                                'perm' => 'all',
                                'module' => 'Hospital Tests Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_path_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Dental Register',
                                'perm' => 'all',
                                'module' => 'Hospital Dental Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_dental_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Ultrasound Register',
                                'perm' => 'all',
                                'module' => 'Hospital Ultrasound Register',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_ultra_register,
                                'order' => 100
                            ],
                            [
                                'label' => 'Dental MR Book',
                                'perm' => 'all',
                                'module' => 'Hospital Dental MRBook',
                                'icon' => 'fas fa-file-invoice',
                                'user_config' => 'is_receptionist',
                                'path' => $this->_hospital_dntl_mrbook,
                                'order' => 100
                            ],
                        ]
                    ],
                    [
                        'label' => 'Edit Patient',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'Patient Receipt Id',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_patient_receipt_id,
                        'order' => 104
                    ],
                    [
                        'label' => 'Edit Transaction',
                        'perm' => 'all',
                        'user_config' => 'is_receptionist',
                        'module' => 'Patient Transaction Id',
                        'icon' => 'fas fas fa-history',
                        'path' => $this->_transaction_id,
                        'order' => 105
                    ],
                    
                ], 
            ],
            'top_nav' => [
                
            ],
            'urlsToRemember' => [
                'HOSPITAL_INPATIENT_REGISTER' => $this->_hospital_inpt_register,
                'HOSPITAL_REC_PATIENTS_JSON_URL' => $this->_hospital_patient_search_url,
                'HOSPITAL_REC_OPD_REGISTER_JSON_URL' => $this->_hospital_opd_register_json,
                'HOSPITAL_REC_INPT_REGISTER_JSON_URL' => $this->_hospital_inpt_register_json,
                'HOSPITAL_REC_EMER_REGISTER_JSON_URL' => $this->_hospital_emer_register_json,
                'HOSPITAL_REC_RAD_REGISTER_JSON_URL' => $this->_hospital_rad_register_json,
                'HOSPITAL_REC_PATH_REGISTER_JSON_URL' => $this->_hospital_path_register_json,
                'HOSPITAL_REC_PATH_INP_PATIENT_PROFILE' =>$this->_hospital_inp_profile,
                'HOSPITAL_REC_PATH_INPT_FILE_SEARCH' => $this->_hospital_inpt_payments_counter_file_serach,
                'HOSPITAL_EXPENSE_VOUCHER_JSON' => $this->_hospital_expense_voucher_payment_json,
                'HOSPITAL_IN_PATIENTS_JSON_URL' => $this->_hospital_inpt_search_url,
                'HOSPITAL_REC_DENTAL_REGISTER_JSON_URL' => $this->_hospital_dental_register_json,
                'HOSPITAL_REC_ULTRA_REGISTER_JSON_URL' => $this->_hospital_ultra_register_json,
                'EDIT_PATIENT' => $this->_patient_edit,
                'PATIENT_RECPT_NO' => $this->_patient_receipt_id,
                'EDIT_INPATIENT' => $this->_inpatient_edit,
                'INPATIENT_MR_NO' => $this->_inpatient_mr_no,
                'INP_PAY' => $this->_inpatient_payment,
                'PAY_EDIT' => $this->_pay_edit,
                'TRANSACTION_NO' => $this->_transaction_id,
                'HOSPITAL_HEALTH_CARD_PATIENTS_JSON_URL' => $this->_hospital_health_card_patient_search_url,
                'HOSPITAL_DNTL_REGISTER' => $this->_hospital_dntl_mrbook,
                'HOSPITAL_REC_DNTL_REGISTER_JSON_URL' => $this->_hospital_dntl_mrbook_json,
                'HOSPITAL_REC_PATH_DENTAL_PATIENT_PROFILE' =>$this->_hospital_dental_profile,
                'HOSPITAL_REC_PATH_DENTAL_FILE_SEARCH' => $this->_hospital_dental_payments_counter_file_serach,
                'NEW_DENTAL_TREATMENT' => $this->_create_dental_treatment,
                //'HOSPITAL_DENTAL_APPOINTMENT_FILE_SEARCH' => $this->_hospital_dental_appointment_file_serach,
                'HOSPITAL_EXPOSE_PATIENT' => 'Hospital/Reception/Expose/index/',
            ]
            ];
    }
    
    
}
