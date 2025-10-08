<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_activity_logs extends CI_Migration
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
            'message' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
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


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table activity_logs
        $this->dbforge->create_table("activity_logs", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table activity_logs
        $this->dbforge->drop_table("activity_logs", TRUE);
    }

}
