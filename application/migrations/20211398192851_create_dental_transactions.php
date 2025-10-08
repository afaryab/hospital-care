<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_dental_transactions extends CI_Migration
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
            'patient_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'treatment_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => null,
                'null' => TRUE
            ),
            'doctor_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'service_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'amount_in_num' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'amount_in_figure' => array(
                'type' => 'TEXT',
            ),
            'payment_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
            ),
            'payment_refference' => array(
                'type' => 'TEXT',
            ),
            'receaved_by' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'submitted_for_accounts' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'submitted_for_accounts_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'cleared_by_accounts' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'cleared_by_accounts_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'cleared_by_accounts_by' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'units' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '1',
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'modified_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
            'reception_transaction_id' => array(
                'type' => 'INT',
                'constraint' => '11'
            ),
            'doctor_voucher_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table transactions
        $this->dbforge->create_table("dental_transactions", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table transactions
        $this->dbforge->drop_table("dental_transactions", TRUE);
    }

}
