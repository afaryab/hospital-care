<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_followup_comments extends CI_Migration
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
			'followup_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'comments' => array(
                'type' => 'TEXT',
				'null' => TRUE,
            ), 
            'time_to_call' => array(
                'type' => 'DATETIME',
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
        $this->dbforge->create_table("followup_comments", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table test_patients
        $this->dbforge->drop_table("followup_comments", TRUE);
    }

}
