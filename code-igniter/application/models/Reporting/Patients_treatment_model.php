<?php

class Patients_treatment_model extends MY_Model
{
    public function __construct(){
        parent::__construct();
        $this->setTableName('opd_treatments');
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

    public function getTreatmentsByDate($date){
        $query = $this->db->select('*')->from($this->getTableName());
        if(is_array($date)){
            $this->db->where('(( date(created_on) >= date("'.$date['start'].'") AND date(created_on) <= date("'.$date['end'].'")) OR (date(created_on) = date("'.$date['start'].'") OR date(created_on) = date("'.$date['end'].'")))');
        }
        if(is_object($date)){
            $this->db->where('(( date(created_on) >= date("'.$date->start.'") AND date(created_on) <= date("'.$date->end.'")) OR (date(created_on) = date("'.$date->start.'") OR date(created_on) = date("'.$date->end.'")))');
        }
        if(is_string($date)){
            $this->db->where('date(created_on) = date('.$date.')');
        }
        $query = $this->db->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function insertNew($array){
        if($return  = $this->db->insert($this->getTableName(),$array)){
            $array['id'] = $this->db->insert_id();
            $array['created_on'] = strtotime("now");
            $queryString = $this->db->insert_string($this->getTableName(), $array);
            $this->logAction('INSERT',$queryString,$array,$this->getTableName());
            return $array['id'];
        }else{
            return false;
        }
    }

    public function updateTreatment($id ,$array){
        if($return  = $this->db->where('id',$id)->update($this->getTableName(),$array)){
            $queryString = $this->db->last_query();
            $this->logAction('UPDATE',$queryString,$array,$this->getTableName(),$id);
            return $return;
        }else{
            return false;
        }
    }

    public function getPaymentGroup(){
        $query = $this->db->select('patient_id as patient, SUM(treatment_charges) as amount')->from($this->getTableName())->group_by("patient_id")->get();
        if($query->num_rows() > 0){
            $dataArray =  $query->result_array();
            return $dataArray;
        }else{
            return [];
        }
    }

}