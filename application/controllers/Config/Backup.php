<?php

class LicenceKey extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        define('MODULE','Config');
        
    }
    
    public function index(){
        $error = [];
        if(!empty($_POST)){
            
            $resp = doCurl(serverURLGenerator('REGISTERATION',$_POST['licenceKey']),1);
            
            $body = json_decode($resp->body);
            
            var_dump($body);
            
            if($body->status == 200){
                
                $array = $body->data;
                
                file_put_contents(__DIR__ . '/../../../licence.bin', serialize($array));
                
                redirect(site_url());
                
            }else{
                
                $error[] = $resp->body;
                
            }
            
        }
        $this->load->makeViewWithOutTemplate('licence',['error' => $error]);
    }
}

