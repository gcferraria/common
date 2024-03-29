<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_Type_Field extends DataMapper 
{
    var $table   = 'content_type_field';
    var $has_one = array(
        'content_type' => array(
            'class'          => 'content_type',
            'other_field'    => 'content_type_fields',
            'join_other_as'  => 'content_type',
        ),
        'content_values' => array(
            'class'          => 'content_value',
            'cascade_delete' => TRUE,
        ),
        'related_caller' => array(
            'class'        => 'Content_Type_Field',
            'other_field'  => 'parent',
            'join_table'   => 'Content_Type_Field',
            'reciprocal'   => TRUE,
        ),
        'caller' => array(
            'class'          => 'Content_Type_Field',
            'other_field'    => 'related_caller',
            'reciprocal'     => TRUE,
            'cascade_delete' => FALSE,
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array(
                'required',
                'unique_pair' => 'content_type_id',
            )
        ),
        'label' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'type' => array(
            'type'  => 'select',
            'rules' => array('required'),
        ),
        'parent_id' => array(
            'type'  => 'select',
            'rules' => array(),
        ),
        'required' => array(
            'type'  => 'radiogroup',
            'value' => '0',
        ),
        'args' => array(
            'type' => 'textarea',
        ),
        'help' => array(
            'type' => 'textarea',
        ),
        'position' => array(
            'type'  => 'spinner',
            'rules' => array('numeric'),
            'value' => 0,
        ),
        'active_flag' => array(
            'type'   => 'radiogroup',
            'value'  => '1',
        ),
        'translatable' => array(
            'type'  => 'radiogroup',
            'value' => 1,
        )
    );

}
