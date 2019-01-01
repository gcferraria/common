<?php
/**
 * Google Analitycs Class
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
 * @category   Google Analitycs
 * @author  Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright   2015 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   1.0 ga-api.php 2015-11-03 gcferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'google-api-php-client/src/Google/autoload.php';

class Ga_api 
{    
    /**
     * @var    array, Configuration list.
     * @access private
    **/
    private $_config = array();

    /**
     * @var    string, Service Account Email.
     * @access private
    **/
    private $_account_email;

    /**
     * @var    string, P12 Configuration file location.
     * @access private
    **/
    private $_file_location;

    /**
     * @var    string, Application name.
     * @access private
    **/
    private $_application_name;

    /**
     * @var    string, Available Applications Domains 
     * @access public
    **/
    public $domains = array();

    /**
     * @var    object, Google Service client.
     * @access private
    **/
    private $_client;

    /**
     * @var    object, Google Analytics Service client,
     * @access public
    **/
    public $analytics;

    /**
     * __construct: Google Analytics Class Constructor.
     *              Load configuration file and inicialize ga_api propreties.
     * 
     * @access public
     * @return void
    **/
    public function __construct() 
    {
        // Get CI Object.
        $this->CI =& get_instance();

        // Load Dependencies.
        $this->CI->load->helper('file');
        
        // Load Configuration.
        $this->_load_config();

        // Inicialize Google Anatytics properties.
        $this->_account_email    = $this->_config['account_email'];
        $this->_file_location    = $this->_config['file_location'];
        $this->_application_name = $this->_config['application_name']; 
        $this->profiles          = $this->_config['ga_profiles'];

        // Initialize google services
        $this->_init_services(); 
        
        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

    /**
     * _load_config: Load Breadcrumb Configuration File.
     * 
     * @access private
     * @return void
    **/
    private function _load_config() 
    {
        log_message(
            'debug',
            'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
            'Load Breadcrumb config.'
        );

        // Load Config file
        $this->CI->load->config('ga_api', TRUE);

        // Save in Configuration array
        $this->_config = $this->CI->config->item('ga_api');
    }
    
    /**
     * _init_services: Create and configure a new client object
     *
     * @access private
     * @return void
    **/
    private function _init_services() 
    {
        // Instancie new Google Client
        $this->client = new Google_Client();
        
        // Set application Name
        $this->client->setApplicationName($this->_application_name);
        
        // Instancie new Google Analytics Service 
        $this->analytics = new Google_Service_Analytics($this->client);
        
        // Read configuration file
        $key = read_file($this->_file_location);

        // Get credentials
        $cred = new Google_Auth_AssertionCredentials(
                $this->_account_email,
                array(Google_Service_Analytics::ANALYTICS_READONLY),
                $key
        );
        
        $this->client->setAssertionCredentials($cred);
        if( $this->client->getAuth()->isAccessTokenExpired() ) 
        {
            $this->client->getAuth()->refreshTokenWithAssertion($cred);
        }

        $this->access_token_ready = $this->client->getAccessToken();
    }

    /**
     * is_logged: Check if client is logged
     *
     * @return boolean
    **/
    public function is_logged() 
    {
        return $this->access_token_ready;
    }

}
