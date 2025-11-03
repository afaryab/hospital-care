<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_aauth_system_variables extends CI_Migration
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
            'data_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'value' => array(
                'type' => 'TEXT',
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

        // Create Table aauth_system_variables
        $this->dbforge->create_table("aauth_system_variables", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table aauth_system_variables
        $this->dbforge->drop_table("aauth_system_variables", TRUE);
    }

}
