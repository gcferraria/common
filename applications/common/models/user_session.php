<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User Session
 *
 * @uses      DataMapper
 * @package   Users
 * @copyright Copyright (c) 2014, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class User_Session extends DataMapper {

    public $table 	= 'user_session';
    public $has_one = array(
        'user' => array(
            'other_field' => 'sessions',
        ),
    );

}

/* End of file user_session.php */
/* Location: ./applications/gpanel/models/user_session.php */
