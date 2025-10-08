<?php
class OtherReports extends MY_Controller
{
    public function __construct(){
        parent::__construct();
        define('MODULE', 'Accountant');
    }

    public function index(){
        if($this->isLoggedIn() && $this->aauth->is_allowed('Income Reports','Accounts')) {

            $this->load->model('commonModel');

            $this->commonModel->setTableName('opd_services');
            $servicesRaw = $this->commonModel->getAll();
            $services = [];
            foreach ($servicesRaw as $s){
                $services[$s['id']] = $s;
            }
            $this->_pageData['title'] = 'Accounts';
            $this->_pageData['module'] = 'IncomeReports';
            //$this->_pageData['doctors'] = $this->aauth->list_users('Doctors');
            $this->_pageData['doctors'] = $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->_pageData['services'] = $services;

            $html = '';

            if($this->havePost()) {

                if($_POST['report_type'] == 1) {
                    $this->load->model('Reporting/transactions_model', 'patient_transactions');
                    $this->load->model('Reporting/patients_model', 'patients');
                    $patient_transactions = $this->patient_transactions->getClosedTransactionsByUserID($_POST['user_id'],1);

                    $patientIDs = [];

                    foreach($patient_transactions as $trans){
                        $patientIDs[] = $trans['patient_id'];
                    }
                    if(!empty($patient_transactions)){
                        $patients = $this->patients->getPatientByIds($patientIDs);
                        $finalArray = [];

                        foreach($patients as $patient){
                            foreach($patient_transactions as $trans){
                                if($trans['patient_id'] == $patient['id']){
                                    $finalArray[] = [
                                        'transaction' => $trans,
                                        'patient' => $patient
                                    ];
                                }
                            }
                        }

                    }else{
                        $finalArray = [];
                    }
                    $this->_pageData['reporteeUser'] = $this->getUser($_POST['user_id']);
                    $this->_pageData['transactions'] = $finalArray;
                    $html = $this->load->makeViewWithoutTemplate('report_1',$this->_pageData,true);

                }elseif($_POST['report_type'] == 2){
                    $closed_by_reception = 0;
                    $closed_by_accounts = 0;

                    if(array_key_exists('closed-by-accounts',$_POST) && $_POST['closed-by-accounts'] == 'on'){
                         $closed_by_accounts = 1;
                    }
                    if(array_key_exists('closed-by-reception',$_POST) && $_POST['closed-by-reception'] == 'on'){
                         $closed_by_reception = 1;
                    }

                    $date_r = explode('-',$_POST['date_range']);
                    $dateRange[0] = date('Y-m-d',strtotime($date_r[0]));
                    $dateRange[1] = date('Y-m-d',strtotime($date_r[1]));

                    $this->load->model('Reporting/transactions_model', 'patient_transactions');
                    $this->load->model('Reporting/patients_model', 'patients');
                    $patient_transactions = $this->patient_transactions->getUserTransactionsWithFilters($_POST['user_id'],$closed_by_reception,$closed_by_accounts,array('start'=>$dateRange[0],'end'=>$dateRange[1]));


                    $patientIDs = [];
                    if(!empty($patient_transactions)) {
                        foreach ($patient_transactions as $trans) {
                            $patientIDs[] = $trans['patient_id'];
                        }
                        $patients = $this->patients->getPatientByIds($patientIDs);
                        $finalArray = [];

                        foreach ($patients as $patient) {
                            foreach ($patient_transactions as $trans) {
                                if ($trans['patient_id'] == $patient['id']) {
                                    $finalArray[] = [
                                        'transaction' => $trans,
                                        'patient' => $patient
                                    ];
                                }
                            }
                        }
                    }else{
                        $finalArray = [];
                    }
                    $this->_pageData['transactions'] = $finalArray;
                    $this->_pageData['reporteeUser'] = $this->getUser($_POST['user_id']);
                    $this->_pageData['dateRange'] = $_POST['date_range'];
                    $html = $this->load->makeViewWithOutTemplate('report_2',$this->_pageData,true);
                }elseif($_POST['report_type'] == 3){

                    $date_r = explode('-',$_POST['date_range']);
                    $dateRange[0] = date('Y-m-d',strtotime($date_r[0]));
                    $dateRange[1] = date('Y-m-d',strtotime($date_r[1]));
                    $this->_pageData['doctor_id'] = $_POST['doctor_id'];
                    $this->_pageData['date_range'] = $_POST['date_range'];
                    $this->_pageData['doc_to_be_share'] = $this->getUser($this->_pageData['doctor_id']);
                    foreach($this->_pageData['doctors'] as $doc){
                        if($doc->id == $this->_pageData['doctor_id']){
                            $array['doc_to_be_share'] = $doc;
                        }
                    }


                    $this->load->model('Reporting/transactions_model', 'patient_transactions');
                    $this->load->model('Reporting/patients_model', 'patients');
                    $patient_transactions = $this->patient_transactions->getTransactionByDoc($_POST['doctor_id'],array('start'=>$dateRange[0],'end'=>$dateRange[1]));
                    $this->_pageData['DoctorToShare'] = $this->getUser($this->_pageData['doctor_id']);
                    if(empty($this->_pageData['DoctorToShare'])){
                        $this->setMessage('error', 'The User you have selected is not a doctor.');
                        redirect('Reports/Accounts/Index');
                    }
                    $array['card'] = [];
                    $array['cash'] = [];
                    $array['check'] = [];
                    $patientIDs = [];
                    if(!empty($patient_transactions)) {
                        foreach ($patient_transactions as $trans) {
                            $patientIDs[] = $trans['patient_id'];
                        }
                        $patients = $this->patients->getPatientByIds($patientIDs);
                        $finalArray = [];

                        foreach ($patients as $patient) {
                            foreach ($patient_transactions as $trans) {
                                if ($trans['patient_id'] == $patient['id']) {
                                    $trans['patient_name'] = $patient['pateint_name'];
                                    $finalArray[] = $trans;
                                }
                            }
                        }
                        foreach($finalArray as $trans){
                            if($trans['payment_type'] == 'CARD'){
                                $array['card'][] = $trans;
                            }
                        }

                        foreach($finalArray as $trans){
                            if($trans['payment_type'] == 'CASH'){
                                $array['cash'][] = $trans;
                            }
                        }

                        foreach($finalArray as $trans){
                            if($trans['payment_type'] == 'CHECK'){
                                $array['check'][] = $trans;
                            }
                        }
                        usort($array['card'],"sort_array_multi_dimention_by_id");
                        usort($array['check'],"sort_array_multi_dimention_by_id");
                        usort($array['cash'],"sort_array_multi_dimention_by_id");
                    }else{
                        $array['card'] = [];
                        $array['cash'] = [];
                        $array['check'] = [];
                    }
                    $html = $this->load->makeViewWithoutTemplate('report_3',$this->_pageData,true);

                }elseif($_POST['report_type'] == 4){ //Not in use for now

                    $date_r = explode('-',$_POST['date_range']);
                    $dateRange[0] = date('Y-m-d',strtotime($date_r[0]));
                    $dateRange[1] = date('Y-m-d',strtotime($date_r[1]));
                    $array['test_id'] = $_POST['test_id'];
                    $array['date_range'] = $_POST['date_range'];

                    $this->load->model('Reporting/patient_transactions_model', 'patient_transactions');
                    $for = $_POST['test_id'];
                    $patient_transactions = $this->patient_transactions->getTransactionsByServices($_POST['service_type'],$for,array('start'=>$dateRange[0],'end'=>$dateRange[1]));
                    $this->_pageData['transactions1'] = [];
                    $this->_pageData['transactions2'] = [];

                    foreach ($patient_transactions as $row) {
                        $this->_pageData['transactions1'][$row['patient_id']]['id'][] = $row['id'];
                        $this->_pageData['transactions1'][$row['patient_id']]['service'] = $row['service'];
                        $this->_pageData['transactions1'][$row['patient_id']]['doctor'][] = $row['doctor'];
                        $this->_pageData['transactions1'][$row['patient_id']]['test'][] = $row['test'];
                        $this->_pageData['transactions1'][$row['patient_id']]['other'][] = $row['other'];
                        $this->_pageData['transactions1'][$row['patient_id']]['bill_amount_figure'] = $row['bill_amount_figure'];
                        $this->_pageData['transactions1'][$row['patient_id']]['actual_amount'] = $row['actual_amount'];
                        $this->_pageData['transactions1'][$row['patient_id']]['created_on'] = $row['created_on'];
                    }
//                    echo '<pre>'.print_r($patient_transactions,true).'</pre>'; die;
                    $html = $this->load->makeViewWithoutTemplate('report_4',$this->_pageData,true);
                }elseif($_POST['report_type'] == 5){

                    $date_r = explode('-',$_POST['date_range']);
                    $dateRange[0] = date('Y-m-d',strtotime($date_r[0]));
                    $dateRange[1] = date('Y-m-d',strtotime($date_r[1]));
                    $this->_pageData['service_id'] = $_POST['service_id'];
                    $this->_pageData['date_range'] = $_POST['date_range'];

                    $this->load->model('Reporting/transactions_model', 'patient_transactions');
                    $this->load->model('Reporting/patients_model', 'patients');
                    $for = $_POST['service_id'];
                    $patient_transactions = $this->patient_transactions->getTransactionsByServices($for,array('start'=>$dateRange[0],'end'=>$dateRange[1]));

                    $patientIDs = [];
                    if(!empty($patient_transactions)) {
                        foreach ($patient_transactions as $trans) {
                            $patientIDs[] = $trans['patient_id'];
                        }
                        $patients = $this->patients->getPatientByIds($patientIDs);
                        $finalArray = [];

                        foreach ($patients as $patient) {
                            foreach ($patient_transactions as $trans) {
                                if ($trans['patient_id'] == $patient['id']) {
                                    $finalArray[] = [
                                        'transaction' => $trans,
                                        'patient' => $patient
                                    ];
                                }
                            }
                        }
                    }else{
                        $finalArray = [];
                    }
                    $this->_pageData['transactions'] = $finalArray;
                    $html = $this->load->makeViewWithoutTemplate('report_5',$this->_pageData,true);

                }elseif($_POST['report_type'] == 6){

                    $mrNumber = $_POST['mr_number'];
                    $record = explode('-',$mrNumber);
                    $caseId = $record[0];
                    $patientId = $record[1];
                    $array['mr_number'] = $_POST['mr_number'];
                    $this->load->model('panel_model', 'panel');
                    $array['panel'] = $this->panel->getAll();

                    $this->load->model('in_patient_model', 'patient');
                    $array['patient'] = $this->patient->getById($patientId);

                    $this->load->model('in_patient_case_model', 'case');
                    $array['case'] = $this->case->getById($caseId);

                    $this->load->model('in_patient_case_transaction', 'in_patient_transactions');
                    $array['transactions1'] = $this->in_patient_transactions->getTransactionsByMr($caseId,$patientId);
                    $this->load->view('reports/report_6',$array);

                }elseif($_POST['report_type'] == 7){

                    $date_r = explode('-',$_POST['date_range']);
                    $dateRange[0] = date('Y-m-d',strtotime($date_r[0]));
                    $dateRange[1] = date('Y-m-d',strtotime($date_r[1]));
                    $array['date_range'] = $_POST['date_range'];
                    $this->load->model('in_patient_case_transaction', 'in_patient_transactions');
                    $transaction = $this->in_patient_transactions->getTransactionsByDate(array('start'=>$dateRange[0],'end'=>$dateRange[1]));
                    $this->load->model('panel_model', 'panel');
                    $array['panel'] = $this->panel->getAll();

                    foreach ($transaction as $row) {

                        $array['transactions2'][$row['case_id'].'-'.$row['patient_id']]['transactions'][] = $row;

                        $this->load->model('in_patient_model', 'patient');
                        $array['transactions2'][$row['case_id'].'-'.$row['patient_id']]['patient'] = $this->patient->getById($row['patient_id']);

                        $this->load->model('in_patient_case_model', 'case');
                        $array['transactions2'][$row['case_id'].'-'.$row['patient_id']]['case'] = $this->case->getById($row['case_id']);
                    }

                    $html = $this->load->view('reports/report_7',$array, true);
                }
            }else{
                $html = $this->load->makeViewWithoutTemplate('index', $this->_pageData,true);
            }
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }
    function clear_current(){
        if($this->isLoggedIn()) {
            $user = $this->aauth->get_user();
            $parientIds = json_decode($_POST['patient_ids']);
            if (!empty($parientIds)) {
                $this->load->model('transactions_model', 'patient_transactions');
                $resp = $this->patient_transactions->accountsClearence(json_decode($_POST['patient_ids']),$user->id);
            }
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function editBill(){
        if($this->isLoggedIn()) {
            $this->load->model('doctors_model', 'doctors');
            $this->load->model('services_model', 'opd_services');
            $array = [
                'title'=>'Edit Bill',
                'doctors' => $this->aauth->list_users('ROLE_DOCTOR'),
                'opd_services' => $this->opd_services->getAllServices()
            ];
            if($this->havePost()){
                $ids = $_POST['receipt_number'];
                $amount_numbers = $_POST['amount_in_numbers'];
                $amount_words = $_POST['amount_in_words'];
                $this->load->model('transactions_model', 'patient_transactions');
                $arrayToDb =
                    [
                        'amount_in_num'=>$amount_numbers,
                        'amount_in_figure'=>$amount_words
                    ];
                if($_POST['doctor_id'] != ''){
                    $arrayToDb['doctor_id'] = $_POST['doctor_id'];
                }
                if($_POST['service_id'] != ''){
                    $arrayToDb['service_id'] = $_POST['service_id'];
                }
                if($_POST['payment_type'] != ''){
                    $arrayToDb['payment_type'] = $_POST['payment_type'];
                }
                $this->patient_transactions->changeAmount($ids,$arrayToDb);
                $this->activityLog($this->getUser()->name . ' has edited receipt',NULL,NULL,$ids);
                $this->session->set_flashdata('success', 'Invoice Updated.');
            }
            $this->_pageData['module'] = 'Edit Bill';
            $html = $this->load->view('accounts/reciept_change', $array,true);
            $this->makeView($html,$array);
        }else{
            $this->redirectUnauthorized();
        }
    }

    public function PendingPayments(){
        if($this->isLoggedIn() && $this->aauth->is_allowed('Receivables','Accounts')) {
            $this->load->model('Reporting/Transactions_model', 'opd_transactions');
            $this->load->model('Reporting/Patients_treatment_model', 'opd_treatments');


            $payment = $this->opd_transactions->getPaymentGroup();
            $treatments = $this->opd_treatments->getPaymentGroup();

            
            $paymentArray = [];
            foreach ($payment as $pay) {
                $paymentArray[$pay['patient']] = $pay['amount'];
            }
            $treatmentArray = [];
            foreach ($treatments as $treatment) {
                $treatmentArray[$treatment['patient']] = $treatment['amount'];
            }
            $finalArray = [];
            $patient_ids = [];

            foreach ($treatmentArray as $patient => $treatment) {
                if (array_key_exists($patient, $paymentArray)) {
                    if ($treatment != 0) {

                        if ($paymentArray[$patient] < $treatment) {
                            $finalArray[] = [
                                'patient_id' => $patient,
                                'treatment_total' => $treatment,
                                'payment' => $paymentArray[$patient]
                            ];
                            $patient_ids[] = $patient;
                        }
                    }
                } else {
                    if ($treatment != 0) {
                        $finalArray[] = [
                            'patient_id' => $patient,
                            'treatment_total' => $treatment,
                            'payment' => 0
                        ];
                        $patient_ids[] = $patient;
                    }
                }
            }

            $Patients_list = [];
            $amount_pending = 0;

            if(count($patient_ids) > 0){
                
                $this->load->model('Reporting/Patients_model','patient');
                $patients = $this->patient->getPatientByIds($patient_ids);
                
                
                
                foreach ($finalArray as $arr) {
                    foreach ($patients as $patient) {
                        if ($arr['patient_id'] == $patient['id']) {
                            $Patients_list[] = [
                                'id' => $patient['id'],
                                'name' => $patient['pateint_name'],
                                'contact' => $patient['patient_contact_mobile'],
                                'treatments' => $arr['treatment_total'],
                                'amount_payed' => $arr['payment'],
                                'pending' => (int)$arr['treatment_total'] - (int)$arr['payment']
                            ];
                            $amount_pending = $amount_pending + ((int)$arr['treatment_total'] - (int)$arr['payment']);
                        }
                    }
                }
            }

            $this->_pageData['title'] = 'Patient Receivable';
            $this->_pageData['receaveable'] = $amount_pending;
            $this->_pageData['module'] = 'Pending Payments';
            $this->_pageData['patients'] = $Patients_list;
            $html = $this->load->makeViewWithoutTemplate('receaveable', $this->_pageData, true);
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

}
