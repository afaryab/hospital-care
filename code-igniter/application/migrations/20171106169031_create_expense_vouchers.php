<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_expense_vouchers extends CI_Migration
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
			'exp_category_id' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'inpatient_file_id' => array(
                'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
			'exp_amount_numbers' => array(
                'type' => 'INT',
                'constraint' => '11',		
            ),
			'exp_amount_words' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'payed_to_employee' => array(
                'type' => 'TINYINT',
                'constraint' => '1',
                'default' => '0',				
            ),
			 'employee_id' => array(
                'type' => 'INT',
                'constraint' => '11',
				'null' => TRUE,
            ),
			'payed_to_others' => array(
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => TRUE,
            ),
			'expense_notes' => array(
                'type' => 'TEXT',
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
                'constraint' => '50',
                'null' => TRUE,
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table expense_vouchers
        $this->dbforge->create_table("expense_vouchers", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table expense_vouchers
        $this->dbforge->drop_table("expense_vouchers", TRUE);
    }

}
