<?php

class ExpenseStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Expense Statement';
            $this->_pageData['module'] = 'Expense Statement';
            $this->_pageData['report_transactions'] = [];

            $this->load->model('commonModel', 'transaction');
            $this->transaction->setTableName('reception_counters_closings_transactions');

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                $this->_pageData['report_transactions'] = $this->transaction->findBy(['CAST(created_on AS DATE) = ' => $date ]);

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

                $this->_pageData['report_transactions'] = $this->transaction->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'] ]);
                $transactions = $this->_pageData['report_transactions'] ;
            }

            $transacIds = [];

            foreach($transactions as $transac){

                if($transac['income_or_expence'] == 'INCOME'){
                
                }else{
                    $expenseTransactions[] = $transac['id'];
                }
                $transacIds[] = $transac['id'];
                $this->_pageData['report_transactions'][$transac['id']] = $transac;
            }

            $this->transaction->setTableName("reception_counters_closings_transaction_elements");
            $rowsRaw = $this->transaction->findBy(['closing_transaction_id' => $transacIds]);

            foreach($rowsRaw as $row){
                $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                //foreach($transactions as $transac){
                    //print_array($transac);
                    if($row['type'] == 'INPT-EXP'){
                        
                        $this->load->model('commonModel', 'inpexpenses');
                        $this->inpexpenses->setTableName('inpatient_expense_transactions');
                       
                        $expenseRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        
                        $expArray = $this->inpexpenses->findOneBy($expenseRecordArray);
                        
                        $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['inpExp'] = $expArray;

                        
                        
                    }else{
                        
                        $this->load->model('commonModel', 'expenses');
                        $this->expenses->setTableName('expenses');
                        $expenseRecordArray = [
                            'id' => $row['department_transaction_id']
                        ];
                        $expArray = $this->expenses->findOneBy($expenseRecordArray);
                        
                        $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']]['exp_array'] = $expArray;

                        
                        
                    }
                    // elseif($transac['income_or_expence'] == 'EXPENSE')
                    // {
                    //     $this->_pageData['report_transactions'][$row['closing_transaction_id']]['rows'][$row['id']] = $row;
                
                    // }
               // }
            }

            
            $this->_pageData['date'] = $date;
            
            $html = $this->load->makeViewWithOutTemplate('expencestatement', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
}

