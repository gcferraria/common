<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Newsletter_Subscriber extends DataMapper 
{
    var $table = 'newsletter_subscriber';

    public $validation = array(
        'email' => array(
            'type'  => 'email',
            'rules' => array( 'required', 'trim', 'valid_email' ),
        ),
        'name' => array(
            'type'  => 'text',
            'rules' => array( 'trim' ),
        ),
        'source' => array(
            'type'  => 'text',
            'rules' => array('trim'),
        ),
        'active_flag' => array(
            'type'  => 'select',
            'value' => '-1',
        ),
        'activation_token' => array(
            'type'  => 'text',
            'rules' => array('trim'),
        )
    );

}
