<?php

class CreateExpCategory extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Expense');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
        $this->load->model('commonModel', 'expense');
        $this->expense->setTableName('expenses_categories');
        $this->_pageData['expenses'] = $this->expense->getAll();


        
        if ($this->havePost()) {
            
               $exp=[
               'name' => $_POST['name'],
               'type' => array_key_exists('type_inpt',$_POST) ? $_POST['type_inpt'] : NULL,
               'pay_doc' => array_key_exists('pay_doc',$_POST) ? $_POST['pay_doc'] : 0,
               'pay_others' => array_key_exists('pay_others',$_POST) ? $_POST['pay_others'] : 0,
               'pay_users' => array_key_exists('pay_users',$_POST) ? $_POST['pay_users'] : 0,
               'add_comments' => array_key_exists('add_comments',$_POST) ? $_POST['add_comments'] : 0,
               ];
               $this->expense->addNew($exp);
        
            $this->setMessage('success', 'Expense Category created successfully!');
            $this->activityLog('Expense Category created successfully');
            redirect($this->_pageData['EXPENSE_LIST']);
  
        }
        
       
        $this->_pageData['module'] = 'Expense';
        $this->_pageData['title'] = 'Create Expense Category';
        $html = $this->load->makeViewWithOutTemplate('create_exp',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

