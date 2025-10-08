<?php

class Login extends CI_Controller
{

    protected $clientArray = [];

    public function __construct()
    {
        parent::__construct();
        define('MODULE','Auth');
        $this->initializeClinet();   
    }



    public function index(){

        if ($this->aauth->is_loggedin()) {
            $this->redirectAutnorized();
        }
        
        if (!empty($_POST)) {

			
            if ($this->aauth->login($_REQUEST['username'], $_REQUEST['password'])) {
			
                $this->redirectAutnorized();
            }
        }
        
        $this->load->makeViewWithOutTemplate('login');
    }
    function redirectAutnorized(){

        $user = $this->aauth->get_user();
		
        if($user->change_password == 1){
            redirect('Auth/ChangePassword');
        }
        if($user->is_receptionist == 1){
            echo "check For Reception";
        }

        $groups = $this->aauth->get_groups();

        foreach ($groups as $group){
            if ($this->aauth->is_member($group->name)) {
                if($group->url != '') {
                    redirect($group->url);
                }
            }
            redirect('User/Profile/Index/');
        }
		die;
    }
    function initializeClinet(){
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
        $clientKey = get_cookie('processtonclient_client_key');
        
        if($clientKey != null){
            $this->load->model('commonModel', 'clientModel');
            $this->clientModel->setTableName('clients');
            $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);

            if(empty($this->clientArray)){
                $this->clientModel->addNew([
                    'machine_name' => $_SERVER["REMOTE_ADDR"],
                    'machine_unique_key' => $clientKey
                ]);
                $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
            }
            $updateClientArray = [];
            $updateClientArray['current_user_login_id'] = null;
            if($this->aauth->is_loggedin() && $this->clientArray['current_user_login_id'] != $this->aauth->get_user()->id){
                $updateClientArray['current_user_login_id'] = $this->aauth->get_user()->id;
            }

            if(!empty($updateClientArray)){
                $this->clientModel->updateRecord($this->clientArray['id'],$updateClientArray);
                $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
            }
        }
        
    }


}

