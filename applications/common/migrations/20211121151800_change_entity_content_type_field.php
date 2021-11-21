<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_Entity_Content_Type_Field extends CI_Migration {

    public function up() {
        $this->db->query("
            ALTER TABLE content_type_field 
            ADD COLUMN `parent_id` int(11) DEFAULT NULL;
        ");
    }

    public function down() {
        $this->db->query("
            ALTER TABLE content_type_field 
            DROP COLUMN `parent_id`;
        ");
    }
}
