<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_option extends DataMapper 
{
    var $table   = 'category_option';
    var $has_one = array(
        'category' => array(
            'other_field' => 'options',
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array(
                'required',
                'unique_pair' => 'category_id',
            ),
        ),
        'value' => array(
            'type'  => 'text',
            'rules' => array('required')
        ),
        'inheritable' => array(
            'type'  => 'radiogroup',
            'rules' => array('required'),
            'value' => '1',
        ),
    );

}