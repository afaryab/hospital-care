<?php

class EditInpatientInfo extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($mrNo = 0){
        if($this->isLoggedIn()) {

            if($mrNo != 0){

                // $this->load->model('commonModel', 'reception_counters_closings_transactions');
                // $this->reception_counters_closings_transactions->setTableName('reception_counters_closings_transactions');
                // $this->_pageData['receptionTransaction'] = $this->reception_counters_closings_transactions->findOneBy(['id' => $receiptId]);
                // $rT = $this->_pageData['receptionTransaction'];
                // $this->load->model('commonModel', 'reception_counters_closings_transaction_elements');
                // $this->reception_counters_closings_transaction_elements->setTableName('reception_counters_closings_transaction_elements');
                // $this->_pageData['receptionTransactionElements'] = $this->reception_counters_closings_transaction_elements->findBy(['closing_transaction_id' => $this->_pageData['receptionTransaction']['id']]);
                // $rte = $this->_pageData['receptionTransactionElements'];

                $this->load->model('commonModel','inpatient_file');
                $this->inpatient_file->setTableName('inpatient_file');
                $this->_pageData['files'] = $this->inpatient_file->findOneBy(['id' => $mrNo]);
                $inp = $this->_pageData['files'];
                //print_array($inp,1);
                $inpID = $inp['patient_id'];
                $fileroomID = $inp['room_id'];
                $fileroomName = $inp['room_name'];

                $this->load->model('commonModel', 'patients');
                $this->patients->setTableName('patients');
                $this->_pageData['patients'] = $this->patients->findOneBy(['id' => $inpID]);

                $this->load->model('commonModel','inpd_services');
                $this->inpd_services->setTableName('inpd_services');
                $this->_pageData['inpatient_services'] =  $this->inpd_services->getAll();

                $this->commonModel->setTableName('panel_companies');
                $this->_pageData['panel_companies'] =  $this->commonModel->getAll();

                $this->load->model('commonModel','inpatient_transactions');
                $this->inpatient_transactions->setTableName('inpatient_transactions');
                $this->_pageData['trans'] = $this->inpatient_transactions->findBy(['file_id' => $mrNo]);

                $this->commonModel->setTableName('inpd_rooms');
                $this->_pageData['inpd_rooms'] =  $this->commonModel->getAll();
                

                if ($this->havePost()) {
                    $edit = [
                        'pateint_name' => $_POST['name'],
                        'gender' => $_POST['gender'],
                        'age_days' => array_key_exists('age',$_POST) ? $_POST['age'] : 0,
                        'guardian' => array_key_exists('guardian',$_POST) ? $_POST['guardian'] : NULL,
                        'patient_contact_mobile' => array_key_exists('contact',$_POST) ? $_POST['contact'] : NULL,
                        'patient_cnic' => array_key_exists('cnic',$_POST) ? $_POST['cnic'] : NULL, 
                        'patient_address' => array_key_exists('address',$_POST) ? $_POST['address'] : NULL,  
                        ];
                        $this->patients->updateRecord($inpID,$edit);

                        $this->load->model('commonModel','inpd_services');
                        $this->inpd_services->setTableName('inpd_services');
                        $this->_pageData['inpatient_services'] =  $this->inpd_services->findOneBy(['id' => $_POST['service_id']]);
                        $inp = $this->_pageData['inpatient_services'];
                        //print_array($inp,1);
                        $serviceName = $inp['name'];

                        if ($_POST['panel'] == ""){
                            $_POST['panel'] = NULL; 
                        }

                        $rid = 0;
                        $rname = NULL;

                        $roomid = 0;
                        $rm = array_key_exists('room_id',$_POST) ? $_POST['room_id'] : NULL; 
                        if($rm != NULL)
                        {
                            $roomid = $_POST['room_id'];

                            $this->load->model('commonModel','inprooms');
                            $this->inprooms->setTableName('inpd_rooms');
                            $Room =  $this->inprooms->findOneBy(['id' => $roomid]);
                            $rid = $roomid;
                            $rname = $Room['name'];

                            $roomArray = [

                                'is_allotted' => 1,
                            ];
                            $this->inprooms->updateRecord($roomid,$roomArray);

                            $previous = [
                                'is_allotted' => 0,
                            ];
                            $this->inprooms->updateRecord($fileroomID,$previous);


                        }else{
                            $rid = $fileroomID;
                            $rname = $fileroomName;
                        }


                        $editfile = [
                            'service_id' => $_POST['service_id'],
                            // 'service_id' => array_key_exists('service_id',$_POST) ? $_POST['service_id'] : $inp['service_id'],
                             'service_name' => $serviceName,
                             'status' => $_POST['status'],
                             'panel_name' => $_POST['panel'],
                             'file_charges' => $_POST['package'],
                             'room_id' => $rid,
                             'room_name' => $rname,
                             'is_visiting' => $_POST['is_visiting'],
                             //'file_charges_paid' => $_POST['charges'],
                            //'service_name' => array_key_exists('service_name',$_POST) ? $_POST['service_name'] : NULL,  
                            ];
                        $this->inpatient_file->updateRecord($mrNo,$editfile);
        
                        $this->setMessage('success', 'Patient Info edited successfully!');
                        $this->activityLog('Patient Info edited successfully');
                        redirect($this->_pageData['INPATIENT_MR_NO']);
                }

            }
            $this->_pageData['title'] = 'Edit Patient Info';
            $this->_pageData['module'] = 'Edit Patient Info';

            $html = $this->load->makeViewWithOutTemplate('edit_inpatient_info', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}