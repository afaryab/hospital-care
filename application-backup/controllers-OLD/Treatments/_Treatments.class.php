<?php

class Treatments{
    
    
    protected $_create_dental_treatment = 'Treatments/CreateDentalTreatments/Index/';
    protected $_dental_profile = 'Hospital/Reception/Register/PatientProfile/DentalPatientProfile/Index/';
    function getNavigation(){
        return [
            
            'urlsToRemember' => [
 
                'DENTAL_PATIENT_PROFILE' =>$this->_dental_profile,
                'NEW_DENTAL_TREATMENT' => $this->_create_dental_treatment,
            ]
            ];
    }
    
    
}