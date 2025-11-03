<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_xray_patients extends CI_Migration
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
            'site_patient_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'last_visit' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '24',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table in_patients
        $this->dbforge->create_table("xray_patients", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table in_patients
        $this->dbforge->drop_table("xray_patients", TRUE);
    }

}
