<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_stock extends CI_Migration
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
            'stock' => array(
                'type' => 'INT',
                'constraint' => '11',
				'default' => '0',
				
            ),
			 'min_stock' => array(
                'type' => 'INT',
                'constraint' => '11',
				'default' => '2',
				
            ),
			 'critical_stock' => array(
                'type' => 'INT',
                'constraint' => '11',
				'default' => '2',
				
            ),
            'suplier_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),          
            'suplier_contact' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table stock
        $this->dbforge->create_table("stock", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table stock
        $this->dbforge->drop_table("stock", TRUE);
    }

}
