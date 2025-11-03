<?php

class MyStatements extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'My Statements';
            $this->_pageData['module'] = 'My Statements';
            $html = $this->load->makeViewWithOutTemplate('my_statements', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'reception_counters_closings';

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'dt' => 0),
                array('db' => 'opening_amount', 'dt' => 1),
                array('db' => 'closing_amount_cash', 'dt' => 2),
                array('db' => 'closing_amount_atm', 'dt' => 3),
                array('db' => 'closing_amount_card', 'dt' => 4),
                array('db' => 'closing_amount_creditcard', 'dt' => 5),
                array('db' => 'expense_payed', 'dt' => 6),
                array('db' => 'status', 'dt' => 7),
                array('db' => 'closing_amount', 'dt' => 8),
                array('db' => 'user_id', 'dt' => 9, 'searchkey' => $this->aauth->get_user()->id),
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
        }
    }
}

