<?php

class EditService extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Services');
    }
    
    public function index($id){
        if ($id != 0) {
           
        
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
            
            
        
        }
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
        
        $this->makeView($html);
    }

    public function editOpd($id){
        $this->load->model('commonModel', 'opd_services');
        $this->opd_services->setTableName('opd_services');
        $this->_pageData['opd_services'] =  $this->opd_services->findOneBy(['id' => $id]);
        
        $type = 'opd';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            
            $opdservices=[
            'name' => $_POST['name'],
            'charges' => $_POST['charges'],
            'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
            'entered_by' => 1,
            'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
            'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
            'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
            ];
            //print_array($opdservices);

            
            $this->opd_services->updateRecord($id,$opdservices);

            $this->setMessage('success', 'Service edited successfully!');
            $this->activityLog('Service edited successfully');
            redirect($this->_pageData['SERVICES_LIST']);
            
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }

    public function editInp($id){
        $this->load->model('commonModel', 'inpd_services');
        $this->inpd_services->setTableName('inpd_services');
        $this->_pageData['inpatient_services'] =  $this->inpd_services->findOneBy(['id' => $id]);

        $type = 'inp';
        $this->_pageData['type'] = $type;


        if ($this->havePost()) {
            $inpservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1,
                'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->inpd_services->updateRecord($id,$inpservices);

                $this->setMessage('success', 'Service edited successfully!');
                $this->activityLog('Service edited successfully');
                redirect($this->_pageData['SERVICES_LIST']);
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }

    public function editEmer($id){
        $this->load->model('commonModel', 'emergency_services');
        $this->emergency_services->setTableName('emergency_services');
        $this->_pageData['emergency_services'] =  $this->emergency_services->findOneBy(['id' => $id]);

        $type = 'emer';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            $emerservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1, 
                'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->emergency_services->updateRecord($id,$emerservices);

                $this->setMessage('success', 'Service edited successfully!');
                $this->activityLog('Service edited successfully');
                redirect($this->_pageData['SERVICES_LIST']);
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }

    public function editXray($id){
        $this->load->model('commonModel', 'xray_services');
        $this->xray_services->setTableName('xray_services');
        $this->_pageData['xray_services'] =  $this->xray_services->findOneBy(['id' => $id]);

        $type = 'xray';
        $this->_pageData['type'] = $type;


        if ($this->havePost()) {
            $xrayservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
                'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
                'entered_by' => 1,
                'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
                'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
                ];
                $this->xray_services->updateRecord($id,$xrayservices);

                $this->setMessage('success', 'Service edited successfully!');
                $this->activityLog('Service edited successfully');
                redirect($this->_pageData['SERVICES_LIST']);
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }

    public function editTest($id){
        $this->load->model('commonModel', 'test_services');
        $this->test_services->setTableName('test_services');
        $this->_pageData['test_services'] =  $this->test_services->findOneBy(['id' => $id]);

        $type = 'test';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            $testservices = [
                'name' => $_POST['name'],
                'charges' => $_POST['charges'],
                'shrt_code' => array_key_exists('shrt_code',$_POST) ? $_POST['shrt_code'] : NULL,
                'sample' => array_key_exists('sample',$_POST) ? $_POST['sample'] : NULL,
                'entered_by' => 1,
                'reporting_time' => $_POST['reporting_time'], 
                ];
                $this->test_services->updateRecord($id,$testservices);

                $this->setMessage('success', 'Service edited successfully!');
                $this->activityLog('Service edited successfully');
                redirect($this->_pageData['SERVICES_LIST']);
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }
    public function editDental($id){
        $this->load->model('commonModel', 'dental_services');
        $this->dental_services->setTableName('dental_services');
        $this->_pageData['dental_services'] =  $this->dental_services->findOneBy(['id' => $id]);
        
        $type = 'dental';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            
            $dentalservices=[
            'name' => $_POST['name'],
            'charges' => $_POST['charges'],
            'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
            'entered_by' => 1,
            'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
            'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
            'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
            ];
            //print_array($opdservices);

            
            $this->dental_services->updateRecord($id,$dentalservices);

            $this->setMessage('success', 'Service edited successfully!');
            $this->activityLog('Service edited successfully');
            redirect($this->_pageData['SERVICES_LIST']);
            
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }
    public function editUltra($id){
        $this->load->model('commonModel', 'ultrasound_services');
        $this->ultrasound_services->setTableName('ultrasound_services');
        $this->_pageData['ultrasound_services'] =  $this->ultrasound_services->findOneBy(['id' => $id]);
        
        $type = 'ultra';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            
            $ultraservices=[
            'name' => $_POST['name'],
            'charges' => $_POST['charges'],
            'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
            'entered_by' => 1,
            'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
            'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
            'is_fileable' => array_key_exists('is_fileable',$_POST) ? $_POST['is_fileable'] : 0,
            ];
            //print_array($opdservices);

            
            $this->ultrasound_services->updateRecord($id,$ultraservices);

            $this->setMessage('success', 'Service edited successfully!');
            $this->activityLog('Service edited successfully');
            redirect($this->_pageData['SERVICES_LIST']);
            
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }
    public function editReces($id){
        $this->load->model('commonModel', 'recestation_services');
        $this->recestation_services->setTableName('recestation_services');
        $this->_pageData['recestation_services'] =  $this->recestation_services->findOneBy(['id' => $id]);
        
        $type = 'reces';
        $this->_pageData['type'] = $type;

        if ($this->havePost()) {
            
            $recestationservices=[
            'name' => $_POST['name'],
            'charges' => $_POST['charges'],
            'post_key' => array_key_exists('post_key',$_POST) ? $_POST['post_key'] : NULL,
            'entered_by' => 1,
            'is_doctor_selectable' => array_key_exists('is_doctor_selectable',$_POST) ? $_POST['is_doctor_selectable'] : 0,
            'is_deleted' => array_key_exists('is_deleted',$_POST) ? $_POST['is_deleted'] : 0,
            ];
            //print_array($opdservices);

            
            $this->recestation_services->updateRecord($id,$recestationservices);

            $this->setMessage('success', 'Service edited successfully!');
            $this->activityLog('Service edited successfully');
            redirect($this->_pageData['SERVICES_LIST']);
            
        }

        
        $this->_pageData['module'] = 'services';
        $this->_pageData['title'] = 'Edit Service';
        $html = $this->load->makeViewWithOutTemplate('edit_service',$this->_pageData,true);
    
        $this->makeView($html);

    }

}

