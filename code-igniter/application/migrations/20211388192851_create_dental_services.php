<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_dental_services extends CI_Migration
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
            'post_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'is_doctor_selectable' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_multiple' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_quantityable' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'fix_amount' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'entered_by' => array(
                'type' => 'INT',
                'constraint' => '11',
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
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table services
        $this->dbforge->create_table("dental_services", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table services
        $this->dbforge->drop_table("dental_services", TRUE);
    }

}
