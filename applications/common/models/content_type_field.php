<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content Type Field
 *
 * @uses      DataMapper
 * @package   Content Types
 * @copyright Copyright (c) 2015, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Content_Type_Field extends DataMapper {

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

/* End of file content_type_field.php */
/* Location: ./applications/common/models/content_type_field.php */
