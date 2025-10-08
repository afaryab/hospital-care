<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_sync_log extends CI_Migration
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
            'logged_at' => array(
                'type' => 'DATETIME',
            ),
            'send_data' => array(
                'type' => 'TEXT',
            ),
            'response' => array(
                'type' => 'TEXT',
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'table_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table sync_log
        $this->dbforge->create_table("sync_log", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table sync_log
        $this->dbforge->drop_table("sync_log", TRUE);
    }

}
