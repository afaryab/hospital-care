<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_page_options extends CI_Migration
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
			 'key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
			 'value' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
			'is_public' => array(
                'type' => 'INT',
                'constraint' => '11',
                'comment' => '1 text 2 number 3 dropzone',
            ),
			'maximum_limit' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '2',
            ),
			'minimum_limit' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '255',
            ),
			'description' => array(
                'type' => 'TEXT',
            ),
			'editable' => array(
                'type' => 'INT',
				'constraint' => '1',
                'default' => '1',
            ),	
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table page_options
        $this->dbforge->create_table("page_options", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table page_options
        $this->dbforge->drop_table("page_options", TRUE);
    }

}
