<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_reception_counters_closings_transaction_elements extends CI_Migration
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
            'closing_transaction_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'patient_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'service_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'original_amount' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => FALSE,
            ),
            'type' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'default' => 'CASH',
                'null' => FALSE,
            ),
            'income_or_expence' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'default' => 'INCOME',
                'null' => FALSE,
            ),
            'department_transaction_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'doctor_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'doctor_service_seq_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
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
        $this->dbforge->create_table("reception_counters_closings_transaction_elements", TRUE, $attributes);
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
        $this->dbforge->drop_table("reception_counters_closings_transaction_elements", TRUE);
       // $this->dbforge->drop_column('aauth_users', $fields);
    }

}
