<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_emails extends CI_Migration
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
            'recipient' => array(
                'type' => 'TEXT',
            ),
            'sender' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'subject' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'final_body' => array(
                'type' => 'TEXT',
            ),
            'header' => array(
                'type' => 'TEXT',
            ),
            'data' => array(
                'type' => 'TEXT',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
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
        $this->dbforge->add_key("id");

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table emails
        $this->dbforge->create_table("emails", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table emails
        $this->dbforge->drop_table("emails", TRUE);
    }

}
