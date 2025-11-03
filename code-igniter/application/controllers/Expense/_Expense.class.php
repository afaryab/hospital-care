<?php

class Expense{
    
    protected $_list_exp = 'Expense/ListExpCategory';
    
    protected $_create_exp = 'Expense/CreateExpCategory';

    protected $_edit_exp = 'Expense/EditExpCategory/Index/';


    
    // protected $_edit_url = 'Services/EditServices';
    
     
    
    function getNavigation(){
        return [
            'navigations' => [
                'Users Management | <i class="fas fa-users"></i>' => [
                    [
                        'label' => 'Expense Categories',
                        'group' => 'ROLE_SUPER_ADMIN|ROLE_ADMIN|ROLE_DEVELOPER',
                        'perm' => 'List Expenses',
                        'perm_group' => 'Expense Management',
                        'priority' => 'perm',
                        'module' => 'Expense',
                        'icon' => 'fas fa-clipboard-list',
                        'path' => $this->_list_exp,
                        'order' => 0
                    ]
                    
                ]
            ],
            'urlsToRemember' => [
                'EXPENSE_LIST' => $this->_list_exp,
                'CREATE_EXPENSE' =>  $this->_create_exp,
                'EDIT_EXPENSE' =>  $this->_edit_exp,
               
            ]
        ];
    }
    
    
}