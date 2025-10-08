<?php

class ReceptionStatements extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Counter Statements';
            $this->_pageData['module'] = 'Counter Statement';
            $html = $this->load->makeViewWithOutTemplate('counter_statements', $this->_pageData, true);
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
                array(
                    'db' => 'user_id',
                    'as' => 'user_id',
                    'dt' => 1,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        $user = $this->getUser($id);

                        $user = (array) $user;
                        $html = '';
                        
                       
                            $html .= $user['name'];
                            
                            return $html;
                        
                    }
                ),
            
                array('db' => 'opening_amount', 'dt' => 2),
                array('db' => 'closing_amount_cash', 'dt' => 3),
                array('db' => 'closing_amount_atm', 'dt' => 4),
                array('db' => 'closing_amount_card', 'dt' => 5),
                array('db' => 'status', 'dt' => 6),
                array('db' => 'closing_amount', 'dt' => 7),
                array('db' => 'created_on', 'dt' => 8),
                array('db' => 'user_id', 'dt' => 9),
                array(
                    'db' => 'status',
                    'as' => 'status',
                    'dt' => 10,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        $html = '';
                        
                       
                            $html .= '<a href="' . (site_url($this->_pageData['urlsToRemember']['REC_TRANS'] . $row['id'])) . '" class="btn btn-sm btn-default" ><i class="fas fa-id-badge"></i></a>';
                            
                            return $html;
                        
                    }
                ),
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

