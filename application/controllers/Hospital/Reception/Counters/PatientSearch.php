<?php

class PatientSearch extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }

    public function index(){
        if($this->isLoggedIn()) {

            $parameters = $_GET;

            if($_GET['patient_number']){

                $exploded = explode('/', $_GET['patient_number']);

                if(count($exploded) == 4){

                    if($exploded[2] == 'EMR'){
                        $this->load->model('commonModel', 'emergency_ps_numbers');
                        $this->emergency_ps_numbers->setTableName('emergency_ps_numbers');
                        $emrRecord = $this->emergency_ps_numbers->findOneBy(['ps_number' => $_GET['patient_number']]);

                        if($emrRecord){
                            $parameters['columns'][0]['search']['value'] = $emrRecord['patient_id'];
                        }
                        
                    }else if ($exploded[2] == 'OPD'){
                        $this->load->model('commonModel', 'opd_ps_numbers');
                        $this->opd_ps_numbers->setTableName('opd_ps_numbers');
                        $opdRecord = $this->opd_ps_numbers->findOneBy(['ps_number' => $_GET['patient_number']]);

                        if($opdRecord){
                            $parameters['columns'][0]['search']['value'] = $opdRecord['patient_id'];
                        }
                    }else if ($exploded[2] == 'ID'){
                        $this->load->model('commonModel', 'inpatient_ps_numbers');
                        $this->inpatient_ps_numbers->setTableName('inpatient_ps_numbers');
                        $inpRecord = $this->inpatient_ps_numbers->findOneBy(['ps_number' => $_GET['patient_number']]);

                        if($inpRecord){
                            $parameters['columns'][0]['search']['value'] = $inpRecord['patient_id'];
                        }
                    }



                    
                }elseif(count($exploded) == 3){
                    $this->load->model('commonModel', 'ps_numbers');
                    $this->ps_numbers->setTableName('ps_numbers');
                    $pnRecord = $this->ps_numbers->findOneBy(['ps_number' => $_GET['patient_number']]);

                    if($pnRecord){
                        $parameters['columns'][0]['search']['value'] = $pnRecord['patient_id'];
                    }
                }

            }


            $table = 'patients';
           


            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id'),
                array('db' => 'pateint_name', 'table' => $table, 'dt' => 1,'as' => 'pateint_name'),
                array('db' => 'patient_contact_mobile', 'table' => $table, 'dt' => 2,'as' => 'patient_contact_mobile'),
                array('db' => 'patient_cnic', 'table' => $table, 'dt' => 3,'as' => 'patient_cnic'),
                array(
                    'db' => 'created_on',
                    'as' => 'created_on',
                    'dt' => 4,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        $html='';
                        
                        
                        $html .= '<a onclick="selectPatient(\''.$row['id'].'\',\''.$row['pateint_name'].'\',\''.$row['guardian'].'\',\''.$row['relation'].'\',\''.$row['patient_contact_mobile'].'\',\''.$row['patient_cnic'].'\',\''.$row['patient_address'].'\',\''.$row['age_days'].'\',\''.$row['gender'].'\')" class="btn btn-sm btn-default pull-right" title="Select '. $row['pateint_name'] .'" ><i class="fas fa-bolt" style="color:green;"></i></a>';
                        

                        return $html;
                    }
                ),
                array('db' => 'age_days', 'table' => $table, 'dt' => 5,'as' => 'age_days'),
                array('db' => 'gender', 'table' => $table, 'dt' => 6,'as' => 'gender'),
                array('db' => 'guardian', 'table' => $table, 'dt' => 7,'as' => 'guardian'),
                array('db' => 'patient_address', 'table' => $table, 'dt' => 8,'as' => 'patient_address'),
                array('db' => 'relation', 'table' => $table, 'dt' => 9,'as' => 'relation')
            );

            // SQL server connection information
            $sql_details = array(
                'user' => $this->db->username,
                'pass' => $this->db->password,
                'db' => $this->db->database,
                'host' => $this->db->hostname
            );


            /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             * If you just want to use the basic configuration for DataTables with PHP
             * server-side, there is no need to edit below this line.
             */

            require(__DIR__.'/../../../../third_party/ssp.class.php');

            echo json_encode(
                SSP::simple($parameters, $sql_details, $table, $primaryKey, $columns)
            );
        }else{
            echo json_encode([]);
        }
    }
    
}

