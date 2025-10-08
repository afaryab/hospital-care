<?php

class ListHealthCardPatients extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'healthcard patients';
            $this->_pageData['module'] = 'healthcard patients';

            $html = $this->load->makeViewWithOutTemplate('list_healthcard_patients', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'health_card_patients';
           


            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id'),
                
                array('db' => 'pateint_name', 'table' => $table,'as' => 'pateint_name', 'dt' => 1),
               
                array('db' => 'patient_cnic', 'table' => $table, 'as' => 'patient_cnic','dt' => 2),
                array('db' => 'patient_contact_mobile', 'table' => $table, 'as' => 'patient_contact_mobile','dt'=>3),
                array('db' => 'antenatal_status', 'table' => $table, 'dt' => 6, 'as' => 'antenatal_status','dt'=>4),
                array('db' => 'last_visit', 'table' => $table, 'dt' => 6, 'as' => 'last_visit','dt'=>5),
                array('db' => 'created_on', 'table' => $table, 'dt' => 6, 'as' => 'created_on','dt'=>6)
               
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

            require(__DIR__.'/../../third_party/ssp.class.php');

            echo json_encode(
                SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
            );
        }else{
            echo json_encode([]);
        }

    }
    
}