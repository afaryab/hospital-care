<?php

class TransactionId extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($receiptId = 0){
        if($this->isLoggedIn()) {

            if($receiptId == 0){

                if(array_key_exists('receipt_id',$_GET)){

                    redirect($this->_pageData['TRANSACTION_NO'].$_GET['receipt_id']);
                }
            }
            if($receiptId != 0){

                $this->load->model('commonModel', 'reception_counters_closings_transactions');
                $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                $this->_pageData['receptionTransaction'] = $this->reception_counters_closings_transactions->findOneBy(['id' => $receiptId]);
                $rT = $this->_pageData['receptionTransaction'];
                $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                $this->_pageData['receptionTransactionElements'] = $this->reception_counters_closings_transaction_elements->findBy(['closing_transaction_id' => $this->_pageData['receptionTransaction']['id']]);
                $rte = $this->_pageData['receptionTransactionElements'];

                foreach($rte as $row){
                    //print_array($row);
                    if($row['type'] == 'OPD'){
                        
                        
                        $this->load->model('commonModel', 'opd_services');
                        $this->opd_services->setTableName('opd_services');
                        $this->_pageData['opd_services'] =  $this->opd_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        


                        $this->_pageData['user'] = $this->aauth->get_user($row['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }elseif($row['type'] == 'INPT'){

                        $this->load->model('commonModel', 'inpd_services');
                        $this->inpd_services->setTableName('inpd_services');
                        $this->_pageData['inpd_services'] =  $this->inpd_services->findOneBy(['id' => $row['service_id']]);
                        // $service_name = $this->_pageData['inpd_services'];
                    }elseif($row['type'] == 'EMER'){

                        $this->load->model('commonModel', 'emergency_services');
                        $this->emergency_services->setTableName('emergency_services');
                        $this->_pageData['emergency_services'] =  $this->emergency_services->findOneBy(['id' => $row['service_id']]);
                        //  $service_name = $this->_pageData['emergency_services'];
                        // print_array($service_name);
                    }elseif($row['type'] == 'DENTAL'){
                        
                        
                        $this->load->model('commonModel', 'dental_services');
                        $this->dental_services->setTableName('dental_services');
                        $this->_pageData['dental_services'] =  $this->dental_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        
                       
                        $this->_pageData['user'] = $this->aauth->get_user($row['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }elseif($row['type'] == 'ULTRA'){
                        
                        
                        $this->load->model('commonModel', 'ultrasound_services');
                        $this->ultrasound_services->setTableName('ultrasound_services');
                        $this->_pageData['ultrasound_services'] =  $this->ultrasound_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        
                        
                        $this->_pageData['user'] = $this->aauth->get_user($row['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }
                    elseif($row['type'] == 'RECES'){

                        $this->load->model('commonModel', 'recestation_services');
                        $this->recestation_services->setTableName('recestation_services');
                        $this->_pageData['recestation_services'] =  $this->recestation_services->findOneBy(['id' => $row['service_id']]);
                        //  $service_name = $this->_pageData['emergency_services'];
                        $this->_pageData['user'] = $this->aauth->get_user($row['doctor_id']);
                        // print_array($service_name);
                    }
                }
                
            
           

            }
            $this->_pageData['title'] = 'Patient Transaction Id';
            $this->_pageData['module'] = 'Patient Transaction Id';
            $this->_pageData['recieptId'] = $receiptId;
            $html = $this->load->makeViewWithOutTemplate('transaction_id', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}