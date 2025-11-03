<?php

class ImportExport extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
       
    }


    public function index(){
        
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'ImportExport';
            $this->_pageData['module'] = 'User';

            $html = $this->load->makeViewWithOutTemplate('import_export', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }

    }
   

//     function exportDatabase()
//    {
//        $this->load->dbutil();
//        $this->load->helper('url');
//        $this->load->helper('file');
//        $this->load->helper('download');
//        $this->load->library('zip');
      

//        $backup=$this->dbutil->backup();
//       write_file('/path/to/lamp.sql',$backup);
//       force_download('lamp.sql',$backup);

//      }

    function importDatabase()
    {
        if($this->havePost()){

            $filename=$_FILES["file"]["tmp_name"];    
            if($_FILES["file"]["size"] > 0)
            {
               $file = fopen($filename, "r");
                $templine = '';
            
                

                $lines = file($file);

                      
                    foreach ($lines as $line)
                    {
                        
                        if (substr($line, 0, 2) == '--' || $line == '')
                        continue;
   
                        $templine .= $line;

                        if (substr(trim($line), -1, 1) == ';')
                        {
                            $this->db->query($templine);
                            $templine = '';
                        }
                    }
                    fclose($file); 
            }
         
        }

    }


}