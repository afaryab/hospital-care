<?php

class EditFile extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index($mrNo = 0){
        if($this->isLoggedIn()) {

            if($mrNo == 0){

                if(array_key_exists('mr_no',$_GET)){

                    redirect($this->_pageData['EDIT_INPATIENT'].$_GET['mr_no']);
                }
            }
            $this->_pageData['title'] = 'Inpatient Mr No';
            $this->_pageData['module'] = 'Inpatient Mr No';

            $html = $this->load->makeViewWithOutTemplate('edit_file', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}