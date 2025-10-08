<?php

class PrintInpatientFile extends MY_Controller
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

                    redirect($this->_pageData['PRINT_INPATIENT_FILE'].$_GET['receipt_id']);
                }
            }else{
                redirect('Reception/InpatientCoverFile/Index/'. $receiptId);
            }

            $this->_pageData['title'] = 'File #'.$receiptId;
            $this->_pageData['module'] = 'Print Inpatient File';
            $this->_pageData['recieptId'] = $receiptId;
            $html = $this->load->makeViewWithOutTemplate('print_inpatient_file', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

