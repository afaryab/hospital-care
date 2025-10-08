<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_syncser extends CI_Migration
{

    /**
     * up (create table)
     *
     * @return void
     */
    public function up()
    {

        // Add Fields.
        $this->dbforge->add_field('id');
        $this->dbforge->add_field(array(
            'date_record' => array(
                'type' => 'DATE',
            ),
            'up_records_requested' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'up_records_successed' => array(
                'type' => 'INT',
                'constraint' => '11',
            ),
            'created_on' => array(
                'type' => 'TIMESTAMP',
                'default' => ['value' => 'CURRENT_TIMESTAMP', 'string' => false],
            ),
            'modified_on' => array(
                'type' => 'TIMESTAMP',
                'null' => TRUE,
            ),
        ));


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table syncser
        $this->dbforge->create_table("syncser", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table syncser
        $this->dbforge->drop_table("syncser", TRUE);
    }

}
