<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter_Template extends DataMapper 
{
    var $table = 'newsletter_template';
    var $has_many = array(
        'newsletters' => array(
            'class'          => 'newsletters',
            'other_field'    => 'template',
            'cascade_delete' => TRUE,
        ),
    ); 
    var $has_one  = array(
        'website' => array(
            'class'          => 'settings_website',
            'other_field'    => 'newsletter_templates',
            'join_other_as'  => 'website',
        ),
    );

    public $validation = array(
        'settings_website_id' => array(
            'type'  => 'select',
            'rules' => array('required'),
        ),
        'name' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'header' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'body' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'footer' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'is_active' => array(
            'type'  => 'radiogroup',
            'value' => '1',
        ),
    );

}