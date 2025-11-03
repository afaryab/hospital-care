<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_reception_closings extends CI_Migration
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
            'date_record' => array(
                'type' => 'DATETIME',
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'transactions_data' => array(
                'type' => 'TEXT',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table reception_closings
        $this->dbforge->create_table("reception_closings", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table reception_closings
        $this->dbforge->drop_table("reception_closings", TRUE);
    }

}
