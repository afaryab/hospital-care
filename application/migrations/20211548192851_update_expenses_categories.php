<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_expenses_categories extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `expenses_categories` ADD ( `type` VARCHAR(255) NULL DEFAULT NULL)");
        $this->db->simple_query("ALTER TABLE `expenses_categories` ADD ( `is_deleted` INT(11) NULL DEFAULT 0)");


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
