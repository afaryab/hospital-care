<?php
/**
 * Created by PhpStorm.
 * User: Ahmad Faryab Kokab
 * Date: 9/7/2017
 * Time: 10:17 AM
 */

class MY_Model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param string $operation INSERT | UPDATE | DEELETE
     * @param string $query Query String Executed
     * @param array $data DATA To Be Effected
     * @param string $target Target Table
     * @param $updateId integer | null Default Null Required in case of update
     */

    function logAction($operation,$query,$data,$target,$updateId = NULL){
        if($updateId) {
            if (is_string($updateId) || is_int($updateId)) {
                $queryLogingArray = [
                    'operation' => $operation,
                    'query_string' => $query,
                    'target_table' => $target,
                    'data' => serialize($data),
                    'rec_id' => $updateId,
                    'is_synced' => 0
                ];
                $this->db->insert(QUERY_LOGGING_TABLE, $queryLogingArray);
            } else {
                foreach ($updateId as $id) {
                    $queryLogingArray = [
                        'operation' => $operation,
                        'query_string' => $query,
                        'target_table' => $target,
                        'data' => serialize($data),
                        'rec_id' => $id,
                        'is_synced' => 0
                    ];
                }
                $this->db->insert(QUERY_LOGGING_TABLE, $queryLogingArray);
            }
        }else{
            $queryLogingArray = [
                'operation' => $operation,
                'query_string' => $query,
                'target_table' => $target,
                'data' => serialize($data),
                'rec_id' => $updateId,
                'is_synced' => 0
            ];
            $this->db->insert(QUERY_LOGGING_TABLE, $queryLogingArray);
        }

    }

}