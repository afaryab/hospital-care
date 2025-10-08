<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_aauth_users_table_ultrasound extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `aauth_users` ADD ( `is_ultrasound_doc` INT(1) NULL DEFAULT 0)");

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
