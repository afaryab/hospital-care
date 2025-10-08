<?php
/**
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 5/21/2018
 * Time: 2:15 PM
 */

class DataImport extends CI_Controller
{
    protected $_oldDb = null;
    protected $_activities = [];
    protected $_respCode = 500;
    protected $_respMessage = "Failed";

    public function index($tableName = "bsofts_clinic",$user = 'root', $password = 'Bsofts!23',$host = 'localhost'){

        if($tableName){

            $this->connectDb($tableName,$user,$password,$host);

            $this->ImportFromTables();

        }else{
            $this->showResp();
        }
    }

    protected function connectDb($dbname,$user,$password ,$host){

        $this->setActivity('Connecting to database');

        $c['hostname'] = $host;
        $c['username'] = $user;
        $c['password'] = $password;
        $c['database'] = $dbname;
        $c['dbdriver'] = "mysqli";
        $c['dbprefix'] = "";
        $c['pconnect'] = TRUE;
        $c['db_debug'] = TRUE;
        $c['cache_on'] = FALSE;
        $c['cachedir'] = '';
        $c['char_set'] = 'utf8';
        $c['dbcollat'] = 'utf8_general_ci';
        $c['swap_pre'] = '';
        $c['autoinit'] = TRUE;
        $c['stricton'] = FALSE;


        $this->_oldDb = $this->load->database($c, TRUE, TRUE);
    }

    protected function ImportFromTables(){

        $this->setActivity('Collecting Tables To Import');
        $array = $this->getTablesToImport();

        foreach ($array as $tableName){
            $this->ImportFromTable($tableName);
        }
        $this->db->truncate('aauth_perms');
        $this->db->insert('aauth_perms',['name' => 'all', 'definition' => '', 'perm_group'=>'ANONYMOUS']);
        $this->db->where(['id' => 1])->update('aauth_users',['is_super_admin' => 1]);
        $this->showSuccess();
    }

    protected function ImportFromTable($tableName){

        switch ($tableName){
            case 'aauth_users':
                $this->collectUsers($tableName);
                break;
            case 'aauth_groups':
                $this->collectGroups($tableName);
                break;
            case 'aauth_user_to_group':
                $this->collectUserGroups($tableName);
                break;
            case 'appointments':
                $this->collectAppointments($tableName);
                break;
            case 'doctors':
                $this->collectDoctors($tableName);
                break;
            case 'patients':
                $this->collectPatients($tableName);
                break;
            case 'services':
                $this->collectServices($tableName);
                break;
            case 'transactions':
                $this->collectTransaction($tableName);
                break;
            case 'treatments':
                $this->collectTreatments($tableName);
                break;
			case 'activity_logs':
                $this->collectSimple($tableName);
                break;
			case 'diagnosis':
                $this->collectSimple($tableName);
                break;
			case 'dic_eng':
                $this->collectSimple($tableName);
                break;
			case 'images':
                $this->collectSimple($tableName);
                break;
			case 'query_logging_table':
                $this->collectSimple($tableName);
                break;
				
            default:
                break;
        }
    }

	protected function collectSimple($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from($tableName)->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update($tableName, $pushData);
            }else{
                $insertQ = $this->db->insert_string($tableName,$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

	
    protected function collectTreatments($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from('treatments')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update('treatments', $pushData);
            }else{
                $insertQ = $this->db->insert_string('treatments',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectTransaction($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            $pushData['units'] = $pushData['units'] == NULL ? 1 : $pushData['units'];
            if($this->db->from('transactions')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update('transactions', $pushData);
            }else{
                $insertQ = $this->db->insert_string('transactions',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectServices($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from('services')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update('services', $pushData);
            }else{
                $insertQ = $this->db->insert_string('services',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectPatients($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from('patients')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update('patients', $pushData);
            }else{
                $insertQ = $this->db->insert_string('patients',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectDoctors($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();

        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            $arrayToUpdate = [
                'feild' => $pushData['feild'],
                'degree' => $pushData['degree'],
                'charges' => $pushData['charges'],
                'opd_percent' => $pushData['opd_percent'],
                'salery_type' => 2,
                'salery_amount' => 0
            ];
            $Resp = $this->db->where(['id' => $pushData['user_id']])->update('aauth_users',$arrayToUpdate);

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectAppointments($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();


        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            $insertQ = $this->db->insert_string('appointments',$pushData);
            $Resp = $this->db->query($insertQ);


            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectUserGroups($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();


        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            $insertQ = $this->db->insert_string('aauth_user_to_group',$pushData);
            $Resp = $this->db->query($insertQ);


            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectGroups($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from('aauth_groups')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $updateArray = [
                    'name' => $pushData['name'],
                    'definition' => $pushData['definition']
                ];
                $Resp = $this->db->where(['id' => $pushData['id']])->update('aauth_groups', $updateArray);

            }else{
                $insertQ = $this->db->insert_string('aauth_groups',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function collectUsers($tableName){
        $this->setActivity('Collecting Data From old table');
        $collectedArray = $this->_oldDb->from($tableName)->get()->result_array();
        $this->db->trans_start();
        foreach ($collectedArray as $pushData){

            if($this->db->from('aauth_users')->where(['id' => $pushData['id']])->get()->num_rows() > 0) {
                $Resp = $this->db->where(['id' => $pushData['id']])->update('aauth_users', $pushData);
            }else{
                $insertQ = $this->db->insert_string('aauth_users',$pushData);
                $Resp = $this->db->query($insertQ);
            }

            if($Resp){
                $this->setActivity('Record Pushed');
            }else{
                $this->setActivity('Record Not Pushed');

            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->setActivity('Rolling Back Table');
            $this->db->trans_rollback();
            $this->KillProcess();

        }else
        {
            $this->setActivity('Committing Table Record');
            $this->db->trans_commit();
            $this->setActivity('Data Committed');
        }
    }

    protected function KillProcess(){
        $this->_respCode = 500;
        $this->_respMessage = 'Failed';
        $this->showResp();

    }

    protected function showSuccess(){
        $this->_respCode = 200;
        $this->_respMessage = 'Success';
        $this->showResp();
    }

    protected function setActivity($msg,$level = "LOG"){
        $this->_activities[] = [$msg,$level];
    }

    protected function getTablesToImport(){
        return [
            'aauth_users','aauth_groups','aauth_user_to_group','appointments','doctors','patients','services','transactions','treatments', 'activity_logs','diagnosis','dic_eng','images','query_logging_table'
        ];
    }

    protected function showResp(){
        echo json_encode([
            'code' => $this->_respCode,
            'msg' => $this->_respMessage,
            'logs' => $this->_activities
        ]);
        die;
    }
}