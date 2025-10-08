<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_clients extends CI_Migration
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
            'last_user_login_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'nullable' => TRUE,
                'default' => null
            ),
            'current_user_login_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'nullable' => TRUE,
                'default' => null
            ),
            'machine_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'machine_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '24',
                'default' => "Default"
            ),
            'machine_unique_key' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table aauth_users
        $this->dbforge->create_table("clients", TRUE, $attributes);
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
        $this->dbforge->drop_table("clients", TRUE);
       // $this->dbforge->drop_column('aauth_users', $fields);
    }

}
