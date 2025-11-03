<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_images extends CI_Migration
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

            'path' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'owner_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false]
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));



        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table images
        $this->dbforge->create_table("images", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table images
        $this->dbforge->drop_table("images", TRUE);
    }

}
