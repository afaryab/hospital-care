<?php

class EditPatient extends MY_Controller
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

                    redirect($this->_pageData['EDIT_PATIENT'].$_GET['receipt_id']);
                }
            }
            $this->_pageData['title'] = 'Patient Receipt Id';
            $this->_pageData['module'] = 'Patient Receipt Id';

            $html = $this->load->makeViewWithOutTemplate('edit_patient', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}