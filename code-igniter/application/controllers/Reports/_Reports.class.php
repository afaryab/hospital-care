<?php

class Reports{
    
    protected $_reports_url = 'Reports/OPD/OPDAccounts/Index';
    protected $_pending_payments_url = 'Reports/OPD/OPDAccounts/PendingPayments';
    protected $_patients_reports_url = 'Reports/OPD/OPDPatientReports/Index';
    
    function getNavigation(){
        return [
            'navigations' => [
                
            
            ],
            'top_nav' => [
            ],
            'urlsToRemember' => [
                'REPORTS_INDEX' => $this->_reports_url
            ]
        ];
    }
    
    
}