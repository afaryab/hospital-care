<?php

class CreateDentalTreatments extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Treatments');
    }
    
    public function index($id = 0){

        if($this->isLoggedIn()) {

            if($id != 0)
            {
                $this->load->model('commonModel', 'commonModel');

                //$this->_pageData['inpatient_doctors'] = $this->aauth->getOpdDoctors('is_inpatient_doctor');
                $this->_pageData['dentists'] = $this->aauth->getOpdDoctors('is_dentist');
                $this->_pageData['title'] = 'Hospital Create Dental Treatment';
                $this->_pageData['module'] = 'Hospital Create Dental Treatment';

                $this->commonModel->setTableName('dental_services');
                $this->_pageData['dental_services'] =  $this->commonModel->getAll();
                //$this->_pageData['closingArray'] = $this->receptionClosingArray;
                
                $userID = $this->aauth->get_user_id();
                $user = $this->aauth->get_user($userID);
                //print_array($user);
                if($user->is_dentist == 1){
                    if($this->havePost()){

                        $this->load->model('commonModel', 'dental_patient_file');
                        $this->dental_patient_file->setTableName('dental_patient_file');
                        $file = $this->dental_patient_file->findOneBy(['id' =>$id]);

                        $treatmentArray = [
                            'status' => 'OPEN',
                            'patient_id' => $file['patient_id'],
                            'dental_patient_id' => $file['dental_patient_id'],
                            'patient_is_first_visit' => 1,
                            'treatment_by' => $this->aauth->get_user_id(),
                            'will_occure_on' => date("Y-m-d h:i:s"),
                            'service_id' => $file['service_id'],
                            'service_name' => $file['service_name'],
                            'file_id' => $file['id'],
                            'name' => $_POST['treatment_name'],
                            'description' => $_POST['treatment_desc'],
                        ];

                        $this->load->model('commonModel', 'dental_treatments');
                        $this->dental_treatments->setTableName('dental_treatments');
                        $treatmentId = $this->dental_treatments->addNew($treatmentArray);

                        
                        $this->setMessage('success', 'Treatment added successfully!');
                        $this->activityLog('Treatment added successfully');
                        
                        redirect($this->_pageData['DENTAL_PATIENT_PROFILE'].$id);

                    }
                }
            
            }
            $html = $this->load->makeViewWithOutTemplate('create_dental_treatments', $this->_pageData, true);
            $this->makeView($html);

        }else{
            $this->redirectUnauthorized();
        }
    }

    
    
}

