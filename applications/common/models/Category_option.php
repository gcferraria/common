<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category Option
 *
 * @uses      DataMapper
 * @package   Categories
 * @copyright Copyright (c) 2012, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Category_option extends DataMapper {

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

/* End of file category_option.php */
/* Location: ./applications/common/models/category_option.php */
