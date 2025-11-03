<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_patients extends CI_Migration
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
            'pateint_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'gender' => array(
                'type' => 'VARCHAR',
                'constraint' => '1',
                'null' => TRUE,
                'comments' => ' 1 Male, 2 Female, 3 BiSexual'
            ),
            'age_group' => array(
                'type' => 'INT',
                'constraint' => '1',
                'comments' => ' 1 under 18 2 above 18 3 above 40'
            ),
            'age_days' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'age_dob' => array(
                'type' => 'DATETIME',
                'constraint' => '1',
            ),
            'patient_address' => array(
                'type' => 'BLOB',
                'null' => TRUE,
            ),
            'guardian' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'relation' => array(
                'type' => 'VARCHAR',
                'constraint' => '11',
                'null' => TRUE,
                'comments' => ' 1 Son, 2 Daughter, 3 Wife'
            ),
            'patient_contact_mobile' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_contact_res' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_contact_office' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_cnic' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_profession' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_school' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_grade' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_mother' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_mother_occupation' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_mother_office_address' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'patient_mother_phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_father' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_father_occupation' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_father_office_address' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'patient_father_phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            
            'patient_reference' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'last_visit' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            'next_appointment' => array(
                'type' => 'DATETIME',
                'null' => TRUE,
            ),
            
            'service_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '24',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            )
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table patients
        $this->dbforge->create_table("patients", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table patients
        $this->dbforge->drop_table("patients", TRUE);
    }

}
