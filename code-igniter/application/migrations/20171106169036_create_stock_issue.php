<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_stock_issue extends CI_Migration
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
			'stock_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
			'issue_to' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'stock' => array(
                'type' => 'INT',
                'constraint' => '11',		
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table stock_issue
        $this->dbforge->create_table("stock_issue", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table stock_issue
        $this->dbforge->drop_table("stock_issue", TRUE);
    }

}
