<?php

class changePassword extends MY_Controller
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
        
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        if ($this->havePost()) {
            
            $config = $this->config->item('aauth');
            $validPassword = (
                strlen($_POST['password']) > $config['min'] OR strlen($_POST['password']) < $config['max']
                &&
                ($_POST['password'] == $_POST['conf_password'])
                ) ? TRUE : FALSE;
            
            
                
            if($validPassword){
                
                
                if($this->_userID == $this->getUser()->id){
                    $cpass = $_POST['c_password'];
                    if($this->_user->password == $this->aauth->hash_password($cpass,$this->_userID)){
                        $this->aauth->update_user($this->_userID,FALSE,$_POST['password'],FALSE);
                        $this->setMessage('success', 'Your password is updated!');
                        $this->activityLog('User#'.$this->_userID.' password is updated.');
                    }else{
                        $this->setMessage('error', 'Current Password is not correct!');
                    }
                }elseif($this->aauth->is_allowed('Change Other User Passwords', 'Users Management')){

                    $resp=$this->aauth->update_user($this->_userID,FALSE,$_POST['password'],FALSE);

                    $this->setMessage('success', 'User password is updated!');
                    $this->activityLog('User#'.$this->_userID.' password is updated by Admin.');
                }
                
            }else{
                if(!$validPassword){
                    $this->setMessage('error','Password Error.');
                }
            }

            redirect(site_url($this->_pageData['urlsToRemember']['CHANGE_USER_PASS'].'/'.$userId));
                    
        }
        $this->_pageData['user'] = $this->_user;
        $this->_pageData['groups'] = $groups;
        $this->_pageData['title'] = 'Change Password';
        $this->_pageData['module'] = 'user';
        $this->_pageData['bc'] = [
            [
                'name' => 'USERS',
                'url' => $this->_pageData['USERS_LIST'],
                'icon' => 'fa fa-users'
            ],
            [
                'name' => 'Change Password',
                'url' => '',
                'icon' => 'fa fa-key'
            ]
        ];
        $html = $this->load->makeViewWithOutTemplate('changePassword',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

