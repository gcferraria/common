<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_Counter extends DataMapper 
{
    var $table   = 'content_counter';
    var $has_one = array(
        'content' => array(
            'other_field' => 'counters',
        ),
    );

}
