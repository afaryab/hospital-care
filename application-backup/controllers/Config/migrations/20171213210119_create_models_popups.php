<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_models_popups extends CI_Migration
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
            'title' => array(
                'type' => 'TEXT',
            ),
            'body' => array(
                'type' => 'TEXT',
            ),
            'footer' => array(
                'type' => 'TEXT',
            ),
            'type' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '1',
                'comment' => '1 one time 2 everylogin 3 everytime',
            ),
            'status' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '1',
                'comment' => '1 active 2 deactivated',
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
            'ENGINE' => 'MyISAM',
        );

        // Create Table models_popups
        $this->dbforge->create_table("models_popups", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table models_popups
        $this->dbforge->drop_table("models_popups", TRUE);
    }

}
