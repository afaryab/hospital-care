<?php

class GroupPermissions extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'User');
    }
    
    public function index(){
//        if($this->userIsAuthorisedFor()){
//
//        }

        $this->_pageData['title'] = 'Group Permissions';
        $this->_pageData['module'] = 'GroupPermissions';

        $this->load->model('commonModel');
        $this->commonModel->setTableName('aauth_groups');
        $this->_pageData['groups'] = $this->commonModel->getAll();

        $this->commonModel->setTableName('aauth_perms');
        $permissionsRaw = $this->commonModel->getAll();

        $this->_pageData['permissions'] = $this->parsePermissionsByGroup($permissionsRaw);

        if($this->havePost()){
            foreach ($this->_pageData['groups'] as $gr){
                foreach ($this->_pageData['permissions'] as $perG){
                    foreach ($perG as $per) {
                        $key = 'is_' . $gr['id'] .'-'. $per['id'];
                        $permissionArray = ['perm_id' => $per['id'],'group_id' => $gr['id']];
                        $this->commonModel->setTableName('aauth_perm_to_group');
                        $result = $this->commonModel->findBy($permissionArray);
                        if (array_key_exists($key, $_POST) && $_POST[$key] == 'on') {
                            if(empty($result)){
                                $this->commonModel->addNew($permissionArray);
                            }
                        }else{
                            $this->db->where($permissionArray)->delete('aauth_perm_to_group');
                        }
                    }
                }
            }

        }

        $html = $this->load->makeViewWithOutTemplate('group_permissions',$this->_pageData,true);
        
        $this->makeView($html);
    }


    public function parsePermissionsByGroup($array){
        $return = [];
        foreach ($array as $arr){
            $return[$arr['perm_group']][] = $arr;
        }
        return $return;
    }
}

