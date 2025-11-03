<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_panel_companies_bills extends CI_Migration
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
            'panel_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'inpatient_file_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'bill_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '21',
                'default' => 'UN-PAID'
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
        $this->dbforge->create_table("panel_companies", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table services
        $this->dbforge->drop_table("panel_companies", TRUE);
    }

}
