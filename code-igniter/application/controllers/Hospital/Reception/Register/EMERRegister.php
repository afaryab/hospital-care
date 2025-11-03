<?php

class EMERRegister extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){
        // && $this->aauth->is_allowed('Hospital EMER Register','Hospital EMER')
        if($this->isLoggedIn() ) {

            $this->_pageData['title'] = 'Hospital EMER Register';
            $this->_pageData['module'] = 'Hospital EMER Register';

            $html = $this->load->makeViewWithOutTemplate('emer_register', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'emergency_treatments';

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id', 'table' => $table, 'dt' => 0,'as' => 'id','formatter' => function ($d, $row) {
                    return 'EMR_TRT_'.str_pad($d, 8, '0', STR_PAD_LEFT).'<br/>'.'<a title="Site Traking Number: '.str_pad($row['patient_id'], 8, '0', STR_PAD_LEFT).'">'.'OPD_'.str_pad($d, 8, '0', STR_PAD_LEFT).'</a>';
                    // return '<a title="Site Traking Number: '.str_pad($row['patient_id'], 8, '0', STR_PAD_LEFT).'">'.'OPD_'.str_pad($d, 8, '0', STR_PAD_LEFT).'</a>';
                }),
                array('db' => 'pateint_name', 'table' => 'patients','as' => 'pateint_name', 'dt' => 1),
                array('db' => 'patient_contact_mobile', 'table' => 'patients','as' => 'patient_contact_mobile', 'dt' => 2),
               // array('db' => 'service_name', 'table' => $table,'as' => 'service_name', 'dt' => 4),
                array(
                    'db' => 'service_id',
                    'as' => 'service_id',
                    'dt' => 3,
                    'table' => $table,
                    'formatter' => function ($id, $row) {
                        if($row['service_name'] == NULL){
                          
                            //return '<a class="btn btn-sm btn-primary">Assign Doctor</a>';
                          $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_NEW_TREATMENTS'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary">Select Service</a>';
                           return $html;
                        
                        }else{

                          return $row['service_name'];
                        }
                    }
                ),
                array(
                    'db' => 'service_name',
                    'as' => 'service_name',
                    'dt' => 4,
                    'table' => $table
                ),
                // array(
                //     'db' => 'service_name',
                //     'as' => 'service_name',
                //     'dt' => 4,
                //     'table' => $table,
                //     'formatter' => function ($id, $row) {
                //         $html = '';
                //         if($row['service_name'] == NULL){
                          
                //             //return '<a class="btn btn-sm btn-primary">Assign Doctor</a>';
                //           $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_NEW_TREATMENTS'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary">Select Service</a>';
                //            return $html;
                        
                //         }else{

                //             if($this->aauth->is_allowed('Edit Patient','OPD Reception')) {
                //                 $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['EDIT_OPD_PATIENT_URL'] . $row['id'])) . '" target="_blank" title="Edit '. $row['pateint_name'] .'" class="btn btn-sm btn-default"><i class="fas fa-edit"></i></a>';
                //             }
                       
                //             $html .= '<a href="' . (site_url($this->_pageData['urlsToRemember']['PRINT_OPD_TOKEN_URL'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-default" title="Print Token '. $row['pateint_name'] .'" ><i class="fas fa-receipt"></i></a>';
                //             $html .= '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_PATIENT_PROFILE'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-default" title="Open '. $row['pateint_name'] .' Profile" ><i class="fas fa-id-badge"></i></a>';
                //             $html .= '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_TREATMENTS_LIST'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary" title="Open '. $row['pateint_name'] .' Treatments" ><i class="fas fa-stethoscope"></i></a>';
                //             if($this->aauth->is_allowed('Collect Payment','OPD Reception')) {
                //                 $html .= '<a href="' . (site_url($this->_pageData['urlsToRemember']['OPD_PATIENT_CHARGES_URL'] . $row['id'])) . '" target="_blank" class="btn btn-sm btn-primary" title="Open '. $row['pateint_name'] .' Billing Details" ><i class="fas fa-file-invoice-dollar"></i></a>';
                //             }
                //             return $html;
                //         }
                //     }
                // ),
                array('db' => 'patient_id', 'table' => $table,'as' => 'patient_id', 'dt' => 5),
                array('db' => 'emergency_patient_id', 'table' => $table, 'dt' => 6,'as' => 'emergency_patient_id'),
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
                SSP::simplewithjoin($_GET, $sql_details, $table, $primaryKey, $columns,'patients','emergency_treatments`.`patient_id` = `patients`.`id')
            );
        }
    }
    
}

