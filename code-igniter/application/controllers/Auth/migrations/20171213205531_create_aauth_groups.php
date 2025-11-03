<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_aauth_groups extends CI_Migration
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
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'department' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'definition' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'url' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
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

        // Create Table aauth_groups
        $this->dbforge->create_table("aauth_groups", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table aauth_groups
        $this->dbforge->drop_table("aauth_groups", TRUE);
    }

}
