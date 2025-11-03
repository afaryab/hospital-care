<?php

class Cron extends CI_Controller
{
    function __construct(){
        parent::__construct();
    }

    function test(){
        log_message('success', "Cron is running check at " . date('d-m-y'));

    }

    public function index(){
        $this->input->is_cli_request() 
            or exit("Execute via command line");
    }

    public function followup(){
        $this->input->is_cli_request() 
           or exit("Execute via command line");
           
                $assigned_to=1;
               
                $results  = $this->db->query("SELECT * FROM patients WHERE last_visit is null or ABS (last_visit - NOW()) > 0")->result();
                
               
                foreach($results as $row)
                {
                   
                    $arraytodb=[
                        'assigned_to'=> $assigned_to,
                        'patient_id' => $row->id,
                        'patient_name' => $row->pateint_name,
                        'status'=>"OPEN",
                        'last_visit'=>$row->last_visit,
                        'next_call_time' => date("d/m/y h:i:s")

                    ];
                    
                    $this->load->model('commonModel', 'marketing_followup');
                    $this->marketing_followup->setTableName('marketing_patients_followup');
                    $followup = $this->marketing_followup->findOneBy(['patient_id' => $row->id]);

                    if(array_key_exists('id',$followup))
                    {
                        $this->marketing_followup->updateRecord($followup['id'], ['assigned_to' => $assigned_to]);
                    }else{
                        $this->db->insert('marketing_patients_followup', $arraytodb);
                    }

                    $this->load->model('commonModel', 'notifications');
                    $this->notifications->setTableName('notifications');
                    $this->notifications->addNew([
                           'content'=>"Please followup patient \""."<a href=''>".$row->pateint_name."</a> last visit was on ".$row->last_visit,
                           'user_id'=>$assigned_to,
                    ]);

                    

                }
                log_message('success', "SetFollowUP | SetFollowUP Follow up scanned at " . date('d-m-y'));
                

                

    }

    
}