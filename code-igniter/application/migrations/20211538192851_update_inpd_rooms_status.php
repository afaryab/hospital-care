<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_inpd_rooms_status extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `inpd_rooms` ADD ( `is_allotted` INT(11) NULL DEFAULT 0)");
       

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
