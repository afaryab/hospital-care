<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_ps_number_inpatient extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
            'patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'inpatient_patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'ps_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ps_numbers
        $this->dbforge->create_table("inpatient_ps_numbers", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table treatments
        $this->dbforge->drop_table("inpatient_ps_numbers", TRUE);
    }

}
