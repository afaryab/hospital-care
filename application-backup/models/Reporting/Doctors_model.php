<?php
class doctors_model extends MY_Model
{
    public function __construct(){
        parent::__construct();
        $this->setTableName('doctors');
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



    public function new_doctor($array){
        if($return  = $this->db->insert($this->getTableName(),$array)){
            $array['id'] = $this->db->insert_id();
            $array['created_on'] = strtotime("now");
            $queryString = $this->db->insert_string($this->getTableName(), $array);
            $this->logAction('INSERT',$queryString,$array,$this->getTableName());
            return $return;
        }else{
            return false;
        }
    }

    public function getAllDoctors(){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function getDoctorById($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->where('id',$id)->get();
        if($query->num_rows() > 0){
            return (array) $query->row();
        }else{
            return [];
        }
    }
    public function getDoctorByUserId($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->where('user_id',$id)->get();
        if($query->num_rows() > 0){
            return (array) $query->row();
        }else{
            return [];
        }
    }

    public function getDocByIds($ids){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->where_in('id',$ids)->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    function updateDoctor($id,$array){
        if($return  = $this->db->where('id',$id)->update($this->getTableName(),$array)){
            $queryString = $this->db->last_query();
            $this->logAction('UPDATE',$queryString,$array,$this->getTableName(),$id);
            return $return;
        }else{
            return false;
        }
    }

}