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
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => TRUE
            ),
            'email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'communication_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'pass' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'banned_message' => array(
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
                'default' => 'public/img/avatar.png',
            ),
            'profile_img_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => null,
                'null' => TRUE,
            ),
            'short_story' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'address' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
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
            'act_as_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
                'default' => null
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
            'is_receptionist' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'reception_id' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'can_change_reception' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_doctor' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_nurse' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_xray_tech' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_opd_doctor' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_inpatient_doctor' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_emergency_doctor' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'is_accountant' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
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
            'opd_charges_type' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0', // 0 For Fixed and 1 For Percentage
                'null' => FALSE,
            ),
            'opd_charges_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'salery_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'is_deleted' => array(
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
        $this->dbforge->create_table("aauth_users", TRUE, $attributes);
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
