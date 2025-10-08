<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_xray_treatments extends CI_Migration
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
            'status' => array(
                'type' => 'VARCHAR',
                'constraint' => '50'
            ),
            'patient_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'xray_patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
            ),
            'patient_discomfort' => array(
                'type' => 'VARCHAR',
                'constraint' => '13',
                'null' => TRUE,
            ),
            'patient_bleed_excess' => array(
                'type' => 'VARCHAR',
                'constraint' => '13',
                'null' => TRUE,
            ),
            'already_medication' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_smoker' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_smoking_frequency' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'is_diabetic' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'tuberculosis' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'hepatitis' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'epilepsy' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'rheumatic' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'hiv' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'is_heart_patient' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'is_allergietic' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'prefer_anesthetic' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'is_pregnant' => array(
                'type' => 'VARCHAR',
                'constraint' => '13',
                'null' => TRUE,
            ),
            'patient_discomfirt_start' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_is_first_visit' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'patient_last_visit' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_last_visit_process' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'patient_physician' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_physician_phone' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'patient_last_examination' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'patient_under_medical' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'treatment_diagnosis_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
            ),
            'name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'description' => array(
                'type' => 'TEXT',
            ),
            'media_files' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE,
            ),
            'service_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'service_name' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
            'treatment_by' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            
            'treatment_charges' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'default' => '0',
            ),
            'treatment_payed' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '1',
                'default' => '0',
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'expire_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'will_occure_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
            'live_ref_number' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
        ));

        // Add Primary Key.
        $this->dbforge->add_key("id", TRUE);

        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table treatments
        $this->dbforge->create_table("xray_treatments", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table treatments
        $this->dbforge->drop_table("xray_treatments", TRUE);
    }

}
