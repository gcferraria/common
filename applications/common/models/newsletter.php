<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Newsletter
 *
 * @uses      DataMapper
 * @package   Newsletter
 * @copyright Copyright (c) 2014, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Newsletter extends DataMapper {

    var $table = 'newsletter';

    var $has_one = array(
        'administrator' => array(
            'other_field'   => 'categories',
            'join_table'    => 'category',
            'join_other_as' => 'creator',
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array( 'required','trim' ),
        ),
        'from' => array(
            'type'  => 'select',
            'rules' => array('required','valid_email','trim'),
        ),
        'template' => array(
            'type'  => 'select',
            'rules' => array('required','trim'),
        ),
        'website' => array(
            'type'  => 'select',
            'rules' => array('required','trim'),
        ),
        'content_types' => array(
            'type' => 'multiselect',
            'rules' => array('required','trim'),
        ),
        'date_range' => array(
            'type' => 'daterange',
            'rules' => array('required','trim'),
        ),
        'body' => array(
            'type' => 'wysiwyg',
            'rules' => array('required','trim'),
        ),
    );

}

/* End of file newsletter.php */
/* Location: ./applications/common/models/newsletter.php */
