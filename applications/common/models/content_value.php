<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content Value
 *
 * @uses      DataMapper
 * @package   Contents
 * @copyright Copyright (c) 2012, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Content_Value extends DataMapper {

    var $table = 'content_value';
    var $has_one = array(
        'content' => array(
            'other_field' => 'values',
        ),
        'content_type_field' => array(
            'other_field' => 'content_values',
        )
    );

}

/* End of file content_value.php */
/* Location: ./applications/common/models/content_value.php */
