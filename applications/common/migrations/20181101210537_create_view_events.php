<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_View_Events extends CI_Migration {

    public function up() 
    {
        $this->db->query("CREATE OR REPLACE VIEW events_v AS 
            SELECT  c.* 
                ,   ( select trim(value) from content_value where content_id = c.id and name = 'start_date') start_date
                ,   ( select trim(value) from content_value where content_id = c.id and name = 'end_date') end_date
                ,   ( select trim(value) from content_value where content_id = c.id and name = 'image_list') image
                ,   ( select trim(value) from content_value where content_id = c.id and name = 'brief') `lead`
                ,   c.name title
            FROM    content c, content_type ct 
            WHERE   c.content_type_id = ct.id
              AND   ct.uriname = 'event' 
        ");

        $this->db->query('CREATE OR REPLACE VIEW category_events_v AS 
            SELECT * FROM category_content 
             WHERE content_id IN ( SELECT content_id FROM events_v ) 
        ');
    }

    public function down() 
    {
        $this->db->query('DROP VIEW events_v');
        $this->db->query('DROP VIEW category_events_v');
    }
}
