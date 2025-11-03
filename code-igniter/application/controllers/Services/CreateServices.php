<?php

class CreateServices extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Services');
    }
    
    public function index(){
        if (!$this->aauth->is_loggedin()) {
            $this->redirectUnauthorized();
        }
            $this->load->model('commonModel', 'opd_services');
            $this->opd_services->setTableName('opd_services');
            $this->_pageData['opd_services'] =  $this->opd_services->getAll();

            $this->load->model('commonModel', 'inpd_services');
            $this->inpd_services->setTableName('inpd_services');
            $this->_pageData['inpatient_services'] =  $this->inpd_services->getAll();

            $this->load->model('commonModel', 'emergency_services');
            $this->emergency_services->setTableName('emergency_services');
            $this->_pageData['emergency_services'] =  $this->emergency_services->getAll();

            $this->load->model('commonModel', 'xray_services');
            $this->xray_services->setTableName('xray_services');
            $this->_pageData['xray_services'] =  $this->xray_services->getAll();

            $this->load->model('commonModel', 'test_services');
            $this->test_services->setTableName('test_services');
            $this->_pageData['test_services'] =  $this->test_services->getAll();

            $this->load->model('commonModel', 'dental_services');
            $this->dental_services->setTableName('dental_services');
            $this->_pageData['dental_services'] =  $this->dental_services->getAll();

            $this->load->model('commonModel', 'ultrasound_services');
            $this->ultrasound_services->setTableName('ultrasound_services');
            $this->_pageData['ultrasound_services'] =  $this->ultrasound_services->getAll();
        
            $this->load->model('commonModel', 'recestation_services');
            $this->recestation_services->setTableName('recestation_services');
            $this->_pageData['recestation_services'] =  $this->recestation_services->getAll();
        


        
        if ($this->havePost()) {
            if($_POST['service'] == 'opd')
            {
               $opdservices=[
               'name' => $_POST['name'],
               'charges' => $_POST['charges'],
               'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
               'entered_by' => 1,
               'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
               'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
               ];
               //print_array($opdservices);
               $this->opd_services->addNew($opdservices);
            }elseif($_POST['service'] == 'inp')
            {
                $inpservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1, 
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->inpd_services->addNew($inpservices);

            }elseif($_POST['service'] == 'emer')
            {
                $emerservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1, 
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->emergency_services->addNew($emerservices);


            }elseif($_POST['service'] == 'xray')
            {
                $xrayservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1,
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->xray_services->addNew($xrayservices);
                
            }elseif($_POST['service'] == 'test')
            {
                $testservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'shrt_code' => array_key_exists('shrt_code',$_POST) ? $_POST['shrt_code'] : NULL,
                'sample' => array_key_exists('sample',$_POST) ? $_POST['sample'] : NULL,
                'reporting_time' => $_POST['reporting_time'],
                ];
                $this->test_services->addNew($testservices);

            }elseif($_POST['service'] == 'dental')
            {
               $dentalservices=[
               'name' => $_POST['name'],
               'charges' => $_POST['charges'],
               'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
               'entered_by' => 1,
               'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
               'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
               ];
               //print_array($opdservices);
               $this->dental_services->addNew($dentalservices);
            }elseif($_POST['service'] == 'ultra')
            {
               $ultraservices=[
               'name' => $_POST['name'],
               'charges' => $_POST['charges'],
               'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
               'entered_by' => 1,
               'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
               'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
               ];
               //print_array($opdservices);
               $this->ultrasound_services->addNew($ultraservices);
            }elseif($_POST['service'] == 'reces')
            {
               $recestatiionservices=[
               'name' => $_POST['name'],
               'charges' => $_POST['charges'],
               'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
               'entered_by' => 1,
               'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
               ];
               //print_array($opdservices);
               $this->recestation_services->addNew($recestatiionservices);
            }
            $this->setMessage('success', 'Service created successfully!');
            $this->activityLog('Service created successfully');
            redirect($this->_pageData['SERVICES_LIST']);
  
        }
        
       
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Create Service';
        $html = $this->load->makeViewWithOutTemplate('create_services',$this->_pageData,true);
        
        $this->makeView($html);
    }
}

