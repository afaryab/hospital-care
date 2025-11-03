<?php

class OpenCounter extends CI_Controller
{

    protected $clientArray = [];

    public $_user = null;

    public function __construct()
    {
        parent::__construct();
        define('MODULE','Reception');
        if($this->aauth->is_loggedin()){
            $this->_user = $this->aauth->get_user();
        }else{
            redirect('Auth/Validate');
        }
        $this->initializeClinet(); 
        $this->inlitializeReception();  
    }



    public function index(){

        
        if (!empty($_POST)) {

			$this->load->model('commonModel', 'receptionModel');
            $this->receptionModel->setTableName('reception_counters');
            $this->recptionArray = $this->receptionModel->findOneBy(['id' => $this->_user->reception_id]);

            $this->load->model('commonModel', 'receptionClosingModel');
            $this->receptionClosingModel->setTableName('reception_counters_closings');

            $this->recptionClosingArray = $this->receptionClosingModel->findOneBy(['user_id' => $this->_user->id,'reception_id' => $this->_user->reception_id,'status' => 'OPEN']);

            if(empty($this->recptionClosingArray)){
                
                $this->receptionClosingArray = $this->receptionClosingModel->addNew([
                    'counter_id' => $this->recptionArray['id'],
                    'user_id' => $this->_user->id,
                    'reception_id' => $this->_user->reception_id,
                    'status' => 'OPEN',
                    'opening_amount' => $_POST['opening_cash'] == '' || $_POST['opening_cash'] == null ? 0 : $_POST['opening_cash'],
                    'closing_amount_cash' => $_POST['opening_cash'] == '' || $_POST['opening_cash'] == null ? 0 : $_POST['opening_cash'],
                    'closing_amount' => $_POST['opening_cash'] == '' || $_POST['opening_cash'] == null ? 0 : $_POST['opening_cash'],
                ]);
                redirect('Auth/Validate');

            }else{
                redirect('Auth/Validate');
            }
            
        }else{
            
            $this->recptionArray = $this->receptionModel->findOneBy(['id' => $this->_user->reception_id]);
            
            $this->load->makeViewWithOutTemplate('opencounter', [ 'reception' => $this->recptionArray ]);
            
        }
        
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
            
            if($this->clientArray['current_user_login_id'] != $this->_user->id){
                $updateClientArray['current_user_login_id'] = $this->_user->id;
            }

            if(!empty($updateClientArray)){
                $this->clientModel->updateRecord($this->clientArray['id'],$updateClientArray);
                $this->clientArray = $this->clientModel->findOneBy(['machine_unique_key' => $clientKey]);
            }
        }
        
    }
    function inlitializeReception(){

        if($this->_user->is_receptionist == 1){

            $this->load->model('commonModel', 'receptionModel');
            $this->receptionModel->setTableName('reception_counters');
            $this->recptionArray = $this->receptionModel->findOneBy(['client_id' => $this->clientArray['id']]);

            if(empty($this->recptionArray)){
                $this->receptionModel->addNew([
                    'counter_name' => "Main Reception",
                    'client_id' => $this->clientArray['id'],
                    'is_opd_allowed' => 1,
                    'is_emergency_allowed' => 1,
                    'is_inpatient_allowed' => 1,
                    'is_followup_allowed' => 1,
                    'is_allowed_to_pay_voucher' => 1,
                    'is_allowed_to_pay_from_petty_cash' => 1,
                    'cash_on_counter' => 1,
                    'cheques_on_counter' => 1,
                    'card_slips_on_counter' => 1
                ]);
                $this->recptionArray = $this->receptionModel->findOneBy(['client_id' => $this->clientArray['id']]);
            };

            $this->load->model('commonModel', 'receptionClosingModel');
            $this->receptionClosingModel->setTableName('reception_counters_closings');
            $this->receptionClosingArray = $this->receptionClosingModel->findOneBy([
                'counter_id' => $this->recptionArray['id'],
                'user_id' => $this->_user->id,
                'status' => 'OPEN'
            ]);

            if(!empty($this->receptionClosingArray)){
                redirect('Auth/Validate');
            }
        }else{
            die("User is not receptionist");
        }


    }
}

