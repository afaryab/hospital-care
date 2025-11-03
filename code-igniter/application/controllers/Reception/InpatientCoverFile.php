<?php

class InpatientCoverFile extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Reception');
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

                   
                

                    $this->_pageData['module'] = 'Reception';
                    $html = $this->load->makeViewWithOutTemplate('inp_cover_file', $this->_pageData, true);
                    $this->makeView($html);
                }else{
                    $this->redirectUnauthorized();
                }
        }else{
            $this->redirectUnauthorized();
        }

    }


    
}

