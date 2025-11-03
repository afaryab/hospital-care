<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_aauth_users_otp extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'code' => array(
                'type' => 'VARCHAR',
                'constraint' => '12'
            ),
            'is_consumed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => FALSE,
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table aauth_users
        $this->dbforge->create_table("aauth_users_otp", TRUE, $attributes);
       // $this->dbforge->add_column('aauth_users', $fields);
    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table aauth_users
        $this->dbforge->drop_table("aauth_users_otp", TRUE);
       // $this->dbforge->drop_column('aauth_users', $fields);
    }

}
