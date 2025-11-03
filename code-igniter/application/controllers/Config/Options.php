<?php

class Options extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        define('MODULE','Config');
        
    }
    
    public function index(){

        if(!file_exists(__DIR__ . '/../../../licence.bin')){
            redirect('Config/LicenceKey');
        }else {

            $options = [];
            $licenceInfo = unserialize(file_get_contents(__DIR__ . '/../../../licence.bin'));
            $this->_pageData['title'] = 'Licence Information';
            $this->_pageData['module'] = 'Options';
            $this->_pageData['licence'] = $licenceInfo;
            $html = $this->load->makeViewWithOutTemplate('options',$this->_pageData, TRUE);

            $this->makeView($html);
        }

    }
}

