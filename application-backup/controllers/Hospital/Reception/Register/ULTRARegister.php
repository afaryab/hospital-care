<?php

class ULTRARegister extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){
        //  && $this->aauth->is_allowed('Hospital OPD Register','Hospital OPD')
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Hospital Ultrasound Register';
            $this->_pageData['module'] = 'Hospital Ultrasound Register';

            $html = $this->load->makeViewWithOutTemplate('ultra_register', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'ultrasound_treatments';

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id','formatter' => function ($d, $row) {
                    return 'ULTRASOUND_TRT_'.str_pad($d, 8, '0', STR_PAD_LEFT).'<br/>'.'<a title="Site Traking Number: '.str_pad($row['patient_id'], 8, '0', STR_PAD_LEFT).'">'.'ULTRASOUND_'.str_pad($d, 8, '0', STR_PAD_LEFT).'</a>';
                    // return '<a title="Site Traking Number: '.str_pad($row['patient_id'], 8, '0', STR_PAD_LEFT).'">'.'OPD_'.str_pad($d, 8, '0', STR_PAD_LEFT).'</a>';
                }),
                array('db' => 'treatment_by', 'table' => $table, 'dt' => 1,'as' => 'treatment_by','formatter' => function ($d, $row) {
                
                    if($d == 0 || $d == NULL){
                        //$return = '<a class="btn btn-sm btn-primary">Assign Doctor</a>';
                        //$html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_NEW_TREATMENTS'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary">Assign Doctor</a>';
                        $html = '<p> --|-- </p>';
                        return $html;
                    }else{

                        $user = $this->getUser($d);

                        if($user) {
                            $user = (array) $user;
                            $return = '<a href="' . site_url($this->_pageData['urlsToRemember']['PROFILE_USER'] . $d) . '">' . $user['name'] . '</a>';
                        }else {
                            $return = '<a title="( User #'.$d.')">Not Found</a>';
                        }
                    }

                    return $return;
                    
                }),
                array('db' => 'pateint_name', 'table' => 'patients','as' => 'pateint_name', 'dt' => 2),
                array('db' => 'patient_contact_mobile', 'table' => 'patients','as' => 'patient_contact_mobile', 'dt' => 3),
               // array('db' => 'service_name', 'table' => $table,'as' => 'service_name', 'dt' => 4),
                array(
                    'db' => 'service_id',
                    'as' => 'service_id',
                    'dt' => 4,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        if($row['service_name'] == NULL){
                          
                            //return '<a class="btn btn-sm btn-primary">Assign Doctor</a>';
                        //   $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_NEW_TREATMENTS'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary">Select Service</a>';
                        $html = '<p> --|-- </p>';
                        return $html;
                        
                        }else{

                          return $row['service_name'];
                        }
                    }
                ),
             
                array('db' => 'status', 'table' => $table,'as' => 'status', 'dt' => 5),
                array('db' => 'will_occure_on', 'table' => $table, 'as' => 'will_occure_on',
                    'dt' => 6,
                    'formatter' => function ($d, $row) {
                        if($d == 0 || $d == NULL){
                            $return = '<a title="Date: '.date('Y-m-d h:i:s a', strtotime($row['will_occure_on'])).'">'.nicetime(date('Y-m-d h:i:s a', strtotime($row['will_occure_on']))).'</a>';
                        }else {
                            $return = '<a title="Date: '.date('Y-m-d h:i:s a', strtotime($d)).'">'.nicetime(date('Y-m-d h:i:s a', strtotime($d))).'</a>';
                        }
                        return $return;
                }),
                array(
                    'db' => 'service_name',
                    'as' => 'service_name',
                    'dt' => 7,
                    'table' => $table
                ),
                array('db' => 'patient_id', 'table' => $table,'as' => 'patient_id', 'dt' => 8),
                array('db' => 'ultrasound_patient_id', 'table' => $table, 'dt' => 9,'as' => 'ultrasound_patient_id'),
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
                SSP::simplewithjoin($_GET, $sql_details, $table, $primaryKey, $columns,'patients','ultrasound_treatments`.`patient_id` = `patients`.`id')
            );
        }
    }
    
}

