<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_inp_transactions extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `inpatient_transactions` ADD ( `edited_amount` INT(11) NULL DEFAULT NULL)");
        $this->db->simple_query("ALTER TABLE `inpatient_file` ADD ( `edited_amount` INT(11) NULL DEFAULT NULL)");

        // Add Fields.
    
    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table test_patients
        
    }

}
//
