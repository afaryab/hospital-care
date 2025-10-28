<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Faryab Kokab
 * Date: 7/22/2017
 * Time: 12:38 AM
 */

class commonModel extends MY_Model
{

    function __construct()
    {
        parent::__construct();
        $now = new DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        $offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

//Your DB Connection - sample
        $this->db->query("SET time_zone='$offset'");
    }

    protected $_tableName = '';

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    function addNew($array){
        if(
            array_key_exists('name',$array) &&
            !is_string($array['name'])
        ){
            $array['name'] = implode(',',$array['name']);
        }
        if(
            array_key_exists('error',$array) &&
            !is_string($array['error'])
        ){
            $array['error'] = serialize($array['error']);
        }

        $this->db->insert($this->_tableName,$array);
        $queryString = $this->db->insert_string($this->getTableName(), $array);
        $lastId = $this->db->insert_id();;
        $this->logAction('INSERT',$queryString,$array,$this->getTableName());
        return $lastId;
    }

    function updateRecord($id,$array){
        if(!empty($id)) {
			
            if (is_string($id) || is_int($id)) {
                $return = $this->db->where(
                    [
                        'id' => $id
                    ]
                )->update($this->_tableName, $array);
				
				
            } else {
                $return = $this->db->where_in(
                    [
                        'id' => $id
                    ]
                )->update($this->_tableName, $array);
				
            }

            $queryString = $this->db->last_query();

            $this->logAction('UPDATE', $queryString, $array, $this->getTableName(), $id);
            return $return;
        }else{
            return false;
        }

        return $return;
    }

    function updatePageOptionBy($key,$val,$array){
        $toUpdate = $this->db->where(
            [
                $key=>$val
            ]
        )->get($this->_tableName)->result_array();
        if(!empty($toUpdate)) {
            $return = $this->db->where(
                [
                    $key => $val
                ]
            )->update($this->_tableName, $array);

            $queryString = $this->db->last_query();
            $this->logAction('UPDATE', $queryString, $array, $this->getTableName(), $toUpdate[0]['id']);
            return $return;
        }else{
            $insertArray = [
                'name' => $val,
                'key' => $val,
                'value' => $array['value'],
                'is_public' => 0,
                'type' => 1,
                'maximum_limit' => 255,
                'minimum_limit' => 2,
                'editable' => 0
            ];
            return $this->addNew($insertArray);
        }
    }

    function getAll(){
        $query = $this->db->select('*')->from($this->_tableName)->get();
        
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }
    function getAllByGrp($groupBy){
        $query = $this->db->select('*')->from($this->_tableName)->group_by($groupBy)->get();

        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    function findBy($incomingCondition,$limit = []){

        $this->db->select('*')->from($this->_tableName);

        if(is_array($incomingCondition)) {

            $array = $incomingCondition;
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }elseif(is_string($incomingCondition)){

            $this->db->where($incomingCondition);
        }
        if(!empty($limit)){
            $this->db->limit($limit[0],$limit[1]);
        }
        
        $query = $this->db->get();
        
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }
    function findOneBy($incomingCondition){

        $this->db->select('*')->from($this->_tableName);

        if(is_array($incomingCondition)) {

            $array = $incomingCondition;
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }elseif(is_string($incomingCondition)){

            $this->db->where($incomingCondition);
        }
        
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $return = $query->result_array();
            return $return[0];
        }else{
            return [];
        }
    }

    function findByAndLimit($incomingCondition,$limit){

        $this->db->select('*')->from($this->_tableName);

        if(is_array($incomingCondition)) {

            $array = $incomingCondition;
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }elseif(is_string($incomingCondition)){

            $this->db->where($incomingCondition);
        }

        $query = $this->db->limit($limit)->get();

        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    function getLastNDaysActivityForActivity($startDate,$EndDate){
        $this->db->select('created_on, COUNT(id) AS count', FALSE);
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

    function getLastNDaysActivityForBugs($startDate,$EndDate){
        $this->db->select('created_on, COUNT(id) AS count', FALSE);
        $this->db->where('date(created_on) BETWEEN "'.$startDate .'" AND "'.$EndDate.'"');
//        $this->db->where('status = 1');
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

    function getCountIndex($dateColum = 'created_on',$countee = 'id'){
        $this->db->select('COUNT('.$countee.') AS count', FALSE);
        $this->db->where('date('.$dateColum.') = CURDATE()');
        $this->db->group_by("date('.$dateColum.')");
        $this->db->from($this->getTableName());
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray[0]['count'];
        }else{
            return 0;
        }
    }
    function getSumIndex($dateColum = 'created_on',$sumFeild = 'amount'){
        $this->db->select('SUM('.$sumFeild.') AS count', FALSE);
        $this->db->where('date('.$dateColum.') = CURDATE()');
        $this->db->group_by("date('.$dateColum.')");
        $this->db->from($this->getTableName());
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray[0]['count'];
        }else{
            return 0;
        }
    }
    function getSumByConditions($sumFeild ,$incomingCondition){
        $this->db->select('SUM('.$sumFeild.') AS count', FALSE);
        if(is_array($incomingCondition)) {

            $array = $incomingCondition;
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }elseif(is_string($incomingCondition)){

            $this->db->where($incomingCondition);
        }
        $this->db->from($this->getTableName());
        $query = $this->db->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray[0]['count'];
        }else{
            return 0;
        }
    }

    public function getAppointmentsByIdNDate($id,$date,$limit = []){
        $query = $this->db->select('*')->from($this->getTableName());

        if($id != 0){
            $query->where('doctor_id',$id);
        }

        if(!empty($date)){
            $this->db->where('(( date(start_date) >= date("'.$date['start'].'") AND date(start_date) <= date("'.$date['end'].'")) OR (date(start_date) = date("'.$date['start'].'") OR date(start_date) = date("'.$date['end'].'")))');
            $this->db->where('(( date(end_date) >= date("'.$date['start'].'") AND date(end_date) <= date("'.$date['end'].'")) OR (date(end_date) = date("'.$date['start'].'") OR date(end_date) = date("'.$date['end'].'")))');
        }
        if(!empty($limit)){
            $this->db->limit($limit[0],$limit[1]);
        }
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }

    }

    function getSumByGroup($key){
        $query = $this->db->select("SUM(id) as sum , $key")->from($this->getTableName());

        $this->db->group_by($key);

        if(!empty($date)){
            $this->db->where('(( date(start_date) >= date("'.$date['start'].'") AND date(start_date) <= date("'.$date['end'].'")) OR (date(start_date) = date("'.$date['start'].'") OR date(start_date) = date("'.$date['end'].'")))');
            $this->db->where('(( date(end_date) >= date("'.$date['start'].'") AND date(end_date) <= date("'.$date['end'].'")) OR (date(end_date) = date("'.$date['start'].'") OR date(end_date) = date("'.$date['end'].'")))');
        }
        $query = $this->db->get();

        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

    function countBy($incomingCondition){

        $this->db->select('COUNT(id) AS count', FALSE)->from($this->_tableName);

        if(is_array($incomingCondition)) {

            $array = $incomingCondition;
            foreach ($array as $key => $value) {
                if (is_object($value)) {
                    $value = (array)$value;
                }
                if (is_array($value)) {
                    $this->db->where_in($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }elseif(is_string($incomingCondition)){

            $this->db->where($incomingCondition);
        }

        $query = $this->db->get();

        if($query->num_rows() > 0){
            $return = $query->result_array();
            return $return[0]['count'];
        }else{
            return 0;
        }
    }

}