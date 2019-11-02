<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_View_Content_Tops extends CI_Migration {

    public function up() 
    {
        $this->db->query("
            CREATE OR REPLACE VIEW content_tops_v AS 
            SELECT  content_id
                ,   COUNT(1) AS views
              FROM  content_counter
             WHERE  1=1
             GROUP  BY content_id
            HAVING  COUNT(1) > 0
             ORDER  BY COUNT(1) DESC
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `content_tops_mv` (
                `content_id` int(11) NOT NULL,
                `views` INT(11) NOT NULL,
                PRIMARY KEY (`content_id`)
            ) ENGINE=InnoDB;
        ");

        $this->db->query("
        CREATE EVENT content_tops_e
            ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 12 HOUR
            DO
                DELETE FROM content_tops_mv; INSERT INTO content_tops_mv SELECT * FROM content_tops_v ORDER BY views DESC;
        ");
    }

    public function down() {
        $this->db->query('DROP VIEW content_tops_v');
    }
}
