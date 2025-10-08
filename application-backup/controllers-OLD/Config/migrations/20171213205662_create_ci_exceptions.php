<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_ci_exceptions extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->dbforge->add_field('id');
        $this->dbforge->add_field(array(
            'file' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'line' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'message' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'method' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => TRUE,
            ),
            'get' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'post' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'files' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'is_ajax' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'is_cli' => array(
                'type' => 'TINYINT',
                'constraint' => '4',
                'null' => TRUE,
            ),
            'user_agent' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'session_data' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'stack_trace' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'sql_query' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'sql_error' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
                'null' => TRUE,
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table ci_exceptions
        $this->dbforge->create_table("ci_exceptions", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table ci_exceptions
        $this->dbforge->drop_table("ci_exceptions", TRUE);
    }

}
