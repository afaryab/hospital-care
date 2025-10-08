<?php

class ViewDashboard extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE','Dashboard');
        
    }

    public function index(){
        if($this->isLoggedIn()) {

            $this->_pageData['title'] = 'Dashboard';
            $this->_pageData['module'] = 'dashboard';
            $html = $this->load->makeViewWithOutTemplate('dashboard', $this->_pageData,true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    public function dashboardJS(){
        if($this->isLoggedIn()) {
            $array = [];
            $thisYearTransactions = $this->db->query('SELECT SUM(`orignal_amount`) as amount, `income_or_expence` FROM `reception_counters_closings_transactions` WHERE YEAR(`created_on`) = '.date('Y').' GROUP BY `income_or_expence`')->result_array();
            $thisMonthTransactions = $this->db->query('SELECT SUM(`orignal_amount`) as amount, `income_or_expence` FROM `reception_counters_closings_transactions` WHERE MONTH(`created_on`) = '.date('m').' GROUP BY `income_or_expence`')->result_array();
            $thisWeekTransactions = $this->db->query('SELECT SUM(`orignal_amount`) as amount, `income_or_expence` FROM `reception_counters_closings_transactions` WHERE WEEKOFYEAR(`created_on`) = '.date('W').' GROUP BY `income_or_expence`')->result_array();
            $todayTransactions = $this->db->query('SELECT SUM(`orignal_amount`) as amount, `income_or_expence` FROM `reception_counters_closings_transactions` WHERE DATE(`created_on`) = CURDATE() GROUP BY `income_or_expence`')->result_array();
            
            foreach($thisMonthTransactions as $row){
                $array['transactions']['this_month'][$row['income_or_expence']] = $row['amount'];
            }
            foreach($thisYearTransactions as $row){
                $array['transactions']['this_year'][$row['income_or_expence']] = $row['amount'];
            }
            foreach($thisWeekTransactions as $row){
                $array['transactions']['this_week'][$row['income_or_expence']] = $row['amount'];
            }
            foreach($todayTransactions as $row){
                $array['transactions']['today'][$row['income_or_expence']] = $row['amount'];
            }


            $thisYearHeaders = $this->db->query('SELECT SUM(`amount`) as amount, `type` FROM `reception_counters_closings_transaction_elements` WHERE YEAR(`created_on`) = '.date('Y').' GROUP BY `type`')->result_array();
            $thisMonthHeaders = $this->db->query('SELECT SUM(`amount`) as amount, `type` FROM `reception_counters_closings_transaction_elements` WHERE MONTH(`created_on`) = '.date('m').' GROUP BY `type`')->result_array();
            $thisWeekHeaders = $this->db->query('SELECT SUM(`amount`) as amount, `type` FROM `reception_counters_closings_transaction_elements` WHERE WEEKOFYEAR(`created_on`) = '.date('W').' GROUP BY `type`')->result_array();
            $todayHeaders = $this->db->query('SELECT SUM(`amount`) as amount, `type` FROM `reception_counters_closings_transaction_elements` WHERE DATE(`created_on`) = CURDATE() GROUP BY `type`')->result_array();

            foreach($thisMonthHeaders as $row){
                $array['headers']['this_month'][$row['type']] = $row['amount'];
            }
            foreach($thisYearHeaders as $row){
                $array['headers']['this_year'][$row['type']] = $row['amount'];
            }
            foreach($thisWeekHeaders as $row){
                $array['headers']['this_week'][$row['type']] = $row['amount'];
            }
            foreach($todayHeaders as $row){
                $array['headers']['today'][$row['type']] = $row['amount'];
            }

            $thisYearPatients = $this->db->query('SELECT COUNT(`id`) as counting FROM `patients` WHERE YEAR(`created_on`) = '.date('Y'))->result_array();
            $thisMonthPatients = $this->db->query('SELECT COUNT(`id`) as counting FROM `patients` WHERE MONTH(`created_on`) = '.date('m'))->result_array();
            $thisWeekPatients = $this->db->query('SELECT COUNT(`id`) as counting FROM `patients` WHERE WEEKOFYEAR(`created_on`) = '.date('W'))->result_array();
            $todayPatients = $this->db->query('SELECT COUNT(`id`) as counting FROM `patients` WHERE DATE(`created_on`) = CURDATE()')->result_array();

            $array['patients']['today'] = $todayPatients[0]['counting'];
            $array['patients']['this_week'] = $thisWeekPatients[0]['counting'];
            $array['patients']['this_month'] = $thisMonthPatients[0]['counting'];
            $array['patients']['this_year'] = $thisYearPatients[0]['counting'];

            echo json_encode($array); die;

        }else{
            $this->redirectUnauthorized();
        }
    }



    public function getUsersStatus(){
        if($this->aauth->is_loggedin() && $this->aauth->is_admin()) {
            $users = $this->aauth->list_users_with_variables();
            echo json_encode($users);
        }
    }
}

