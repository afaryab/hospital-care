<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_user_viewd_models extends CI_Migration
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
			'model_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
			'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
			'viewied_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),	
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table user_viewd_models
        $this->dbforge->create_table("user_viewd_models", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table user_viewd_models
        $this->dbforge->drop_table("user_viewd_models", TRUE);
    }

}
