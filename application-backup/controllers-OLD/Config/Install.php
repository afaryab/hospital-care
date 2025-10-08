<?php

class Install extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        define('MODULE','Config');
        $this->_ci_ob_level  = ob_get_level();
        
        //         // Default
                $this->_ci_view_paths = array(
                    APPPATH . 'controllers/'.MODULE => TRUE
                );
        
    }
    
    public function index(){
        
        $this->load->makeViewWithOutTemplate('install');
    }
}

