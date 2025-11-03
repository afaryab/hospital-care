<?php

/**
 * Class patients_model
 * Responsibility: Patients Records
 * Created by PhpStorm.
 * User: Ahmad Faryab Kokab
 * Date: 9/7/2017
 * Time: 10:17 AM
 */
class patients_model extends MY_Model {

    public function __construct(){
        parent::__construct();
        $this->setTableName('patients');
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

    /**\
     * @param $array
     * @return bool | integer
     */
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

    /**
     * @param $id integer
     * @return array
     */

    public function getPatientById($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('id',$id)->get();
        if($query->num_rows() > 0){
            return (array) $query->row();
        }else{
            return [];
        }
    }

    /**
     * @param $id
     * @param $array
     * @return bool
     */

    public function updateRecord($id,$array){
        if($return  = $this->db->where('id',$id)->update($this->getTableName(),$array)){
            $queryString = $this->db->last_query();
            $this->logAction('UPDATE',$queryString,$array,$this->getTableName(),$id);
            return $return;
        }else{
            return false;
        }
    }

    /**
     * @param $ids string comma separated int
     * @return array
     */

    public function getPatientByIds($ids){
        $query = $this->db->select('*')->from($this->getTableName())->where_in('id',$ids)->get();
       
        if($query->num_rows() > 0){
            return (array) $query->result_array();
        }else{
            return [];
        }
    }

}
