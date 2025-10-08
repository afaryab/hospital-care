<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_test_mesurements extends CI_Migration
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
            'test_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'unit' => array(
                'type' => 'VARCHAR',
                'constraint' => '5',
            ),
            'normal_value' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table tests
        $this->dbforge->create_table("test_mesurements", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table tests
        $this->dbforge->drop_table("test_mesurements", TRUE);
    }

}
