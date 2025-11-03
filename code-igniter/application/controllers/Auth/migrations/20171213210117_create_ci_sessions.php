<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_ci_sessions extends CI_Migration
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
                'type' => 'VARCHAR',
                'constraint' => '128',
            ),
            'ip_address' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
            ),
            'timestamp' => array(
                'type' => 'INT',
                'constraint' => '10',
                'unsigned' => TRUE,
                'default' => '0',
            ),
            'data' => array(
                'type' => 'BLOB',
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

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table ci_sessions
        $this->dbforge->create_table("ci_sessions", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table ci_sessions
        $this->dbforge->drop_table("ci_sessions", TRUE);
    }

}
