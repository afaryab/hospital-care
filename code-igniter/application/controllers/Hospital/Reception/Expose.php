<?php

class Expose extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        define('MODULE', 'Hospital');
    }
    
    public function index(){

        $id = array_key_exists('patient_id',$_GET) ? ($_GET['patient_id'] == '' ? 0 : $_GET['patient_id']) : 0;

        if($this->isLoggedIn()) {

            $this->load->model('commonModel', 'commonModel');

            $this->_pageData['opd_doctors'] =  []; // $this->aauth->getOpdDoctors('is_opd_doctor');
            $this->_pageData['inpatient_doctors'] =  []; // $this->aauth->getOpdDoctors('is_inpatient_doctor');
            $this->_pageData['xray_tech'] =  []; // $this->aauth->getOpdDoctors('is_xray_tech');
            $this->_pageData['dentists'] =  []; // $this->aauth->getOpdDoctors('is_dentist');
            $this->_pageData['ultradocs'] =  []; // $this->aauth->getOpdDoctors('is_ultrasound_doc');

            $this->_pageData['title'] = 'Hospital Counter';
            $this->_pageData['module'] = 'Hospital Counter';

            // $this->commonModel->setTableName('opd_services');
            $this->_pageData['opd_services'] =  [];
            $this->_pageData['inpatient_services'] =  [];
            $this->_pageData['emergency_services'] =  [];
            $this->_pageData['xray_services'] =  [];
            $this->_pageData['test_services'] =  [];
            $this->_pageData['inpd_rooms'] =  [];
            $this->_pageData['panel_companies'] =  [];
            $this->_pageData['dental_services'] =  [];
            $this->_pageData['ultrasound_services'] = []; // $this->commonModel->getAll();
            
            $id = array_key_exists('patient_id',$_GET) ? ($_GET['patient_id'] == '' ? 0 : $_GET['patient_id']) : 0;

            $this->_pageData['title'] = 'Hospital Counter';
            $this->_pageData['module'] = 'Hospital Counter';
            
            $this->load->model('commonModel', 'patients');
            $this->patients->setTableName('patients');
            $this->_pageData['patient'] = $this->patients->findOneBy(['id' => $id]);
            
            $this->load->model('commonModel', 'ps_numbers');
            $this->ps_numbers->setTableName('ps_numbers');
            $patientPSNumberRecord = $this->ps_numbers->findOneBy([
                'patient_id' => $id
            ]);
            

            if(!$patientPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                $psNumber = date('Y/m', strtotime($createdDateofPatient)) . '/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $psNumber
                ]);

                $patientPSNumberRecord = $this->ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['ps_number'] = $patientPSNumberRecord['ps_number'];
            

            // Get and Create OPD PSID

            $this->load->model('commonModel', 'opd_ps_numbers');
            $this->opd_ps_numbers->setTableName('opd_ps_numbers');
            $patientOPDPSNumberRecord = $this->opd_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientOPDPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                // Get OPD patient Id
                $this->load->model('commonModel', 'opd_patients');
                $this->opd_patients->setTableName('opd_patients');
                $opdPatientRecord = $this->opd_patients->findOneBy([
                    'site_patient_id' => $id
                ]);

                $opd_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/OPD/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->opd_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $opd_ps_id,
                    'opd_patient_id' => $opdPatientRecord ? $opdPatientRecord['id'] : null
                ]);

                $patientOPDPSNumberRecord = $this->opd_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['opd']['psid'] = $patientOPDPSNumberRecord['ps_number'];

            // Get All OPD Treatments for Patient
            $this->load->model('commonModel', 'opd_treatments');
            $this->opd_treatments->setTableName('opd_treatments');
            $this->_pageData['patient']['opd']['treatments'] = $this->opd_treatments->findBy([
                'patient_id' => $id
            ]);


            // Get and create Inpatient PSNumber

            $this->load->model('commonModel', 'inpatient_ps_numbers');
            $this->inpatient_ps_numbers->setTableName('inpatient_ps_numbers');
            $patientInpatientPSNumberRecord = $this->inpatient_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientInpatientPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");


                // Get Inpatient patient Id
                $this->load->model('commonModel', 'inpt_patients');
                $this->inpt_patients->setTableName('inpt_patients');
                $inptPatientRecord = $this->inpt_patients->findOneBy([
                    'site_patient_id' => $id
                ]);


                $inpatient_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/ID/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->inpatient_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $inpatient_ps_id,
                    'inpatient_patient_id' => $inptPatientRecord ? $inptPatientRecord['id'] : null
                ]);

                $patientInpatientPSNumberRecord = $this->inpatient_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['inpatient']['psid'] = $patientInpatientPSNumberRecord['ps_number'];

            // Get All Inpatient Treatments for Patient

            $this->load->model('commonModel', 'inpatient_file');
            $this->inpatient_file->setTableName('inpatient_file');
            $this->_pageData['patient']['inpatient']['treatments'] = $this->inpatient_file->findBy([
                'patient_id' => $id
            ]);


            // Get and create Emergency PSNumber
            $this->load->model('commonModel', 'emergency_ps_numbers');
            $this->emergency_ps_numbers->setTableName('emergency_ps_numbers');
            $patientEmergencyPSNumberRecord = $this->emergency_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientEmergencyPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");


                // Get Emergency patient Id
                $this->load->model('commonModel', 'emergency_patients');
                $this->emergency_patients->setTableName('emergency_patients');
                $emergencyPatientRecord = $this->emergency_patients->findOneBy([
                    'site_patient_id' => $id
                ]);

                $emergency_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/ER/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->emergency_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $emergency_ps_id,
                    'emergency_patient_id' => $emergencyPatientRecord ? $emergencyPatientRecord['id'] : null
                ]);

                $patientEmergencyPSNumberRecord = $this->emergency_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['emergency']['psid'] = $patientEmergencyPSNumberRecord['ps_number'];

            // Get All Emergency Treatments for Patient

            $this->load->model('commonModel', 'emergency_treatments');
            $this->emergency_treatments->setTableName('emergency_treatments');
            $this->_pageData['patient']['emergency']['treatments'] = $this->emergency_treatments->findBy([
                'patient_id' => $id
            ]);

            // Get and create Dental PSNumber
            $this->load->model('commonModel', 'dental_ps_numbers');
            $this->dental_ps_numbers->setTableName('dental_ps_numbers');
            $patientDentalPSNumberRecord = $this->dental_ps_numbers->findOneBy([
                'patient_id' => $id,
            ]);

            if(!$patientDentalPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                // Get Dental patient Id
                $this->load->model('commonModel', 'dental_patients');
                $this->dental_patients->setTableName('dental_patients');
                $dentalPatientRecord = $this->dental_patients->findOneBy([
                    'site_patient_id' => $id
                ]);

                if(!$dentalPatientRecord){
                    // If dental patient record does not exist, create one
                    $this->dental_patients->addNew([
                        'site_patient_id' => $id,
                        'created_on' => $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s')
                    ]);
                    $dentalPatientRecord = $this->dental_patients->findOneBy([
                        'site_patient_id' => $id
                    ]);
                }


                $dental_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/DEN/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->dental_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $dental_ps_id,
                    'dental_patient_id' => $dentalPatientRecord ? $dentalPatientRecord['id'] : null
                ]);

                $patientDentalPSNumberRecord = $this->dental_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['dental']['psid'] = $patientDentalPSNumberRecord['ps_number'];
            // Get All Dental Treatments for Patient
            $this->load->model('commonModel', 'dental_treatments');
            $this->dental_treatments->setTableName('dental_treatments');
            $this->_pageData['patient']['dental']['treatments'] = $this->dental_treatments->findBy([
                'patient_id' => $id
            ]);

            // Get and create Ultrasound PSNumber
            $this->load->model('commonModel', 'ultrasound_ps_numbers');
            $this->ultrasound_ps_numbers->setTableName('ultrasound_ps_numbers');
            $patientUltrasoundPSNumberRecord = $this->ultrasound_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientUltrasoundPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                // Get Ultrasound patient Id
                $this->load->model('commonModel', 'ultrasound_patients');
                $this->ultrasound_patients->setTableName('ultrasound_patients');
                $ultrasoundPatientRecord = $this->ultrasound_patients->findOneBy([
                    'site_patient_id' => $id
                ]);

                if(!$ultrasoundPatientRecord){
                    // If ultrasound patient record does not exist, create one
                    $this->ultrasound_patients->addNew([
                        'site_patient_id' => $id,
                        'created_on' => $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s')
                    ]);
                    $ultrasoundPatientRecord = $this->ultrasound_patients->findOneBy([
                        'site_patient_id' => $id
                    ]);
                }


                $ultrasound_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/ULS/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->ultrasound_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $ultrasound_ps_id,
                    'ultrasound_patient_id' => $ultrasoundPatientRecord ? $ultrasoundPatientRecord['id'] : null
                ]);

                $patientUltrasoundPSNumberRecord = $this->ultrasound_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['ultrasound']['psid'] = $patientUltrasoundPSNumberRecord['ps_number'];
            // Get All Ultrasound Treatments for Patient
            $this->load->model('commonModel', 'ultrasound_treatments');
            $this->ultrasound_treatments->setTableName('ultrasound_treatments');
            $this->_pageData['patient']['ultrasound']['treatments'] = $this->ultrasound_treatments->findBy([
                'patient_id' => $id
            ]);

            // Get and Create XRay PSID
            $this->load->model('commonModel', 'xray_ps_numbers');
            $this->xray_ps_numbers->setTableName('xray_ps_numbers');
            $patientXRayPSNumberRecord = $this->xray_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientXRayPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                // Get XRay patient Id
                $this->load->model('commonModel', 'xray_patients');
                $this->xray_patients->setTableName('xray_patients');
                $xrayPatientRecord = $this->xray_patients->findOneBy([
                    'site_patient_id' => $id
                ]);

                $xray_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/XRAY/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->xray_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $xray_ps_id,
                    'xray_patient_id' => $xrayPatientRecord ? $xrayPatientRecord['id'] : null
                ]);

                $patientXRayPSNumberRecord = $this->xray_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['xray']['psid'] = $patientXRayPSNumberRecord['ps_number'];
            // Get All XRay Treatments for Patient
            $this->load->model('commonModel', 'xray_treatments');
            $this->xray_treatments->setTableName('xray_treatments');
            $this->_pageData['patient']['xray']['treatments'] = $this->xray_treatments->findBy([
                'patient_id' => $id
            ]);


            // Get and create Lab PSNumber
            $this->load->model('commonModel', 'lab_ps_numbers');
            $this->lab_ps_numbers->setTableName('lab_ps_numbers');
            $patientLabPSNumberRecord = $this->lab_ps_numbers->findOneBy([
                'patient_id' => $id
            ]);

            if(!$patientLabPSNumberRecord){

                $createdDateofPatient = $this->_pageData['patient']['created_on'] ?? date('Y-m-d H:i:s');

                // Check how many patients registered that month
                $monthStart = date('Y-m-01 00:00:00', strtotime($createdDateofPatient));
                $monthEnd = date('Y-m-t 23:59:59', strtotime($createdDateofPatient));
                $patientsRegisteredThatMonth = $this->patients->countBy("created_on >= '$monthStart' AND created_on <= '$monthEnd'");

                // Get Lab patient Id
                $this->load->model('commonModel', 'laboratory_patients');
                $this->laboratory_patients->setTableName('laboratory_patients');
                $labPatientRecord = $this->laboratory_patients->findOneBy([
                    'site_patient_id' => $id
                ]);


                $lab_ps_id = date('Y/m', strtotime($createdDateofPatient)) . '/LAB/' . str_pad($patientsRegisteredThatMonth + 1, 6, '0', STR_PAD_LEFT);

                $this->lab_ps_numbers->addNew([
                    'patient_id' => $id,
                    'ps_number' => $lab_ps_id,
                    'lab_patient_id' => $labPatientRecord ? $labPatientRecord['id'] : null
                ]);

                $patientLabPSNumberRecord = $this->lab_ps_numbers->findOneBy([
                    'patient_id' => $id
                ]);

            }

            $this->_pageData['patient']['lab']['psid'] = $patientLabPSNumberRecord['ps_number'];
            // Get All Lab Tests for Patient
            $this->load->model('commonModel', 'test_treatments');
            $this->test_treatments->setTableName('test_treatments');
            $this->_pageData['patient']['lab']['tests'] = $this->test_treatments->findBy([
                'patient_id' => $id
            ]);
            
            $html = $this->load->makeViewWithOutTemplate('expose', $this->_pageData, true);
            
            $this->makeView($html);
        }else{
            $this->redirectUnauthorized();
        }
    }

    
}

