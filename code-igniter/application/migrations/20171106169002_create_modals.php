<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_modals extends CI_Migration
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
			'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
			'content' => array(
                'type' => 'TEXT',
            ),
			'is_active' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
			'show_on_every_load' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
			'pulled_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
			'created_on' => array(
                'type' => 'DATETIME',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table modals
        $this->dbforge->create_table("modals", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table modals
        $this->dbforge->drop_table("modals", TRUE);
    }

}
