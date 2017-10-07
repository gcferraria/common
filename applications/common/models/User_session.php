<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Session extends DataMapper 
{
    public $table 	= 'user_session';
    public $has_one = array(
        'user' => array(
            'other_field' => 'sessions',
        ),
    );

}