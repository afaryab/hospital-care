<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_update_recipet_elements_table extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        $this->db->simple_query("ALTER TABLE `reception_counters_closings_transaction_elements` ADD ( `serial_number_doctor` INT(11) NULL DEFAULT NULL, `serial_number_service` INT(11) NULL DEFAULT NULL, `serial_number_doctor_nd_service` INT(11) NULL DEFAULT NULL)");

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
