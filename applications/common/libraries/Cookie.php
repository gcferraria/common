<?php
/**
 * Cookie Class
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
 * @category   Session
 * @author  Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright   2011 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   1.0 Cookie.php 1.0 2011-09-28 gferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Cookie 
{
    /**
     * @var string, name of the cookie.
     * @access public
    **/
    public $name;

    /**
     * @var int, expiration time of the cookie.
     * @access private
    **/
    private $_expire;

    /**
     * @var string, path on the server in which the cookie
     *              will be available.
     * @access private
    **/
    public $_path;

    /**
     * @var string, domain that the cookie is available.
     * @access private
    **/
    private $_domain;

    /**
     * @var boolean, indicates that the cookie should only be 
     *      transmitted over a secure HTTPS connection from the client.
     * @access private
    **/
    private $_secure;

    /**
     * __construct: Cookie Class Constructor.
     *              Inicialize cookie properties based on Cookies config.
     * 
     * @access public
     * @return void
    **/
    public function __construct() 
    {
        // Get CI Object.
        $this->CI =& get_instance();

        // Inicialize cookie properties.
        $this->_expire = $this->CI->config->item('cookie_time');
        $this->_path   = $this->CI->config->item('cookie_path');
        $this->_domain = $this->CI->config->item('cookie_domain');
        $this->_secure = $this->CI->config->item('cookie_secure');

        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

   /**
     * exists: Verify the existence of the cookie regarding its name.
     * 
     * @access public
     * @param  string $name, [Required] name of the cookie.
     * @return boolean, return TRUE if cookie exist, and FALSE if not.
    **/
    public function exists( $name ) 
    {
        // If name is provide.
        if ( !empty( $name ) ) 
        {
            $this->name = $name;

            // Check is cookie exist.
            return isset( $_COOKIE[$this->name] );
        }

        log_message(
            'error',
            'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
            'No Required Parameter name provided.'
        );

        return FALSE;
    }

   /**
     * is_empty: Checks if cookie is empty. Returns true if there no cookie with
     *           this name or it's empty, or 0, or a few other things.
     *
     * @access public
     * @param  string $name, [Required] name of the cookie.
     * @return boolean, return TRUE if cookie is empty, and FALSE if not.
    **/
    public function is_empty( $name ) 
    {
        // If name is provide.
        if ( $name ) 
        {
            $this->name = $name;

            return empty( $_COOKIE[ $this->name ] ) ? TRUE : FALSE;
        }

        log_message(
            'error',
            'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
            'No Required Parameter name provided.'
        );

        return FALSE;
    }

   /**
     * get: Get the value of the given cookie. 
     *      If the cookie does not exist the value of $default will be returned.
     *
     * @access public
     * @param  string $name, [Required] name of the cookie.
     * @param  string $default, [Optional] default value for cookie if cookie not exist.
     * @return mixed, return NULL if cookie name not exist, return the value of cookie if exist
     *               and return FALSE if cookie name is not provided.
    **/
    public function get( $name, $default = NULL ) 
    {
        if ( $name ) 
        {
            $this->name = $name;

            return $this->exists( $this->name ) ? $_COOKIE[$this->name] : $default;
        }

        log_message(
            'error',
            'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
            'No Required Parameter name provided.'
        );

        return FALSE;
    }

   /**
     * set: Set the cookie by calling setcookie function.
     *
     * @access public
     * @param string $name,   [Required] name of the cookie.
     * @param string $value,  [Required] value for cookie.
     * @param int $time,      [Optional] The time the cookie expires.
     * @param string $path    [Optional] The path on the server in which the cookie will be available on.
     * @param string $domain  [Optional] The domain that the cookie is available to.
     * @param boolean $secure [Optional] Indicates that the cookie should only be 
     *        transmitted over a secure HTTPS connection from the client.
     * @return boolean, return true if save cookie with success, and false if not.
    **/
    public function set( $name, $value , $expire = NULL , $path = NULL , $domain = NULL , $secure = NULL ) 
    {
        // If no value defined for cookie, check if cookie exist and get your value.
        if ( isset( $value ) && isset( $name ) ) 
        {
            $this->name    = $name;
            $this->_value  = $value;
            $this->_expire = isset($expire) ? $expire : $this->_expire;
            $this->_path   = isset($path)   ? $path   : $this->_path;
            $this->_domain = isset($domain) ? $domain : $this->_domain;
            $this->_secure = isset($secure) ? $secure : $this->_secure;
        }
        else 
        {
            log_message(
                'error',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'No Requireds Parameters provided.'
            );

            return FALSE;
        }

        // Silently does nothing if headers have already been sent.
        if ( ! headers_sent() ) 
        {
            // Save Cookie.
            $result = setcookie( 
                $this->name, 
                $this->_value, 
                $this->_expire, 
                $this->_path, 
                $this->_domain, 
                $this->_secure
            );
            
            return $result;
        }

        return FALSE;
    }

    /**
     * delete: Delete a cookie.
     *
     * @param  string $name, [Required] name of the cookie.
     * @param  bool   $remove_from_global, [Optional] set to TRUE to remove this cookie from this request.
     * @param  string $path [optional] The path on the server in which the cookie will be available on.
     * @param  string $domain [optional] The domain that the cookie is available to.
     * @return boolean, return TRUE if cookie has be deleted with success, and FALSE if not exist or
     *                  cookie does not exist.
    **/
    public function delete( $name, $remove_from_global = FALSE, $path = NULL , $domain = NULL ) 
    {
        // If Cookie not exist.
        if ( $this->exists( $name ) ) 
        {
            log_message(
                'debug',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'The cookie for delete not exist.'
            );

            return FALSE;
        }

        $this->name    = $name;
        $this->_path   = $path || $this->_path;
        $this->_domain = $domain || $this->_domain;

        // Silently does nothing if headers have already been sent.
        if ( !headers_sent() ) 
        {
            $return = setcookie( $this->name, '', time() - 3600, $this->_path, $this->_domain );

            // In case you definitely want to delete the cookie.
            if ( $remove_from_global )
                unset( $_COOKIE[$this->name] );

            return $return;
        }

        return FALSE;
    }

}
