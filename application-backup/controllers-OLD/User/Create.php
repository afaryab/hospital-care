<?php

class Create extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        $groups = $this->aauth->get_groups();
        $perms = $this->aauth->get_perms();
        if ($this->havePost()) {
            $config = $this->config->item('aauth');
            $validEmail = valid_email($_POST['user_email']);
            $validUserName = (
                $_POST['user_name'] !='' && 
                ctype_alnum(
                    str_replace(
                        $config['valid_chars'], '', $_POST['user_name']
                        )
                    )
                ) ? TRUE : FALSE;
                $validPassword = (
                    strlen($_POST['user_password']) > $config['min'] OR strlen($_POST['user_password']) < $config['max'] 
                    &&  
                    ($_POST['user_password'] == $_POST['ret_user_password'])
                ) ? TRUE : FALSE;
                // print_array($_POST);
                 
                $permis = [
                    'is_super_admin' => array_key_exists('is_super_admin',$_POST) ? $_POST['is_super_admin'] : 0,
                    'is_receptionist' => array_key_exists('is_receptionist',$_POST) ? $_POST['is_receptionist'] : 0,
                    'is_doctor' => array_key_exists('is_doctor',$_POST) ? $_POST['is_doctor'] : 0,
                    'is_nurse' => array_key_exists('is_nurse',$_POST) ? $_POST['is_nurse'] : 0,
                    'is_xray_tech' => array_key_exists('is_xray_tech',$_POST) ? $_POST['is_xray_tech'] : 0,
                    'is_opd_doctor' => array_key_exists('is_opd_doctor',$_POST) ? $_POST['is_opd_doctor'] : 0,
                    'is_inpatient_doctor' => array_key_exists('is_inpatient_doctor',$_POST) ? $_POST['is_inpatient_doctor'] : 0,
                    'is_emergency_doctor' => array_key_exists('is_emergency_doctor',$_POST) ? $_POST['is_emergency_doctor'] : 0,
                    'is_dentist' => array_key_exists('is_dentist',$_POST) ? $_POST['is_dentist'] : 0,
                    'is_ultrasound_doc' => array_key_exists('is_ultrasound_doc',$_POST) ? $_POST['is_ultrasound_doc'] : 0,
                ];
                
                if($validEmail && $validPassword && $validUserName){
                    if($userId = $this->aauth->create_user($_POST['user_email'],$_POST['user_password'],$_POST['user_name'],$_POST['parent_id'])){
                        $this->aauth->update_user_type($userId,$permis);
                        $this->setMessage('success', 'User is created!');
                        $this->activityLog('New User#'.$userId.' is created.');
                        
                        foreach ($groups as $group){
                            
                            $key = 'is_'.str_replace(' ','_',$group->name);
                            if(
                                array_key_exists($key, $_POST) &&
                                $_POST[$key]  == 'on'
                                ){
                                    $this->aauth->add_member($userId,$group->name);
                                    $this->setMessage('success', 'User#'.$userId.' have granted access to '.$group->department.'!');
                                    $this->activityLog('User#'.$userId.' have granted access to '.$group->department.'!');
                            }
                            
                        }

                        foreach ($perms as $p){
                            foreach ($p as $perm) {
                                $key = 'have_' . $perm->name;
                                if (
                                    array_key_exists($key, $_POST) &&
                                    $_POST[$key] == 'on'
                                ) {
                                    $this->aauth->is_allowed($perm->name, '', $userId);
                                }
                            }
                        }
                        
                        
                        redirect($this->_pageData['PROFILE_USER'].$userId);
                    }else{
                        $errors = $this->aauth->errors;
                        foreach ($errors as $key=>$error){
                            $this->setMessage('error',$error);
                        }
                    }
                    
                }else{
                    if(!$validEmail){
                        $this->setMessage('error','Email is invalid.');
                    }
                    if(!$validUserName){
                        $this->setMessage('error','Name is invalid.');
                    }
                    if(!(strlen($_POST['user_password']) < $config['min'] OR strlen($_POST['user_password']) > $config['max'])){
                        $this->setMessage('error','Password length must be inbetween '.$config['min'].'-'.$config['max']);
                    }
                    if($_POST['user_password'] != $_POST['ret_user_password']){
                        $this->setMessage('error','Password Missmatch.');
                    }
                }
            
        }
        $this->_pageData['users'] = $this->aauth->list_users(FALSE,FALSE,FALSE,TRUE);
        $this->_pageData['groups'] = $groups;
        $this->_pageData['permissions'] = $perms;
        $this->_pageData['module'] = 'user';
        $this->_pageData['title'] = 'Create User';
        $html = $this->load->makeViewWithOutTemplate('create',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

