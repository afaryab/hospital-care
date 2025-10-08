<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_test_services extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        

        // Add Primary Key.

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
            'shrt_code' => array(
                'type' => 'VARCHAR',
                'constraint' => '8',
            ),
            'charges' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'charges_including_tax' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '1'
            ),
            'tax_rate' => array(
                'type' => 'INT',
                'constraint' => '3',
                'default' => '0'
            ),
            'sample' => array(
                'type' => 'VARCHAR',
                'constraint' => '42',
            ),
            'reporting_time' => array(
                'type' => 'VARCHAR',
                'constraint' => '30',
				'null' => TRUE,
            ),
            'is_multiple' => array(
                'type' => 'INT',
                'constraint' => '1',
				'default' => '0',
            ),
			'entered_by' => array(
                'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
            'is_deleted' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
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

        $this->dbforge->add_key('id', TRUE);
        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table tests
        $this->dbforge->create_table("test_services", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table tests
        $this->dbforge->drop_table("test_services", TRUE);
    }

}
