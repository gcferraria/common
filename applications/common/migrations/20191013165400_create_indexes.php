<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_indexes extends CI_Migration {

    public function up() {    

        $this->db->query("CREATE INDEX idx_trans_language_category ON translation (category_id, language_id);");

        $this->db->query("CREATE INDEX idx_category_option_inheritable ON category_option (category_id, inheritable);");

    }

    public function down() {
        $this->db->query("DROP INDEX idx_trans_language_category;");

        $this->db->query("DROP INDEX idx_category_option_inheritable;");
    }
}
