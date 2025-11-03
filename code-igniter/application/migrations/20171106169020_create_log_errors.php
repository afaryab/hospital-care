<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_log_errors extends CI_Migration
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
			'status_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
				'null' => TRUE,
            ),
			'status' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '01',
            ),
			'logged_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
			'file_path' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
				'null' => TRUE,
            ),
			'line_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
				'null' => TRUE,
            ),
			'request_array' => array(
                'type' => 'TEXT',
            ),
			'post_array' => array(
                'type' => 'TEXT',
            ),
			'get_array' => array(
               'type' => 'TEXT',
            ),
			'server_array' => array(
               'type' => 'TEXT',
            ),
			'error' => array(
               'type' => 'TEXT',
            ),
			'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
			 'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table log_errors
        $this->dbforge->create_table("log_errors", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table log_errors
        $this->dbforge->drop_table("log_errors", TRUE);
    }

}
