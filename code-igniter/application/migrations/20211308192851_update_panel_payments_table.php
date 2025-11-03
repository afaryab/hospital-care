<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_panel_payments_table extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `panel_payments` ADD ( `amount_recieved` INT(11) NULL DEFAULT 0, `payment_reference` VARCHAR(255) NULL DEFAULT NULL)");

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
