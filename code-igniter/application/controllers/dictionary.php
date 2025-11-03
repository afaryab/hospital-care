<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Faryab Kokab
 * Date: 9/7/2017
 * Time: 2:40 PM
 */

class dictionary extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
    }
    function english(){
        $q = $_GET['q'];
        $resultArray = [];
        if(strlen($q) >= 1) {

            $query = $this->db->select('word')->from('dic_eng')->like('word',$q , 'after')->where('word != ',$q)->limit(10)->get();
            if($query->num_rows() > 0) {
                foreach ($query->result() as $res) {
                    $resultArray[] = ['name' => $res->word];
                }
            }
        }
        echo json_encode($resultArray);
    }
    function markeng(){
        $q = $_GET['q'];
        $this->db->where(
            [
                'word'=>$q
            ]
        )->update('dic_eng',['used'=>'used + 1']);
    }

}