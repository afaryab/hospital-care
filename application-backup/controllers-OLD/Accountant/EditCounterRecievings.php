<?php

class EditCounterRecievings extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index($id = 0){

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            // $this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');

            $this->_pageData['title'] = 'Edit Counter Recievings';
            $this->_pageData['module'] = 'Edit Counter Recievings';

            // $this->commonModel->setTableName('inpd_services');
            // $this->_pageData['inpatient_services'] =  $this->commonModel->getAll();

            $this->load->model('commonModel', 'reception_counters_closings');
            $this->reception_counters_closings->setTableName('reception_counters_closings');
            $closings_data = $this->reception_counters_closings->findOneBy(['id' => $id]);
            $this->_pageData['closing_cash']=$closings_data['closing_amount'];
            $this->_pageData['cash_recieved_amount']=$closings_data['cash_recieved_amount'];
            $this->_pageData['cash_recieved_by']=$closings_data['cash_recieved_by'];
            
            if($this->havePost()){

                $this->load->model('commonModel', 'reception_counters_closings');
                $this->reception_counters_closings->setTableName('reception_counters_closings');
                $closings_data2 = $this->reception_counters_closings->findOneBy(['id' => $id]);

                $data_update = [ 
                    'is_cash_recieved' => 1,
                    'cash_recieved_amount' => $_POST['cash_recieved_amount'],
                    'cash_recieved_by' => $_POST['cash_recieved_by'],
                    'cash_recieving_difference' => $closings_data['closing_amount']-$_POST['cash_recieved_amount'],
                    // 'cash_recieving_time' => date('Y-m-d H:i:s')
                ];

                $this->reception_counters_closings->updateRecord($closings_data2['id'],$data_update);


                redirect($this->_pageData['COUNTER_RECIEVING']);

            }
            
            
            $html = $this->load->makeViewWithOutTemplate('edit_counter_recieving', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

