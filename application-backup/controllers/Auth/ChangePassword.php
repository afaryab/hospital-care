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

    function isLoggedIn(){

        $this->aauth->is_loggedin();
        $this->_user = $this->aauth->get_user();

        $notEmptyUser = (!empty($this->_user));
        $notNullUser = ($this->_user != NULL);
        $notFalseUser = ($this->_user != FALSE);

        if($notEmptyUser && $notNullUser && $notFalseUser){
            return true;

        }else{
            return false;
        }
    }

    public function index(){
        
        $this->_userID = $this->getUser()->id;
        $this->_user = $this->aauth->get_user($this->_userID);
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
                    if($this->_user->pass == $this->aauth->hash_password($cpass,$this->_userID)){
                        $this->aauth->update_user($this->_userID,FALSE,$_POST['password'],FALSE);
                        $this->setMessage('success', 'Your password is updated!');
                        $this->activityLog('User#'.$this->_userID.' password is updated.');
                    }else{
                        $this->setMessage('error', 'Current Password is not correct!');
                    }
                }elseif($this->hasAccessTo('ROLE_ADMIN') || $this->hasAccessTo('ROLE_SUPER_ADMIN')){
                    $this->aauth->update_user($this->_userID,FALSE,$_POST['password'],FALSE);
                    $this->setMessage('success', 'User password is updated!');
                    $this->activityLog('User#'.$this->_userID.' password is updated by Admin.');
                }
                
            }else{
                if(!$validPassword){
                    $this->setMessage('error','Password Error.');
                }
            }

            $this->redirectAutnorized();
                    
        }
        $this->_pageData['user'] = $this->_user;
        $this->_pageData['groups'] = $groups;
        $this->_pageData['title'] = 'Change Password';
        $this->_pageData['module'] = 'user';
        $this->_pageData['bc'] = [
            [
                'name' => 'Profile',
                'url' => $this->_pageData['PROFILE_USER'],
                'icon' => 'fas fa-user-circle'
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

