<?php
/**
 * Breadcrumb Class
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
 * @category   Breadcrumbs
 * @author  Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright   2011 - 2018 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   2.1 breadcrumb.php 2018-03-27 gcferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Breadcrumb 
{
    /**
     * @var    array, Configuration list.
     * @access private
    **/
    private $_config = array();

    /**
     * @var    array, Breadcrumbs list.
     * @access private
    **/
    private $_breadcrumbs = array();

    /**
     * @var    string, Initial Home Icon.
     * @access private
     *
    **/
    private $_home_icon;

    /**
     * @var    string, Initial Home Text.
     * @access private
    **/
    private $_home_text;

    /**
     * @var    string, Initial Home Link.
     * @access private
    **/
    private $_home_link;

    /**
     * @var    string, Breadcrumbs divider.
     * @access private
    **/
    private $_divider;

    /**
     * @var    string, Main Wrapper to Breadcrumbs List.
     * @access private
    **/
    private $_wrapper;

    /**
     * @var    string, Wrapper to Breadcrumbs Item Inline.
     * @access private
    **/
    private $_wrapper_inline;

    /**
     * @var    string, Output Breadcrumbs List.
     * @access private
    **/
    private $_output;

    /**
     * __construct: BreadCrumb Class Constructor.
     *              Load configuration file and inicialize breadcrumbs propreties.
     * 
     * @access public
     * @return void
    **/
    public function __construct() 
    {
        // Get CI Object.
        $this->CI =& get_instance();

        // Load Breadcrumb Configuration.
        $this->_load_config();

        // Load Language File
        $this->_load_language();

        // Inicialize breadcrumbs properties.
        $this->_home_text      = lang('home');
        $this->_home_icon      = $this->_config['home_icon'];
        $this->_home_link      = $this->_config['home_link'];
        $this->_divider        = $this->_config['divider'];
        $this->_wrapper        = $this->_config['wrapper'];
        $this->_wrapper_inline = $this->_config['wrapper_inline'];

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

        // Load Breadcrumb Configuration.
        $this->CI->load->config('breadcrumb', TRUE);

        // Save in Config array the Breadcrumb Configuration
        $this->_config = $this->CI->config->item('breadcrumb');
    }

    /**
     * Load Form Language
     *
     * @return void
     */
    private function _load_language() 
    {
        // Load the Form language file.
        $this->CI->lang->load('breadcrumb');

        $this->lang = $this->CI->lang;
    }

    /**
     * add: Add Breadcrumb to breadcrumbs list.
     *
     * @access public
     * @param  array $breadcrumbs, [Required] Breadcrumbs to add.
     * @return void
    **/
    public function add( $breadcrumbs ) 
    {
        if( is_array( $breadcrumbs ) ) 
        {
            foreach( $breadcrumbs as $breadcrumb ) 
            {
                $this->add( $breadcrumb );
            }
        }

        /**
         * If breadcrumb has not an array and not has a required parameter text
         * go to next breadcrumb and not add breadcrum to the breadcrumbs list.
        **/
        if( !is_array( $breadcrumbs ) || !array_key_exists('text', $breadcrumbs ) )
            return;

        // Add new Breadcrumb to end of the Breadcrumbs list.
        $this->_breadcrumbs[] = $breadcrumbs;

        return;
    }

    /**
     * show: Show Breadcrumb list in HTML format.
     *
     * @access public
     * @return string with breadcrumnbs list in HTML Format.
    **/
    public function show() 
    {
        // Get Wrapper Inline Delimiters.
        $wrapper_inline = explode( '|', $this->_wrapper_inline );

        // Get Home Breadcrumb.
        $this->_output = $this->home_breadcrumb( $wrapper_inline );

        // If have breadcrumbs.
        if ( $this->_breadcrumbs && count($this->_breadcrumbs) > 0 ) 
        {
            foreach( $this->_breadcrumbs as $key => $crumb ) 
            {
                // Add divider.
                if( !empty($this->_divider ) )
                    $this->_output .= $wrapper_inline[0] . $this->_divider . $wrapper_inline[1];

                // If is the last element has not link display breadcrumb withou link.
                $keys = array_keys( $this->_breadcrumbs );
                if( end( $keys ) == $key && !isset( $crumb['href'] ) ) 
                {

                    $this->_output .= $wrapper_inline[0] . $crumb['text'] . $wrapper_inline[1];
                }
                else 
                {
                    $title = isset( $crumb['title'] )
                           ? $crumb['title']
                           : $crumb['text'];

                    if ( !isset( $crumb['href'] ) || !empty( $crumb['href'] ) ) {
                        $link  = '<a href="'. site_url( $crumb['href'] ).'" title="' . $title .'">';
                        $link .= $crumb['text'];
                        $link .= '</a>';
                    }
                    else
                        $link  = '<a href="JavaScript:Void(0)">' . $crumb['text'] . '</a>';

                    $this->_output .= $wrapper_inline[0] . $link . $wrapper_inline[1];
                }
            }
        }

        // If use Wrapper is enable and wrapper is defined.
        if( $this->_config['use_wrapper'] && ! empty( $this->_wrapper ) ) 
        {
            // Get Wrapper Delimiters.
            $wrapper = explode( '|', $this->_wrapper );

            $this->_output = $wrapper[0] . $this->_output . $wrapper[1]; 
        } 

        return $this->_output; 
    }

    /**
     * home_breadcrum: Get Home breadcrumb item in HTML Format.
     *
     * @access private
     * @param  array $wrapper_inline, [Required] Wrapper for Home breadcrum.
     * @return string with home breadcrumnbs in Format or empty if home_text is not defined.
    **/
    private function home_breadcrumb( $wrapper_inline ) 
    {
        /**
         * If home breadcumb text is not empty return the home breadcrumb,
         * else return and empty value.
        **/
        if( empty( $this->_home_text ) )
            return;

        /**
         * If home breadcrumb link is provided and home link breadcrumb config is enabled
         * construct the home breadcrumb with link.
        **/
        if( !$this->_config['unlink_home'] && !empty( $this->_home_link ) ) 
        {
            $link  = '<a href="' . site_url( $this->_home_link ) .'" title="' . strip_tags($this->_home_text) . '">';
            $link .= $this->_home_text;
            $link .= '</a>';

            return $wrapper_inline[0] . $this->_home_icon . $link . $wrapper_inline[1];
        }

        return $wrapper_inline[0] . $this->_home_icon . $this->_home_text . $wrapper_inline[1];
    }

   /**
    * clear: Clear Breadcrumbs list.
    * 
    * @access public
    * @return void
    **/
    public function clear() 
    {
        reset($this->_breadcrumbs);
    }

    /**
     * count: get breadcrumbs number.
     * @access public
     * @return number
    */
    public function count() 
    {
        return count( $this->_breadcrumbs );
    }

}
