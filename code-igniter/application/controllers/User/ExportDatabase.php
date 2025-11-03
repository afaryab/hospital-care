<?php

class ExportDatabase extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }


    public function index(){

        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'ExportDatabase';
            $this->_pageData['module'] = 'User';
        
        }
        else{
            $this->redirectUnauthorized();
        }
           
    }

    
    
    function exportDatabase()
       {
           $this->load->dbutil();
           $this->load->helper('url');
           $this->load->helper('file');
           $this->load->helper('download');
           $this->load->library('zip');

           $prefs = array(
            'tables'        => array('patients'),   // Array of tables to backup.
          'ignore'        => array(),                    
           'format'        => 'sql',                       
          'filename'      => 'mybackup.sql',             
           'add_drop'      => TRUE,                        
           'add_insert'    => TRUE,                        
           'newline'       => "\n"                         
        );
          
    
           $backup=$this->dbutil->backup($prefs);
          write_file('/path/to/lamp.sql',$backup);
          force_download('lamp.sql',$backup);
    
         }



 


}