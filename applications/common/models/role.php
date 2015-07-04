<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Role
 *
 * @uses      DataMapper
 * @package   Users
 * @copyright Copyright (c) 2014, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Role extends DataMapper {

    public $table    = 'role';
    public $has_many = array(
        'users' => array(
            'class'          => 'user',
            'other_field'    => 'roles',
            'join_self_as'   => 'role',
            'join_other_as'  => 'user',
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array('required', 'unique'),
        ),
        'key_match' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'active_flag' => array(
            'type'   => 'radiogroup',
            'rules'  => array('required'),
            'value'  => '1',
        ),
    );

}

/* End of file role.php */
/* Location: ../applications/common/models/role.php */
