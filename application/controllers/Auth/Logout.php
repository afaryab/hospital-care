<?php

class Logout extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(){

        $this->load->helper('cookie');
        
        $clientKey = get_cookie('processtonclient_client_key');

        if($clientKey == null){
            $clientKey = generateRandomString(12);
            set_cookie(
                "processtonclient_client_key",
                $clientKey,
                time() + (10 * 365 * 24 * 60 * 60)
            );
        }

        $this->load->model('commonModel', 'clientModel');
        $this->clientModel->setTableName('clients');
        $clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);

        if(empty($clientArray)){
            $this->clientModel->addNew([
                'machine_name' => $_SERVER["REMOTE_ADDR"],
                'machine_unique_key' => $clientKey
            ]);
            $clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
        }
        $updateClientArray = [];
        $clientArray['current_user_login_id'] = null;
        $this->_user = $this->aauth->get_user();
        if($this->aauth->is_loggedin() && $clientArray['last_user_login_id'] != $this->_user->id){
            $updateClientArray['last_user_login_id'] = $this->_user->id;
        }
        if(!empty($updateClientArray)){
            $this->clientModel->updateRecord($clientArray['id'],$updateClientArray);
        }
        $this->aauth->logout();
        redirect(site_url('/'));
    }
}

