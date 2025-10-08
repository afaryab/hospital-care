<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_query_logging_table extends CI_Migration
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
            'operation' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'query_string' => array(
                'type' => 'LONGBLOB',
            ),
            'target_table' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
            ),
            'data' => array(
                'type' => 'TEXT',
            ),
            'rec_id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'null' => TRUE,
            ),
            'is_synced' => array(
                'type' => 'INT',
                'constraint' => '11',
                'default' => '0',
                'null' => TRUE,
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

        // Add Primary Key.


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'InnoDB',
        );

        // Create Table query_logging_table
        $this->dbforge->create_table("query_logging_table", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table query_logging_table
        $this->dbforge->drop_table("query_logging_table", TRUE);
    }

}
