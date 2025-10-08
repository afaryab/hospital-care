<?php

class PanelPayments extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Panel Payments';
            $this->_pageData['module'] = 'Panel Payments';

            $this->load->model('commonModel', 'panel_payments');
            $this->panel_payments->setTableName('panel_payments');
            $this->_pageData['payments'] = $this->panel_payments->getAll(); 
            
            $this->load->model('commonModel','inpatient_file');
            $this->inpatient_file->setTableName('inpatient_file');
            $this->_pageData['files'] = $this->inpatient_file->getAll();

            $this->load->model('commonModel','patients');
            $this->patients->setTableName('patients');
            $this->_pageData['patients'] = $this->patients->getAll();
            
            $html = $this->load->makeViewWithOutTemplate('panel_payments', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    public function JSON(){
        if($this->isLoggedIn()) {

            $table = 'panel_payments';

            // Table's primary key
            $primaryKey = 'id';

            // Array of database columns which should be read and sent back to DataTables.
            // The `db` parameter represents the column name in the database, while the `dt`
            // parameter represents the DataTables column identifier. In this case simple
            // indexes
            $columns = array(
                array('db' => 'id' ,'table' => $table,'as' => 'id', 'dt' => 0),
                array('db' => 'mr_no' ,'table' => $table,'as' => 'mr_no', 'dt' => 1),
                array('db' => 'mr_no' ,'table' => $table,'as' => 'mr_no','dt' => 2,'formatter' => function ($id, $row) {
                    
                    $this->load->model('commonModel','inpatient_file');
                    $this->inpatient_file->setTableName('inpatient_file');  
                    $file = $this->inpatient_file->findOneBy(['id' => $row['mr_no']]);

                    $this->load->model('commonModel','patients');
                    $this->patients->setTableName('patients');
                    
                    $pat = $this->patients->findOneBy(['id' => $file['patient_id']]);

                        //$html = $pat['pateint_name'];
                    $html = $pat['pateint_name'];
                        return $html;
                   
                }),
                array('db' => 'company_name','table' => $table ,'as' => 'company_name', 'dt' => 3),
                array('db' => 'amount_recieved', 'table' => $table ,'as' => 'amount_recieved', 'dt' => 4),
                array('db' => 'payment_reference', 'table' => $table,'as' => 'payment_reference', 'dt' => 5),
                array('db' => 'status', 'table' => $table,'as' => 'status', 'dt' => 6),
                array(
                    'db' => 'status',
                    'as' => 'status',
                    'dt' => 7,
                    'table' => $table,
                    'as' => 'status',
                    'formatter' => function ($id, $row) {
                       
                        $html = '<a href="' . (site_url($this->_pageData['urlsToRemember']['INP_PANEL_PAY'] . $row['mr_no'])) . '" target="_blank" class="btn btn-sm btn-primary">PAYMENT</a>';
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
               // SSP::simplewithjoin($_GET, $sql_details, $table, $primaryKey, $columns,'inpatient_file','panel_payments`.`mr_no` = `inpatient_file`.`id')
               SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
            );
        }
    }


}

