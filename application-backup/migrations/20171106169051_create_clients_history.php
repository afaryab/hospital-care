<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_clients_history extends CI_Migration
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
            'user_login_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'nullable' => TRUE,
                'default' => null
            ),
            'activity_url' => array(
                'type' => 'VARCHAR',
                'constraint' => '255'
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table aauth_users
        $this->dbforge->create_table("clients_history", TRUE, $attributes);
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
        $this->dbforge->drop_table("aauth_users", TRUE);
       // $this->dbforge->drop_column('aauth_users', $fields);
    }

}
