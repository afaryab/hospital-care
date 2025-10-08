<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_reception_counters extends CI_Migration
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
            'counter_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '100'
            ),
            'client_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'is_opd_allowed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'is_emergency_allowed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'is_inpatient_allowed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'is_followup_allowed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'is_allowed_to_pay_voucher' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'is_allowed_to_pay_from_petty_cash' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => 1,
                'null' => FALSE,
            ),
            'cash_on_counter' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'cheques_on_counter' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'card_slips_on_counter' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table aauth_users
        $this->dbforge->create_table("reception_counters", TRUE, $attributes);
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
