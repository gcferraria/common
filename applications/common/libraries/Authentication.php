<?php
/**
 * Form Class
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category   Authentication
 * @author  Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright   2011 - 2016 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   Version 1.0 form.php 2016-05-29 gferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication 
{
    /**
     * @var object, Codeigniter Instance
     * @access private
    **/
    private $CI;

    /**
     * __construct: Ahthentication Class Constructor.
     *
     * @access public
     * @return void
    **/
    public function __construct() 
    {
        // Get CI Object.
        $this->CI =& get_instance();

        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

    /**
     * logged_in: Check if exist any administrator logged in.
     *
     * @access public
     * @return boolean
    **/
    public function logged_in() 
    {
        if ( $administrator = $this->administrator() ) 
        {
            $sessions = $administrator->sessions->where( array(
                    'session_id' => session_id()
                )
            )->count();

            if ( $sessions == 1 ) 
            {
                log_message(
                    'debug',
                    'Controller: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                    'Already exist one Session for Administrator.'
                );

                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * login: Checks if the administrator login credentials are valid
     *        and this is not locked.
     *
     * @access public
     * @param string  $username, [Required] username of the administrator.
     * @param string  $password, [Required] password of the administrator.
     * @param boolean $remember, [Required] Remember Login Option.
     * @return boolean
    **/
    public function login( $username, $password, $remember ) 
    {
        $administrator = new Administrator;
        $administrator->username = $username;
        $administrator->password = $password;

        if ( $administrator->login() ) 
        {
            // Set Login Cookies.
            $this->CI->session->set_userdata( array(
                    'logged_in' => TRUE,
                    'admin_id'  => $administrator->id,
                )
            );

            // Register Administrator Session.
            $session = new Administrator_Session;
            $session->session_id       = session_id();
            $session->user_agent       = $this->CI->input->user_agent();
            $session->ip_address       = $_SERVER['REMOTE_ADDR'];
            $session->browser          = $this->CI->agent->browser();
            $session->operating_system = $this->CI->agent->platform();

            $session->save( $administrator );

            log_message(
                'debug',
                'Controller: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'Successful Login for Administrator ' . $administrator->name . '.'
            );

            return TRUE;
        }

        log_message(
            'debug',
            'Controller: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
            'Invalid Login.'
        );

        return FALSE;
    }

    /**
     * usernameExists: Checks if the username exists.
     *
     * @access public
     * @param  string  $username, [Required] username of the administrator.
     * @return boolean
    **/
    public function usernameExists( $username ) 
    {
        if ( isset($username) && !empty($username) ) 
        {
            $administrator = new Administrator();

            if ( $administrator->get_by_username( $username )->exists() ) 
            {
                return TRUE;
            }
        }

        return  FALSE;
    }

    /**
     * forgetPassword: Generate a new password.
     *
     * @access public
     * @param  string  $email, [Required] email of the administrator.
     * @return mixed, null if email not exists or string with generated password.
    **/
    public function forgetPassword( $email ) 
    {    
        if ( isset($email) && !empty($email) ) 
        {
            $administrator = new Administrator();
            $administrator->where( array( 'email' => $email, 'active_flag' => 1 ) )->get();
            
            if ( $administrator->exists() ) 
            {
                $this->CI->load->helper('string');
                $password = random_string('alnum', 8 );
                $administrator->password = $password;

                // Save user data.
                if( $administrator->save() ) 
                {
                    return $password;
                }
            }
        }

        return;
    }

    /**
     * administrator: Get the Administrator logged in.
     *
     * @access public
     * @return Object or null.
    **/
    public function administrator() 
    {
        if ( $this->_check_login_cookies() ) 
        {
            $administrator = new Administrator;

            return $administrator->get_by_id(
                $this->CI->session->userdata('admin_id')
            );
        }

        return;
    }

    /**
     * is_logged: Check if Administrator is Logged
     *
     * @access public
     * @return boolean
    **/
    public function isLogged() 
    {
        return is_empty( $this->administrator() ) ? FALSE : TRUE; 
    }

    /**
     * is_admin: Checks if administrator logged in is an super administrator.
     *
     * @access public
     * @return boolean
    **/
    public function is_admin() 
    {
        if ( $administrator = $this->administrator() ) 
        {
            return $administrator->super_admin_flag == 1 ? TRUE : FALSE;
        }

        return FALSE;
    }

    /**
     * _check_login_cookies: Checks if login cookies are defined.
     *
     * @access private
     * @return boolean
    **/
    private function _check_login_cookies() 
    {
        // Checks if login cookies are defined.
        if( ! $this->CI->session->userdata('logged_in') ||
            ! $this->CI->session->userdata('admin_id')
        ) 
        {
            log_message(
                'debug',
                'Controller: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'No data found for Administrator Session.'
            );

            return FALSE;
        }

        return TRUE;
    }

    /**
     * logout: Destroy the Administrator Sessions.
     *
     * @access public
     * @param  string $redirect, [Optional] Logout Redirect.
     * @return void
    **/
    public function logout( $redirect = 'login' ) 
    {
        // Destroy Administrator Sessions Variables.
        $this->CI->session->unset_userdata(session_id());

        // Destroy session.
        $this->CI->session->sess_destroy();

        // Redirect.
        redirect( $redirect, 'refresh');
    }

}