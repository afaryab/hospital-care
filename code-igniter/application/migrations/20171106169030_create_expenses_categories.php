<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_expenses_categories extends CI_Migration
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
            'created_on' => array(
                'type' => 'DATETIME',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table expenses_categories
        $this->dbforge->create_table("expenses_categories", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table expenses_categories
        $this->dbforge->drop_table("expenses_categories", TRUE);
    }

}
