<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_appointments extends CI_Migration
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
            'doctor_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'treatment_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '11'
            ),
            'appointment_notes' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'start_date' => array(
                'type' => 'DATETIME',
            ),
            'end_date' => array(
                'type' => 'DATETIME',
            ),
            'patient_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'opd_patient_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'entered_by' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '2',
                'comment' => ''
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table appointments
        $this->dbforge->create_table("appointments", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table appointments
        $this->dbforge->drop_table("appointments", TRUE);
    }

}
