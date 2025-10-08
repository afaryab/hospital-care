<?php

class PrintRecieptDuplicate extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
    }
    
    public function index($receiptId = 0){
        if($this->isLoggedIn()) {

            if($receiptId == 0){

                if(array_key_exists('receipt_id',$_GET)){

                    redirect($this->_pageData['PRINT_RECEIPT_DUP'].$_GET['receipt_id']);
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
                

                //$this->load->model('commonModel', 'opd_services');
                //$this->commonModel->setTableName('opd_services');
                //$this->_pageData['opd_services'] =  $this->commonModel->getAll();
                //print_array($this->_pageData['opd_services']);
                // $this->load->model('commonModel', 'inpd_services');
                // $this->commonModel->setTableName('inpd_services');
                // //$this->_pageData['inpatient_services'] =  $this->commonModel->getAll();

                // $this->load->model('commonModel', 'emergency_services');
                // $this->commonModel->setTableName('emergency_services');
                //$this->_pageData['emergency_services'] =  $this->commonModel->getAll();
                
                $this->load->model('commonModel', 'patients');
                $this->patients->setTableName('patients');
                $this->_pageData['patients'] = $this->patients->findOneBy(['id' => $this->_pageData['receptionTransaction']['patient_id']]);

                $this->load->model('commonModel', 'inpatient_file');
                $this->inpatient_file->setTableName('inpatient_file');
                $this->_pageData['inpatient_file'] = $this->inpatient_file->findOneBy(['patient_id' => $this->_pageData['receptionTransaction']['patient_id']]);
                
                $this->load->model('commonModel', 'dental_patient_file');
                $this->dental_patient_file->setTableName('dental_patient_file');
                $this->_pageData['dental_patient_file'] = $this->dental_patient_file->findOneBy(['patient_id' => $this->_pageData['receptionTransaction']['patient_id']]);
                
                foreach($rte as $row){
                    //print_array($row);
                    if($row['type'] == 'OPD'){
                        
                        
                        $this->load->model('commonModel', 'opd_services');
                        $this->opd_services->setTableName('opd_services');
                        $this->_pageData['opd_services'] =  $this->opd_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        
                        $this->load->model('commonModel', 'opdtransactions');
                        $this->opdtransactions->setTableName('opd_transactions');
                        $this->_pageData['opdtransactions'] = $this->opdtransactions->findOneBy(['patient_id' => $this->_pageData['patients']['id']]);

                        $this->_pageData['user'] = $this->aauth->get_user($this->_pageData['opdtransactions']['doctor_id']);
                        
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
                        // $service_name = $this->_pageData['emergency_services'];
                    }elseif($row['type'] == 'VOUCHER-PAY'){

                        $vid = $row['department_transaction_id'];
                        
                        $this->load->model('commonModel', 'expen');
                        $this->expen->setTableName('expenses');
                        $payments = $this->expen->findOneBy(['id' => $vid]);
                        
                        //$payments = (array) $payments;
                        $this->_pageData['payments'] = $payments;
                        //$payments = (array) $payments;
                        //print_array($payments);
                        
                            //$eid = $payments['voucher_id'];
                            //print_array($eid);
                            $this->load->model('commonModel', 'expenses');
                            $this->expenses->setTableName('expense_vouchers');
                            $voucher = $this->expenses->findOneBy(['id' => $this->_pageData['payments']['voucher_id']]);
                            if($voucher['payed_to_employee'] == 1)
                            {
                                $id = $voucher['employee_id'];
                                $users = $this->getUser($id);
                                $users = (array) $users;
                                $this->_pageData['user'] = $users;

                            // $this->_pageData['user'] = $users;
                            }
                            
                            // $this->expenses->setTableName('expenses');
                            // $payments = $this->expenses->findBy(['voucher_id' => $vid]);
                            $this->_pageData['voucher'] = $voucher;
                        
                        
                    }elseif($row['type'] == 'EXP'){
                        
                        $etid = $row['department_transaction_id'];
                        
                        $this->load->model('commonModel', 'expen');
                        $this->expen->setTableName('expenses');
                        $payments = $this->expen->findOneBy(['id' => $etid]);
                        
                        //$payments = (array) $payments;
                        $this->_pageData['payments'] = $payments;
                        // $payments = (array) $payments;
                        // print_array($payments,1);
                        
                            //$eid = $payments['voucher_id'];
                            //print_array($eid);
                        
                        
                    }elseif($row['type'] == 'INPT-EXP'){
                        
                        $etid = $row['department_transaction_id'];
                        
                        $this->load->model('commonModel', 'inptexpen');
                        $this->inptexpen->setTableName('inpatient_expense_transactions');
                        $inpexp = $this->inptexpen->findOneBy(['id' => $etid]);
                        
                        //$payments = (array) $payments;
                        $this->_pageData['inpexp'] = $inpexp;
                        // $payments = (array) $payments;
                        // print_array($payments,1);
                        
                            //$eid = $payments['voucher_id'];
                            //print_array($eid);
                        
                        
                    }elseif($row['type'] == 'DENTAL'){
                        
                        
                        $this->load->model('commonModel', 'dental_services');
                        $this->dental_services->setTableName('dental_services');
                        $this->_pageData['dental_services'] =  $this->dental_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        
                        $this->load->model('commonModel', 'dentaltransactions');
                        $this->dentaltransactions->setTableName('dental_transactions');
                        $this->_pageData['dentaltransactions'] = $this->dentaltransactions->findOneBy(['patient_id' => $this->_pageData['patients']['id']]);

                        $this->_pageData['user'] = $this->aauth->get_user($this->_pageData['dentaltransactions']['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }elseif($row['type'] == 'ULTRA'){
                        
                        
                        $this->load->model('commonModel', 'ultrasound_services');
                        $this->ultrasound_services->setTableName('ultrasound_services');
                        $this->_pageData['ultrasound_services'] =  $this->ultrasound_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        
                        $this->load->model('commonModel', 'ultrasoundtransactions');
                        $this->ultrasoundtransactions->setTableName('ultrasound_transactions');
                        $this->_pageData['ultrasoundtransactions'] = $this->ultrasoundtransactions->findOneBy(['patient_id' => $this->_pageData['patients']['id']]);

                        $this->_pageData['user'] = $this->aauth->get_user($this->_pageData['ultrasoundtransactions']['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }
                    elseif($row['type'] == 'RECES'){
                        
                        
                        $this->load->model('commonModel', 'recestation_services');
                        $this->recestation_services->setTableName('recestation_services');
                        $this->_pageData['recestation_services'] =  $this->recestation_services->findOneBy(['id' => $row['service_id']]);
                        $this->_pageData['serial'] = $row;
                        // print_array($row);
                        $this->load->model('commonModel', 'recestationtransactions');
                        $this->recestationtransactions->setTableName('recestation_transactions');
                        $this->_pageData['recestationtransactions'] = $this->recestationtransactions->findOneBy(['reception_transaction_id' => $row['closing_transaction_id']]);

                        $this->_pageData['user'] = $this->aauth->get_user($row['doctor_id']);
                        
                        // $service_name = $this->_pageData['opd_services'];
                        // print_array($service_name);
                    }
                }



            }else{

                $this->_pageData['receptionTransaction'] = [];
                $this->_pageData['receptionTransactionElements'] = [];
            }
            

            $this->_pageData['title'] = 'Recipt #'.$receiptId;
            $this->_pageData['module'] = 'Receipt Print';
            $this->_pageData['recieptId'] = $receiptId;
            $html = $this->load->makeViewWithOutTemplate('print_reciept_dup', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

