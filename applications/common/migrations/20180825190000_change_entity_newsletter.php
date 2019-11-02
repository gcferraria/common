<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_entity_newsletter extends CI_Migration {

    public function up() {    

        $this->db->query("
            ALTER TABLE newsletter 
            CHANGE COLUMN name subject VARCHAR(255) NOT NULL;
        ");

        $this->db->query("
            ALTER TABLE newsletter
            ADD COLUMN `status` VARCHAR(1) NOT NULL DEFAULT 'S' AFTER `body`
        ");

        $this->db->query("
            ALTER TABLE newsletter
            ADD COLUMN `phase` INT(1) NOT NULL DEFAULT 1 AFTER `status` 
        ");

        $this->db->query("
            ALTER TABLE newsletter
            ADD COLUMN `sent_to` INT(11) NOT NULL DEFAULT 0 AFTER `phase`
        ");

        $this->db->query("
            ALTER TABLE newsletter
            ADD COLUMN `seen_by` INT(11) NOT NULL DEFAULT 0 AFTER `sent_to`
        ");

        $this->db->query("
            ALTER TABLE `newsletter` DROP COLUMN `site`
        ");

        $this->db->query("
            ALTER TABLE `newsletter_contact` RENAME TO `newsletter_subscriber`
        ");

        $this->db->query("
            ALTER TABLE `newsletter_subscriber` CHANGE `active_flag` `active_flag` INT(1) NULL DEFAULT -1
        ");

        $this->db->query("
            ALTER TABLE `newsletter_subscriber` ADD `activation_token` VARCHAR(32) NULL AFTER `source`
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS `newsletter_template` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `settings_website_id` int(11) NOT NULL,
                `name` varchar(50) NOT NULL,
                `header` TEXT NOT NULL,
                `body` TEXT NOT NULL,
                `footer` TEXT NOT NULL,
                `custom_css` TEXT NOT NULL,
                `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
                `created_by` int(11) DEFAULT NULL,
                `last_update_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `last_update_by` int(11) DEFAULT NULL,                
                PRIMARY KEY (`id`),
                KEY `fk_news_temp_settings_website` (`settings_website_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1
        ");

        $this->db->query("
            ALTER TABLE `newsletter_template`
            ADD CONSTRAINT `fk_news_temp_settings_website` FOREIGN KEY (`settings_website_id`) REFERENCES `settings_website` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
        ");
        
        $this->db->query("
            ALTER TABLE `newsletter` MODIFY COLUMN `creation_date` datetime DEFAULT CURRENT_TIMESTAMP
        ");

        $this->db->query("
            ALTER TABLE `newsletter` DROP COLUMN `template`
        ");

        $this->db->query("
            ALTER TABLE `newsletter` ADD COLUMN `template_id` INT(11) NULL AFTER `id`
        ");

        $this->db->query("
            ALTER TABLE `newsletter_template` ADD COLUMN `is_active` tinyint(1) default 1
        ");

        $this->db->query("
            ALTER TABLE `newsletter`
            ADD FOREIGN KEY (template_id) REFERENCES newsletter_template(id);
        ");

    }

    public function down() {
    }
}
