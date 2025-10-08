<?php

class Permissions extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index($id = 0){

        if($this->havePost()){

            if($_POST['id'] == 0){
                if($this->aauth->is_allowed('Create Permission', 'Users Management')){

                    $cg['name'] = $_POST['Name'];
                    $cg['definition'] = $_POST['definition'];
                    $cg['perm_group'] = $_POST['perm_group'];

                    if($this->aauth->create_perm($cg['name'], $cg['perm_group'], $cg['definition'])){
                        $this->setMessage('success','Permission created successfully');
                    }else{
                        $this->setMessage('error','Permission not created');
                    }

                }else{
                    $this->setMessage('error','You don\'t have access to create a Permission');
                }
            }else{
                if($this->aauth->is_allowed('Edit Permission', 'Users Management')){
                    $cg['name'] = $_POST['Name'];
                    $cg['definition'] = $_POST['definition'];
                    $cg['perm_group'] = $_POST['perm_group'];

                    if($this->aauth->update_perm_custom($_POST['id'],$cg)){
                        $this->setMessage('success','Permission updated successfully');
                    }else{
                        die;
                        $this->setMessage('error','Permission not updated');
                    }
                }else{
                    $this->setMessage('error','You don\'t have access to edit a Permission');
                }
            }

            redirect($this->_pageData['urlsToRemember']['LIST_PERMISSION']);
        }

        if($id != 0){
            $cg = $this->aauth->get_perm($id);
        }else{
            $cg['id'] = 0;
            $cg['name'] = '';
            $cg['definition'] = '';
            $cg['perm_group'] = '';
            $cg = (object) $cg;
        }
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }

        $this->_pageData['permissions'] = $this->aauth->list_perms();
//        print_array($this->_pageData['permissions']);
        $this->_pageData['title'] = 'Permissions';
        $this->_pageData['module'] = 'permissions';
        $this->_pageData['cg'] = $cg;
        $html = $this->load->makeViewWithOutTemplate('permissions/list',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

