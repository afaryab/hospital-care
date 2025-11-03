<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_inpd_file_discharge extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `inpatient_file` CHANGE `closed_on` `closed_on` DATETIME NULL DEFAULT NULL");

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
