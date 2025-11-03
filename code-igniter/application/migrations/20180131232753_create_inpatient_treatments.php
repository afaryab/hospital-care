<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_inpatient_treatments extends CI_Migration
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
                'null' => TRUE,
            ),
            'file_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'inpatient_patient_id' => array(
                'type' => 'BIGINT',
                'constraint' => '20',
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
            'treatment_by' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
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
        $this->dbforge->create_table("inpatient_treatments", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table treatments
        $this->dbforge->drop_table("inpatient_treatments", TRUE);
    }

}
