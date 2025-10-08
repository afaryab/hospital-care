<?php
class services_model extends MY_Model
{
    public function __construct(){
        parent::__construct();
        $this->setTableName('opd_services');
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



    public function new_service($array){
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

    public function getAllServices(){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

    public function getServiceById($id){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->where('id',$id)->get();
        if($query->num_rows() > 0){
            return (array) $query->row();
        }else{
            return [];
        }
    }

    public function getServiceByIds($ids){
        $query = $this->db->select('*')->from($this->getTableName())->where('is_deleted',0)->where_in('id',$ids)->get();
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
    }

}