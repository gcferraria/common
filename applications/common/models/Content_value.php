<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_Value extends DataMapper 
{
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