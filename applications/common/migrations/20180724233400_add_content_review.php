<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Content_Review extends CI_Migration {

    public function up() {
        
        $this->dbforge->add_field(array(
            'id' => array(
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => TRUE,
                'auto_increment' => TRUE
            ),
            'content_id' => array(
                'type'       => 'INT',
                'constraint' => 11,
            ),
            'value' => array(
                'type'       => 'INT',
                'constraint' => 11,
            ),
            'creation_date' => array(
                'type' => 'DATETIME',
                'default' => '0000-00-00 00:00:00'
            ),
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field('CONSTRAINT FOREIGN KEY (content_id) REFERENCES content(id)');
        $this->dbforge->create_table('content_review');
    }

    public function down() {
        $this->dbforge->drop_table('content_review');
    }
}