<?php

class Appointments{
    
    protected $_list_appointments = 'Appointments/ListAppointments/Index';
    protected $_hospital_dental_appointment_counter = 'Appointments/CreateDentalAppointments/Index';
    //protected $_hospital_dental_appointment_file_serach = 'Appointments/CreateDentalAppointments/FileSearch';
    protected $_dental_profile = 'Hospital/Reception/Register/PatientProfile/DentalPatientProfile/Index/';
    protected $_hospital_dental_payments_counter_file_serach = 'Hospital/Reception/Counters/DentalPaymentsCounter/FileSearch';
    protected $_hospital_dental_mrbook = 'Hospital/Reception/Register/DentalMRBook';
    protected $_hospital_dental_mrbook_json = 'Hospital/Reception/Register/DentalMRBook/JSON';
    function getNavigation(){
        return [
            'navigations' => [
                'Appointments|APP' => [
                    [
                        'label' => 'List Appointments',
                        'perm' => 'all',
                        'user_config' => 'is_doctor',
                        'module' => 'List Appointments',
                        'icon' => 'fab fa-servicestack',
                        'path' => $this->_list_appointments,
                        'order' => 0
                    ],
                    [
                        'label' => 'Create Appointment',
                        'perm' => 'all',
                        'user_config' => 'is_doctor',
                        'module' => 'Hospital Dental Appointment',
                        'icon' => 'fas fa-person-booth',
                        'path' => $this->_hospital_dental_appointment_counter,
                        'order' => 1
                    ],
                    [
                        'label' => 'Dental MR Book',
                        'perm' => 'all',
                        'module' => 'Hospital Dental MRBook',
                        'icon' => 'fas fa-file-invoice',
                        'user_config' => 'is_doctor',
                        'path' => $this->_hospital_dental_mrbook,
                        'order' => 100
                    ],
                
                    
                ] 
            ],
            'urlsToRemember' => [
 
                'HOSPITAL_DENTAL_APPOINTMENT_FILE_SEARCH' => $this->_hospital_dental_payments_counter_file_serach,
                'DENTAL_PATIENT_PROFILE' =>$this->_dental_profile,
                'LIST_APPOINTMENTS' =>$this->_list_appointments,
                'HOSPITAL_DNTL_REGISTER' => $this->_hospital_dental_mrbook,
                'HOSPITAL_REC_DNTL_REGISTER_JSON_URL' => $this->_hospital_dental_mrbook_json,
            ]
            ];
    }
    
    
}
