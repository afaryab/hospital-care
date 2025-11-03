<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_marketing_followup extends CI_Migration
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
            'assigned_to' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
			'patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'patient_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
				'null' => TRUE,
				
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '10',
				'null' => TRUE,
            ),  
            'next_call_time' => array(
                'type' => 'DATETIME',
				'null' => TRUE,
				
            ),        
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'last_visit' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table test_patients
        $this->dbforge->create_table("marketing_patients_followup", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table test_patients
        $this->dbforge->drop_table("test_patients", TRUE);
    }

}
