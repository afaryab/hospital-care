<?php

class BackupNRestore extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Processton');
    }
    
    public function index(){

        if($this->isLoggedIn() && $this->aauth->is_allowed('Processton Backup & Restore','Processton Configuration')) {
            
            $this->_pageData['title'] = 'New Patient';
            $this->_pageData['module'] = 'New Patient';
            $this->_pageData['doctors'] = $this->aauth->list_users('Doctors');

            $html = $this->load->makeViewWithOutTemplate('backupandrestore', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function Backup(){

        $groups = $this->aauth->get_groups();
        $permissions = $this->aauth->list_perms();
        $users = $this->aauth->list_users(FALSE,FALSE,FALSE,TRUE);

        

        $ouputArray = [];
        $patientsTable = 'patients';
        $secondaryPatientTables = [
            'opd_patients' => [
                'treatments' => 'treatments',
                'transactions' => 'transactions',
            ]
        ];
        $treatmentsTable = 'treatments';
        $transactionsTable = 'transactions';

        

        if(count($secondaryPatientTables) <= 0){
            $this->load->model('commonModel', 'patients');
            $this->patients->setTableName('patients');
            $patients = $this->patients->getAll();
        }else{
            $patientIds = [];
            foreach($secondaryPatientTables as $secondaryPatientTable=>$tables){
                
                $this->load->model('commonModel', 'other_patients');
                $this->other_patients->setTableName($secondaryPatientTable);
                $other_patients[$secondaryPatientTable]['profiles'] = $this->other_patients->getAll();

                $this->load->model('commonModel', 'other_treatments');
                $this->other_patients->setTableName($secondaryPatientTable);
                $other_patients[$secondaryPatientTable]['profiles'] = $this->other_patients->getAll();

            }
            
            
            

            $this->load->model('commonModel', 'patients');
            $this->patients->setTableName('patients');
            $primary_patients = $this->patients->findBy(['id' => $patientIds]);

            

        }


        
        print_array([
            'groups' => $groups,
            'permissions' => $permissions,
            'users' => $users
        ],1);


    }
    
}

