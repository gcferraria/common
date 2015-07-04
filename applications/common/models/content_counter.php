<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content Counter
 *
 * @uses      DataMapper
 * @package   Contents
 * @copyright Copyright (c) 2012, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Content_Counter extends DataMapper {

    var $table   = 'content_counter';
    var $has_one = array(
        'content' => array(
            'other_field' => 'counters',
        ),
    );

}

/* End of file content_counter.php */
/* Location: ./applications/common/models/content_counter.php */
