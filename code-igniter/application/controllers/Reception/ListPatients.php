<?php

class ListPatients extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'patients';
            $this->_pageData['module'] = 'patients';

            $html = $this->load->makeViewWithOutTemplate('list_patients', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'reception_counters_closings_transactions';
           


            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id'),
                
                array('db' => 'pateint_name', 'table' => 'patients','as' => 'pateint_name', 'dt' => 1),
               
                
                array('db' => 'patient_contact_mobile', 'table' => 'patients', 'as' => 'patient_contact_mobile','dt'=>2),
                
                array('db' => 'patient_cnic', 'table' => 'patients', 'as' => 'patient_cnic','dt' => 3),
                array(
                    'db' => 'created_on',
                    'as' => 'created_on',
                    'dt' => 4,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                      $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['PRINT_RECEIPT'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-default pull-right" title="Print Bill" ><i class="fas fa-file-invoice-dollar" style="color:green;"></i></a>';
                

                        return $html;
                    }
                ),
           
                array('db' => 'pateint_name', 'table' => 'patients','as' => 'pateint_name', 'dt' => 5),
                array('db' => 'patient_contact_mobile', 'table' => 'patients', 'dt' => 6, 'as' => 'patient_contact_mobile'),
               
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
                SSP::simplewithjoin($_GET, $sql_details, $table, $primaryKey, $columns,'patients','reception_counters_closings_transactions`.`patient_id` = `patients`.`id')
            );
        }else{
            echo json_encode([]);
        }

    }
    
}