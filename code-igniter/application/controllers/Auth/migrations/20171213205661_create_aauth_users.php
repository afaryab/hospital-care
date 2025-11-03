<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_aauth_users extends CI_Migration
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
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
            ),
            'pass' => array(
                'type' => 'VARCHAR',
                'constraint' => '64',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'banned' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',
                'null' => TRUE,
            ),
            'last_login' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'last_activity' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'last_login_attempt' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'forgot_exp' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'remember_time' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'remember_exp' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'verification_code' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'totp_secret' => array(
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => TRUE,
            ),
            'ip_address' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'login_attempts' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'profile_img_path' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => 'assets/img/avatar.png',
            ),
            'profile_img_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
                'null' => TRUE,
            ),
            'short_story' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'city' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'state' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'country' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'designation' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'parent_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'is_super_admin' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => FALSE,
            ),
            'change_password' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => FALSE,
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
            'feild' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'degree' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'charges' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'is_deleted' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => FALSE,
            ),
            'opd_percent' => array(
                'type' => 'INT',
                'constraint' => '3',
                'default' => '70',
                'null' => FALSE,
            ),
            'salery_type' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
                'null' => FALSE,
            ),
            'salery_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'pass_normal' => array(
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table aauth_users
        $this->dbforge->create_table("aauth_users", TRUE, $attributes);

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
    }

}
