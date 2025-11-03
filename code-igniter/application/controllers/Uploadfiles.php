<?php

/**
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 3/1/2016
 * Time: 4:49 AM
 */
class uploadfiles extends CI_Controller
{
    public function index(){

    }

    public function mediafile(){

        $patientId = array_key_exists('patient_id',$_GET) ? $_GET['patient_id'] : 'undefined';
        $opdPatientId = array_key_exists('opd_patient',$_GET) ? $_GET['opd_patient'] : 'undefined';
        $type = array_key_exists('type',$_GET) ? $_GET['type'] : 'undefined';
        $treatmentId = array_key_exists('treatment_id',$_GET) ? $_GET['treatment_id'] : 'undefined';
        

        //Fetch Patient

        $uploaddir = __DIR__.'/../../uploads/'.$patientId.'/'.$opdPatientId.'/'.$type.'/'.$treatmentId.'/';
        $publicfolder = 'uploads/'.$patientId.'/'.$opdPatientId.'/'.$type.'/'.$treatmentId.'/';
        if ( ! is_dir($uploaddir)) {
            if(mkdir($uploaddir, 0777 , true)){
                $uploaddir = __DIR__.'/../../uploads/'.$patientId.'/'.$opdPatientId.'/'.$type.'/'.$treatmentId.'/';
                $publicfolder = 'uploads/'.$patientId.'/'.$opdPatientId.'/'.$type.'/'.$treatmentId.'/';
            }else{
                $uploaddir = __DIR__.'/../../uploads/anonymous/';
                $publicfolder = 'uploads/anonymous/';
            }
        }
        $path = $_FILES['file']['name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $imgname = generateRandomString();
        $uploadfile = $uploaddir . $imgname.'.'.$ext;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            echo $publicfolder.$imgname.'.'.$ext;
        } else {
            echo 0;
        }
    }

}