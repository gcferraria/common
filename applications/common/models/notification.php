<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Notification
 *
 * @uses      DataMapper
 * @package   Notification
 * @copyright Copyright (c) 2013, Gonçalo Ferraria
 * @author    Gonçalo Ferraria <gferraria@gmail.com>
 */

class Notification extends DataMapper {

    var $table = 'notification';

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array( 'required', 'trim' ),
        ),
        'source' => array(
            'type'  => 'text',
            'rules' => array( 'required', 'trim' ),
        ),
        'email' => array(
            'type'  => 'text',
            'rules' => array( 'required', 'trim', 'valid_email' ),
        ),
        'subject' => array(
            'type'  => 'text',
            'rules' => array( 'required', 'trim' ),
        ),
        'body' => array(
            'type'  => 'text',
            'rules' => array( 'required', 'trim' ),
        ),
        'status' => array(
            'type'  => 'radiogroup',
            'value' => '0',
        ),
    );

    /**
     * get_unread_messages: Return the list of unread messages.
     *
     * @access public
     * @return array
     **/
    public function get_unread_messages() {
        return $this->where( 'status', 0 )->order_by('creation_date', 'desc')->get();
    }

    /**
     * get_unread_messages_number: Return the number of unread messages.
     *
     * @access public
     * @return array
     **/
    public function get_unread_messages_number() {
        return $this->where( 'status', 0 )->count();
    }
}

/* End of file notification.php */
/* Location: ../applications/common/models/notification.php */
