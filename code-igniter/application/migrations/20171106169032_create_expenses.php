<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_expenses extends CI_Migration
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
            'voucher_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'amount_received_num' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'amount_received_words' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'payment_type' => array(
                'type' => 'VARCHAR',
                'constraint' => '6',
            ),
            'payment_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'cleared_by_accounts' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'cleared_by_accounts_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'cleared_by_accounts_by' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'submitted_for_accounts' => array(
                'type' => 'INT',
                'constraint' => '1',
            ),
            'submitted_for_accounts_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'category_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'payed_to_employee' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',				
            ),
            'payed_to' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'payed_to_other' => array(
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => TRUE,
            ),
            'receaved_by' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table expenses
        $this->dbforge->create_table("expenses", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table expenses
        $this->dbforge->drop_table("expenses", TRUE);
    }

}
