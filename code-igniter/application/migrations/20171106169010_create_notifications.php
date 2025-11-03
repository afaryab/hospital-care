<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_notifications extends CI_Migration
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
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
			'content' => array(
                'type' => 'TEXT',
            ),
			'created_on' => array(
                'type' => 'DATETIME',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
			'pulled_on' => array(
                'type' => 'TIMESTAMP',
                'default' => NULL,
            ),
			
			'till' => array(
               'type' => 'DATETIME',
               'default' => NULL,
            ),		
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table notifications
        $this->dbforge->create_table("notifications", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table notifications
        $this->dbforge->drop_table("notifications", TRUE);
    }

}
