<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_dic_eng extends CI_Migration
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
            'word' => array(
                'type' => 'VARCHAR',
                'constraint' => '25',
            ),
			'wordtype' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
            ),
			'definition' => array(
                'type' => 'TEXT',
            ),
			 'used' => array(
                'type' => 'INT',
                'constraint' => '11',
				'default' => '0',
            ),
        ));


        // Table attributes.

        $attributes = array(
            'ENGINE' => 'MyISAM',
        );

        // Create Table dic_eng
        $this->dbforge->create_table("dic_eng", TRUE, $attributes);

    }

    /**
     * down (drop table)
     *
     * @return void
     */
    public function down()
    {
        // Drop table dic_eng
        $this->dbforge->drop_table("dic_eng", TRUE);
    }

}
