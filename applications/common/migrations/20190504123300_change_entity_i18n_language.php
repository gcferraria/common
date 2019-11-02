<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_entity_i18n_language extends CI_Migration {

    public function up() {    

        $this->db->query("
            ALTER TABLE i18n_language
            ADD COLUMN `region` VARCHAR(10) NULL AFTER `name`
        ");

    }

    public function down() {
    }
}
