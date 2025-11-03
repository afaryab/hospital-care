<?php  if ( ! defined('BASEPATH')) exit("No direct script access allowed");

class Upgrade extends CI_Controller {

  public function __construct()
  {
    parent::__construct();

    // $this->input->is_cli_request() 
    //   or exit("Execute via command line: php index.php upgrade");

  }

  public function index(){

    // Make sure execution take as mcuh time as needed
    set_time_limit(0);
    // Make sure execution use as much memory as needed
    ini_set('memory_limit', '-1');
    

    $this->upgradePatientsRecord();

    $this->upgradeOPDPatientsRecord();

    $this->upgradeInpatientRecords();

    $this->upgradeEmergencyPatientsRecords();

    $this->upgradeXRaysPatientsRecords();

    $this->upgradeDentalPatientsRecords();

    $this->upgradeLaboratoryPatientsRecords();

    $this->upgradeUltraSoundPatientsRecords();

  }

  public function upgradeUltraSoundPatientsRecords(){

    // Fetch all ultrasound patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('ultrasound_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('ultrasound_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'ultrasound_ps_numbers');
      $this->ultrasound_ps_numbers->setTableName('ultrasound_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'ultrasound_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('ultrasound_ps_numbers', $ps_number_data);

        // echo "Inserted Ultrasound PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }
    }

  }

  public function upgradeLaboratoryPatientsRecords(){

    // Fetch all laboratory patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('laboratory_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('laboratory_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'lab_ps_numbers');
      $this->lab_ps_numbers->setTableName('lab_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'lab_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('lab_ps_numbers',$ps_number_data);

        // echo "Inserted Laboratory PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }
    }

  }

  public function upgradeDentalPatientsRecords(){

    // Fetch all dental patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('dental_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('dental_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'dental_ps_numbers');
      $this->dental_ps_numbers->setTableName('dental_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'dental_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('dental_ps_numbers',$ps_number_data);

        // echo "Inserted Dental PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }
    }

  }


  public function upgradeXRaysPatientsRecords(){

    // Fetch all xray patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('xray_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('xray_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'xray_ps_numbers');
      $this->xray_ps_numbers->setTableName('xray_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'xray_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('xray_ps_numbers',$ps_number_data);

        // echo "Inserted XRay PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }
    }

  }



  public function upgradeEmergencyPatientsRecords()
  {
    // Fetch all emergency patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('emergency_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('emergency_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'emergency_ps_numbers');
      $this->emergency_ps_numbers->setTableName('emergency_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'emergency_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('emergency_ps_numbers',$ps_number_data);

        // echo "Inserted Emergency PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }
    }
  }

  public function upgradeInpatientRecords()
  {
    // Fetch all inpatient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('inpt_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $inpatientCountData = $query->result_array();

    foreach($inpatientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('inpt_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $inpatients = $query->result_array();

      $this->load->model('commonModel', 'inpatient_ps_numbers');
      $this->inpatient_ps_numbers->setTableName('inpatient_ps_numbers');
      $ps_number_counter = 1;

      foreach($inpatients as $inpatient) {

        $inpatient_id = $inpatient['id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'site_patient_id' => $inpatient_id,
          'inpatient_patient_id' => $inpatient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('inpatient_ps_numbers',$ps_number_data);

        // echo "Inserted Inpatient PS Number: " . $ps_number . " for Inpatient ID: " . $inpatient_id . "\n";

        $ps_number_counter++;
      }
    }
  }

  public function upgradeOPDPatientsRecord()
  {
    
    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('opd_patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, site_patient_id, created_on');
      $this->db->from('opd_patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'opd_ps_numbers');
      $this->opd_ps_numbers->setTableName('opd_ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['site_patient_id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'opd_patient_id' => $patient['id'],
          'ps_number' => $ps_number,
        ];

        $this->db->insert('opd_ps_numbers',$ps_number_data);

        // echo "Inserted OPD PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }



    }

  }

  public function upgradePatientsRecord()
  {

    // Fetch all patient rows grouped by month and year

    $this->db->select('DATE_FORMAT(created_on, "%Y/%m") AS month, count(*) AS total');
    $this->db->from('patients');
    $this->db->group_by('DATE_FORMAT(created_on, "%Y/%m")');
    //Make sure the order is last to latest
    $this->db->order_by('created_on', 'ASC');

    $query = $this->db->get();

    $patientCountData = $query->result_array();

    foreach($patientCountData as $data) {
      

      $month = $data['month'];
      $total = $data['total'];

      $this->db->select('id, created_on');
      $this->db->from('patients');
      $this->db->like('created_on', $month, 'after');
      $this->db->order_by('created_on', 'ASC');
      $query = $this->db->get();

      $patients = $query->result_array();

      $this->load->model('commonModel', 'ps_numbers');
      $this->ps_numbers->setTableName('ps_numbers');
      $ps_number_counter = 1;

      foreach($patients as $patient) {

        $patient_id = $patient['id'];

        $ps_number = $month . '/' . str_pad($ps_number_counter, 6, '0', STR_PAD_LEFT);

        // Insert PS Number record
        $ps_number_data = [
          'patient_id' => $patient_id,
          'ps_number' => $ps_number,
        ];

        $this->db->insert('ps_numbers',$ps_number_data);

        // echo "Inserted PS Number: " . $ps_number . " for Patient ID: " . $patient_id . "\n";

        $ps_number_counter++;
      }



    }

  }
}