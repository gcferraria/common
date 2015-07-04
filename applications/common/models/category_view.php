<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category View
 *
 * @uses      DataMapper
 * @package   Categories
 * @copyright Copyright (c) 2014, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Category_View extends DataMapper {

	var $table    = 'category_view';
	var $has_one  = array(
        'category' => array(
            'class' => 'category',
        ),
    );

}

/* End of file category_view.php */
/* Location: ./applications/common/models/category_view.php */
