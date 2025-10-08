<?php

class Groups extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index($id = 0){

        if($this->havePost()){

            if($_POST['id'] == 0){
                if($this->aauth->is_allowed('Create Group', 'Advance')){

                    $cg['name'] = $_POST['Name'];
                    $cg['department'] = $_POST['department'];
                    $cg['definition'] = $_POST['description'];
                    $cg['url'] = $_POST['url'];

                    if($this->aauth->create_group($cg['name'], $cg['department'], $cg['definition'], $cg['url'])){
                        $this->setMessage('success','Group created successfully');
                    }else{
                        $this->setMessage('error','Group not created');
                    }

                }else{
                    $this->setMessage('error','You don\'t have access to create a group');
                }
            }else{
                if($this->aauth->is_allowed('Edit Group', 'Advance')){
                    $cg['name'] = $_POST['Name'];
                    $cg['department'] = $_POST['department'];
                    $cg['definition'] = $_POST['description'];
                    $cg['url'] = $_POST['url'];

                    if($this->aauth->update_group_custom($_POST['id'],$cg)){
                        $this->setMessage('success','Group updated successfully');
                    }else{
                        die;
                        $this->setMessage('error','Group not updated');
                    }
                }else{
                    $this->setMessage('error','You don\'t have access to edit a group');
                }
            }

            redirect($this->_pageData['urlsToRemember']['LIST_GROUPS']);
        }

        if($id != 0){
            $cg = $this->aauth->get_group($id);
        }else{
            $cg['id'] = 0;
            $cg['name'] = '';
            $cg['department'] = '';
            $cg['definition'] = '';
            $cg['url'] = '';
            $cg = (object) $cg;
        }
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }

        $this->_pageData['groups'] = $this->aauth->list_groups();
        $this->_pageData['title'] = 'Groups';
        $this->_pageData['module'] = 'groups';
        $this->_pageData['cg'] = $cg;
        $html = $this->load->makeViewWithOutTemplate('groups/list',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

