<?php

class Edit extends MY_Controller
{
    protected $_userID = 0;
    protected $_user = null;
    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index($userId){
        
        $this->_userID = $userId;
        $this->_user = $this->aauth->get_user($userId);
        
        $groups = $this->aauth->get_groups();
        // print_array($groups);
        // print_array($_POST,1);
        
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        if ($this->havePost()) {

            $config = $this->config->item('aauth');
            $validEmail = filter_var($_POST['user_email'],FILTER_VALIDATE_EMAIL);
            $validUserName = (
                $_POST['user_name'] !='' &&
                ctype_alnum(
                    str_replace(
                        $config['valid_chars'], '', $_POST['user_name']
                        )
                    )
                ) ? TRUE : FALSE;

                $permis = [
                    'is_receptionist' => array_key_exists('is_receptionist',$_POST) ? $_POST['is_receptionist'] : 0,
                    'is_opd_doctor' => array_key_exists('is_opd_doctor',$_POST) ? $_POST['is_opd_doctor'] : 0,
                    'is_inpatient_doctor' => array_key_exists('is_inpatient_doctor',$_POST) ? $_POST['is_inpatient_doctor'] : 0,
                    'is_dentist' => array_key_exists('is_dentist',$_POST) ? $_POST['is_dentist'] : 0,
                    'is_ultrasound_doc' => array_key_exists('is_ultrasound_doc',$_POST) ? $_POST['is_ultrasound_doc'] : 0,
                ];
                
            if($validEmail && $validUserName){
                
                
                if($this->aauth->update_user($this->_userID,$_POST['user_email'],FALSE,$_POST['user_name'])){
                    $this->aauth->update_user_type($userId,$permis);
                    $this->setMessage('success', 'User#'.$this->_userID.' is updated!');
                    $this->activityLog('User#'.$this->_userID.' is updated.');
                    $this->setMessage('success', 'User#'.$this->_userID.' departments have been updated!');

                    foreach ($groups as $group){
                        
                        $key = 'is_'.str_replace(' ','_',$group->name);

                        if(array_key_exists($key, $_POST) && $_POST[$key] == 'on' ){

                                $this->aauth->create_group($group->name);
                                $this->aauth->add_member($this->_userID,$group->name);


                        }else{
                            $this->aauth->remove_member($this->_userID,$group->name);
                        }

                        
                    }
                    $this->setMessage('success', 'User#'.$this->_userID.' permissions have been updated!');
                    $this->activityLog('User#'.$this->_userID.' permissions are updated by Admin!');
                    
                    redirect($this->_pageData['EDIT_USER'].$userId);
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
            }
                    
        }
        //print_array($this->_user);
        $this->_pageData['user'] = $this->_user;

        $this->_pageData['groups'] = $groups;
        $this->_pageData['title'] = 'Edit User';
        $this->_pageData['module'] = 'user';
        $html = $this->load->makeViewWithOutTemplate('edit',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

