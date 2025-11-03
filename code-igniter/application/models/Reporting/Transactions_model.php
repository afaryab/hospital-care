<?php

class transactions_model extends MY_Model
{
    public function __construct(){
        parent::__construct();
        $this->setTableName('opd_transactions');
    }

    private $tableName;

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTreatmentByPId($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('patient_id',$id)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function getTodayTransactions(){
        $query = $this->db->select('*')->from($this->getTableName())->where('created_on > DATE_SUB(NOW(), INTERVAL 1 DAY)')->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function getTreatmentByIds($id){
        $query = $this->db->select('*')->from($this->getTableName())->where_in('id',$id)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return (array) $query->result_array();
        }else{
            return [];
        }
    }

    public function getTreatmentById($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('id',$id)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return (array) $query->row();
        }else{
            return [];
        }
    }
    public function getTransactionsByUserID($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('receaved_by',$id)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }
    public function getUnClosedTransactionsByUserID($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('receaved_by',$id)->where('submitted_for_accounts',0)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }
    public function getClosedTransactionsByUserID($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('receaved_by',$id)->where('submitted_for_accounts',1)->where('cleared_by_accounts',0)->order_by('created_on','DESC')->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function insertNew($array){
        if($return  = $this->db->insert($this->getTableName(),$array)){
            $array['id'] = $this->db->insert_id();
            $array['created_on'] = date('Y-m-d H:i:s',strtotime("now"));
            $queryString = $this->db->insert_string($this->getTableName(), $array);
            $this->logAction('INSERT',$queryString,$array,$this->getTableName());
            return $array['id'];
        }else{
            return false;
        }
    }

    public function submitForAccounts($ids){
        $array = ['submitted_for_accounts'=>1,'submitted_for_accounts_on'=>date('Y-m-d H:i:s',strtotime("now"))];
        if($return  = $this->db->where_in('id',$ids)->update($this->getTableName(),$array)) {
            $queryString = $this->db->last_query();
            foreach ($ids as $id) {
                $this->logAction('UPDATE', $queryString, $array, $this->getTableName(), $id);
            }
            return $return;
        }else{
            return false;
        }
    }

    public function accountsClearence($array,$id){
        $intArray = array();
        foreach($array as $key=>$val){
            $intArray[] = (int) $val;
        }
        $toUpdate = ['cleared_by_accounts'=>1,'cleared_by_accounts_on'=>'now()','cleared_by_accounts_by'=>$id];
        if($return  = $this->db->where_in('id',$intArray)->update($this->getTableName(),$toUpdate)){
            $queryString = $this->db->last_query();
            foreach ($intArray as $key=>$val){
                $this->logAction('UPDATE', $queryString, $toUpdate, $this->getTableName(), $val);
            }
            return $return;
        }else{
            return false;
        }
    }

    public function getUserTransactionsWithFilters($userId, $submited_for_closing = 0, $reviewd = 0, $date = array()){
        $this->db->select('*')->from($this->getTableName())->where('receaved_by',$userId)->where('submitted_for_accounts',$submited_for_closing)->where('cleared_by_accounts',$reviewd);
        if(!empty($date)){
            $this->db->where('(( date(created_on) >= date("'.$date['start'].'") AND date(created_on) <= date("'.$date['end'].'")) OR (date(created_on) = date("'.$date['start'].'") OR date(created_on) = date("'.$date['end'].'")))');
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }
    public function getTransactionByDoc($doctorId, $date = array()){
        $this->db->select('*')->from($this->getTableName())->where('doctor_id',$doctorId);
        if(!empty($date)){
            $this->db->where('(( date(created_on) >= date("'.$date['start'].'") AND date(created_on) <= date("'.$date['end'].'")) OR (date(created_on) = date("'.$date['start'].'") OR date(created_on) = date("'.$date['end'].'")))');
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

    public function getTransactionsByServices($serviceId, $date = array()){
        $this->db->select('*')->from($this->getTableName())->where('service_id',(int) $serviceId);
        if(!empty($date)){
            $this->db->where('(( date(created_on) >= date("'.$date['start'].'") AND date(created_on) <= date("'.$date['end'].'")) OR (date(created_on) = date("'.$date['start'].'") OR date(created_on) = date("'.$date['end'].'")))');
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

    public function getUserTransactionsByDoctor($userId, $submited_for_closing = 0, $reviewd = 0, $date = array()){
        $this->db->select('*')->from($this->getTableName())->where('receaved_by',$userId)->where('submitted_for_accounts',$submited_for_closing)->where('cleared_by_accounts',$reviewd);
        if(!empty($date)){
            $this->db->where('(( date(created_on) >= date("'.$date['start'].'") AND date(created_on) <= date("'.$date['end'].'")) OR (date(created_on) = date("'.$date['start'].'") OR date(created_on) = date("'.$date['end'].'")))');
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

    public function changeAmount($id,$array){
        if($return  = $this->db->where('id',$id)->update($this->getTableName(),$array)){
            $queryString = $this->db->last_query();
            $this->logAction('UPDATE', $queryString, $array, $this->getTableName(), $id);
            return $return;
        }else{
            return false;
        }
    }

    public function insertNewBulk($array){
        $this->db->insert_batch($this->getTableName(),$array);
        $queryString = $this->db->last_query();
        foreach ($array as $arr){
            $this->logAction('INSERT', $queryString, $arr, $this->getTableName());
        }
    }

    public function getLastNDaysActivity($startDate,$EndDate){
        $this->db->select('created_on, SUM(amount_in_num) AS amount', FALSE);
        $this->db->where('date(created_on) BETWEEN "'.$startDate .'" AND "'.$EndDate.'"');
        $this->db->group_by("date(created_on)");
        $this->db->from($this->getTableName());
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

    public function getPaymentGroup(){
        $query = $this->db->select('patient_id as patient, SUM(amount_in_num) as amount')->from($this->getTableName())->group_by("patient_id")->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }
}
