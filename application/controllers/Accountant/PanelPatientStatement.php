<?php

class PanelPatientStatement extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Accountant');
    }
    
    public function index(){

        if($this->isLoggedIn()) {
            
            $this->_pageData['title'] = 'Panel Patient Statement';
            $this->_pageData['module'] = 'Panel Patient Statement';
            
            $this->load->model('commonModel', 'commonModel');
            $this->load->model('commonModel', 'transaction');

            $this->_pageData['inpstatement'] = [];
            
           


                    $this->load->model('commonModel','inpatient_file');
                    $this->inpatient_file->setTableName('inpatient_file');
                   // $this->_pageData['files'] = $this->inpatient_file->getAll();
                    $this->load->model('commonModel','inpatient_transactions');
                    $this->inpatient_transactions->setTableName('inpatient_transactions');
                    $this->_pageData['transactions'] = $this->inpatient_transactions->getAll();
                    $this->load->model('commonModel','recestation_transactions');
                    $this->recestation_transactions->setTableName('recestation_transactions');
                    $this->_pageData['recestrans'] = $this->recestation_transactions->getAll();
                    $this->load->model('commonModel','patients');
                    $this->patients->setTableName('patients');
                    $this->_pageData['patients'] = $this->patients->getAll();

                    $this->load->model('commonModel', 'panel_payments');
                    $this->panel_payments->setTableName('panel_payments');
                    $this->_pageData['panel_payments'] = $this->panel_payments->getAll(); 

                    $this->load->model('commonModel', 'panels');
                    $this->panels->setTableName('panel_companies');
                    $this->_pageData['panels'] = $this->panels->getAll();

            $dateType = array_key_exists('dtype',$_GET) ? $_GET['dtype'] : 'R'; //R For Range

            $date = array_key_exists('date',$_GET) ? $_GET['date'] : 'Today'; 

            $panel_comp = array_key_exists('p_company',$_GET) ? $_GET['p_company'] : '';
// echo $panel_comp;die;
            if($panel_comp != '' && $panel_comp != 'all'){
                $this->_pageData['selectedCompany'] = array_filter(
                    $this->_pageData['panels'],
                    function ($e) use (&$panel_comp) {
                        return $e['name'] == $panel_comp;
                    }
                );

                $seldoc = $this->_pageData['selectedCompany'];
                $this->_pageData['selectedCompany'] = reset($this->_pageData['selectedCompany']);
                
            }
            
            if($dateType == 'S'){

                $date = array_key_exists('date',$_GET) ? date("Y-m-d", strtotime($_GET['date'])) :  date("Y-m-d");

                //$this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) = ' => $date,'panel_name' => $panel_comp ]);
                if($panel_comp != '' && $panel_comp != 'all'){
                    $this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) = ' => $date,'panel_name' => $panel_comp ]);
                }else{
                    // echo $panel_comp;die;
                    $$this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) = ' => $date ]);

                }

            }else{

                if(array_key_exists('date_range',$_GET) && $_GET['date_range']!=''){
                    
                    $date_r = explode('-',$_GET['date_range']);
                    $start_date = date('Y-m-d',strtotime($date_r[0]));
                    $end_date = date('Y-m-d',strtotime($date_r[1]));

                }else{
                    $start_date = array_key_exists('sdate',$_GET) ? date("Y-m-d", strtotime($_GET['sdate'])) :  date("Y-m-d", strtotime("-10 day"));

                    $end_date = array_key_exists('edate',$_GET) ? date("Y-m-d", strtotime($_GET['edate'])) :  date("Y-m-d");
                }
                $date = [
                    'start' => $start_date,
                    'end' => $end_date
                ];

                //$this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'panel_name' => $panel_comp ]);
                if($panel_comp != '' && $panel_comp != 'all'){
                    $this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end'],'panel_name' => $panel_comp ]);
                }else{
                    // echo $panel_comp;die;
                    $this->_pageData['inpstatement'] = $this->inpatient_file->findBy(['CAST(created_on AS DATE) >= ' => $date['start'], 'CAST(created_on AS DATE) <= ' => $date['end']]);

                }

            }

                   
                    //incomplete logic

                //     $inp = $this->_pageData['files'];
                   
                //     // $id = $inp['id'];
                //     // $inpd = $inp['patient_id'];
                //     // $id = array_column($in, 'id');
                //     // $inpd = array_column($in, 'patient_id');
                //     foreach($inp as $in)
                //     {
                  
                //     // $id = array_column($in, 'id');
                //     // $inpd = array_column($in, 'patient_id');
                //     $id = $in['id'];
                //     $inpd = $in['patient_id'];
                    
                //     $this->load->model('commonModel','inpatient_transactions');
                //     $this->inpatient_transactions->setTableName('inpatient_transactions');
                //     $this->_pageData['trans'] = $this->inpatient_transactions->findBy(['file_id' => $id]);
                   

                //     $this->load->model('commonModel','patients');
                //     $this->patients->setTableName('patients');
                //     $this->_pageData['patient'] = $this->patients->findOneBy(['id' => $inpd]);
                //     //print_array($this->_pageData['patient']);
                //    // $this->_pageData['files'] = $in;

                //     }
                $this->_pageData['date'] = $date;

            $html = $this->load->makeViewWithOutTemplate('panel_patient_statement', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    

}

