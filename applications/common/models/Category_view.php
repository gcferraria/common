<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_view extends DataMapper 
{
	var $table    = 'category_view';
	var $has_one  = array(
        'category' => array(
            'class' => 'category',
        ),
    );

}