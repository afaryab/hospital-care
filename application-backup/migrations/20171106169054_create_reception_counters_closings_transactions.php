<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_reception_counters_closings_transactions extends CI_Migration
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
            'counter_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'orignal_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'patient_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
            ),
            'customer_payed' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'change' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'type' =>  array(
                'type' => 'VARCHAR',
                'constraint' => '11'
            ),
            'income_or_expence' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'default' => 'INCOME',
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
        $this->dbforge->create_table("reception_counters_closings_transactions", TRUE, $attributes);
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
