<?php

class ExpenseCashBook extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){
        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['title'] = 'Expense Book';
            $this->_pageData['module'] = 'Expense Book';
            $this->_pageData['voucher_payments'] = [];

            $this->load->model('commonModel', 'exp');
            $this->exp->setTableName('expenses_categories');
            $this->_pageData['expcategories'] =  $this->exp->getAll();

            $this->load->model('commonModel', 'vouchers');
            $this->vouchers->setTableName('expense_vouchers');
            //$this->_pageData['users'] = $this->aauth->list_users();

            $seldoc = NULL;
            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today';
            
            $category = array_key_exists('catg',$_GET) ? $_GET['catg'] : 0;
            
            // $doctor = array_key_exists('doc',$_GET) ? $_GET['doc'] : 0;

            // $departemnt = array_key_exists('service',$_GET) ? $_GET['service'] : '';
            
            if($category != 0){
                $this->_pageData['selectedCategory'] = array_filter(
                    $this->_pageData['expcategories'],
                    function ($e) use (&$category) {
                        return $e['id'] == $category;
                    }
                );

                $seldoc = $this->_pageData['selectedCategory'];
                $this->_pageData['selectedCategory'] = reset($this->_pageData['selectedCategory']);
                
            }
           

            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $this->_pageData['voucher_payments'] = $this->vouchers->findBy(['CAST(created_on AS DATE) = ' => $date , 'exp_category_id' => $category]);

            }else{

                if(array_key_exists('date_range',$_GET)){
                    
                    $date_r = explode('-',$_GET['date_range']);
                    $start_date = date('Y-m-d',strtotime($date_r[0]));
                    $end_date = date('Y-m-d',strtotime($date_r[1]));

                }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-2 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                $this->_pageData['voucher_payments'] = $this->vouchers->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] , 'exp_category_id' => $category]);

            }
            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('expensecashbook', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    
}

