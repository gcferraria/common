<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Newsletter Contact
 *
 * @uses      DataMapper
 * @package   Newsletter
 * @copyright Copyright (c) 2014, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Newsletter_Contact extends DataMapper {

    var $table = 'newsletter_contact';

    public $validation = array(
        'email' => array(
            'type'  => 'text',
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
            'type'  => 'radiogroup',
            'value' => '1',
        ),
    );

}

/* End of file newsletter_contact.php */
/* Location: ./applications/common/models/newsletter_contact.php */
