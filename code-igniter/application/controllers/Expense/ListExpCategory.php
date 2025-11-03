<?php

class ListExpCategory extends MY_Controller
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

        $this->load->model('commonModel', 'exp');
        $this->exp->setTableName('expenses_categories');
        $this->_pageData['expenses'] = $this->exp->getAll();


        $this->_pageData['title'] = 'Expense';
        $this->_pageData['module'] = 'Expense';
        $html = $this->load->makeViewWithOutTemplate('list_exp',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

