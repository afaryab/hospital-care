<?php

class ListNotifications extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Notifications';

            $html = $this->load->makeViewWithOutTemplate('notifications/list', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function JSON(){
        if($this->isLoggedIn()) {

            $table = ACTIVITY_LOGS_TABLE;

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            if($this->hasAccessTo('ROLE_ADMIN') || $this->hasAccessTo('ROLE_SUPER_ADMIN') || $this->hasAccessTo('ROLE_DEVELOPER')) {
                $columns = array(
                    array('db' => 'id', 'dt' => 0),
                    array('db' => 'message', 'dt' => 1),
                    array(
                        'db' => 'user_id',
                        'dt' => 2,
                        'formatter' => function ($d, $row) {
                            if ($d == 0 || $d == NULL) {
                                $return = '<a>Not Preferred</a>';
                            } else {
                                $user = $this->getUser($d);

                                if ($user) {
                                    $user = (array)$user;

                                    $return = '<a href="' . site_url($this->_pageData['urlsToRemember']['PROFILE_USER'] . $d) . '">' . $user['name'] . '</a>';
                                } else {
                                    $return = '<a title="( User #' . $d . ')">Not Found</a>';
                                }
                            }

                            return $return;
                        }
                    ),
                    array(
                        'db' => 'created_on',
                        'dt' => 3,
                        'formatter' => function ($id, $row) {

                            return $id;
                        }
                    )
                );
            }else{
                $columns = array(
                    array('db' => 'id', 'dt' => 0),
                    array('db' => 'message', 'dt' => 1),
                    array(
                        'db' => 'created_on',
                        'dt' => 2,
                        'formatter' => function ($id, $row) {

                            return $id;
                        }
                    )
                );
            }

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

