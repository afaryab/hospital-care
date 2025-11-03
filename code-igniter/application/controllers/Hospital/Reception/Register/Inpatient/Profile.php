<?php

class Profile extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($id = 0){

        if($this->isLoggedIn()) {
                if($id != 0 ){
                
                    $this->load->model('commonModel','inpatient_file');
                    $this->inpatient_file->setTableName('inpatient_file');
                    $this->_pageData['files'] = $this->inpatient_file->findOneBy(['id' => $id]);
                    $inp = $this->_pageData['files'];
                    //print_array($inp,1);
                    $inpID = $inp['patient_id'];
                    
                    $this->load->model('commonModel','inpatient_transactions');
                    $this->inpatient_transactions->setTableName('inpatient_transactions');
                    $this->_pageData['trans'] = $this->inpatient_transactions->findBy(['file_id' => $id]);
                    $this->load->model('commonModel','inpatient_expense_transactions');
                    $this->inpatient_expense_transactions->setTableName('inpatient_expense_transactions');
                    $this->_pageData['exp_trans'] = $this->inpatient_expense_transactions->findBy(['file_id' => $id]);
                    $this->load->model('commonModel','inpatient_treatments');
                    $this->inpatient_treatments->setTableName('inpatient_treatments');
                    $this->_pageData['treats'] = $this->inpatient_treatments->findBy(['file_id' => $id]);
                    $this->load->model('commonModel','expense_vouchers');
                    $this->expense_vouchers->setTableName('expense_vouchers');
                    $this->_pageData['exp_vouchers'] = $this->expense_vouchers->findBy(['inpatient_file_id' => $id]);
                    $this->load->model('commonModel','expense_categories');
                    $this->expense_categories->setTableName('expenses_categories');
                    $this->_pageData['expense_categories'] = $this->expense_categories->getAll();
                    $this->load->model('commonModel','recestation_transactions');
                    $this->recestation_transactions->setTableName('recestation_transactions');
                    $this->_pageData['recestrans'] = $this->recestation_transactions->findBy(['mr_no' => $id]);
                    $this->load->model('commonModel','recestation_treatments');
                    $this->recestation_treatments->setTableName('recestation_treatments');
                    $this->_pageData['recestreats'] = $this->recestation_treatments->findBy(['mr_no' => $id]);
                    $this->load->model('commonModel','recestation_services');
                    $this->recestation_services->setTableName('recestation_services');
                    $this->_pageData['recestation_services'] =  $this->recestation_services->getAll();

                    $this->load->model('commonModel','patients');
                    $this->patients->setTableName('patients');
                    $this->_pageData['patient'] = $this->patients->findOneBy(['id' => $inpID]);

                    
                    //$file =$this->_pageData['files'];
                    //print_array($file,1);

//                     date_default_timezone_set("Asia/Karachi");
// echo "The time in " . date_default_timezone_get() . " is " . date("H:i:s");
                    
                    if($this->havePost())
                    {
                        if($inp['panel_name'] != NULL){
                            $this->load->model('commonModel','panel_payments');
                            $this->panel_payments->setTableName('panel_payments');

                            $panelarray = [

                                'company_name' => $inp['panel_name'],
                                'mr_no' => $inp['id'],
                                'amount' => $_POST['remaining_ammount'],
                                'status' => 'PENDING',
                            ];

                            $this->panel_payments->addNew($panelarray);
                        }

                        if($inp['room_id'] != NULL){

                            $roomid = $inp['room_id'];
                            $this->load->model('commonModel','inpd_rooms');
                            $this->inpd_rooms->setTableName('inpd_rooms');

                            $roomArray = [

                               'is_allotted' => 0,
                            ];

                            $this->inpd_rooms->updateRecord($roomid,$roomArray);
                        }
                        date_default_timezone_set("Asia/Karachi");
                        $fileupdate = [
                            'status' => 'CLOSED',
                            'closed_on' => date("Y-m-d h:i:s"),
                        ];


                        $this->inpatient_file->updateRecord($id,$fileupdate);
                        
                        redirect($this->_pageData['HOSPITAL_INPATIENT_REGISTER']);
                    }
                

                    $this->_pageData['module'] = 'Hospital';
                    $html = $this->load->makeViewWithOutTemplate('inp_profile', $this->_pageData, true);
                    $this->makeView($html);
                }else{
                    $this->redirectUnauthorized();
                }
        }else{
            $this->redirectUnauthorized();
        }

    }


    
}

