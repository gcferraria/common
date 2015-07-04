<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breadcrumb Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Breadcrumbs
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2011 - 2014 Gonçalo Ferraria
 * @version    1.0.4 breadcrumb.php 2014-07-29 22:26 gferraria $
 */

class Breadcrumb {

    /**
     * @var array, Configuration list.
     * @access private
    **/
    private $_config = array();

    /**
     * @var array, Breadcrumbs list.
     * @access private
    **/
    private $_breadcrumbs = array();

    /**
     * @var string, Inicial Home Text.
     * @access private
    **/
    private $_home_text;

    /**
     * @var string, Inicial Home Link.
     * @access private
    **/
    private $_home_link;

    /**
     * @var string, Breadcrumbs divider.
     * @access private
    **/
    private $_divider;

    /**
     * @var string, Main Wrapper to Breadcrumbs List.
     * @access private
    **/
    private $_wrapper;

    /**
     * @var string, Wrapper to Breadcrumbs Item Inline.
     * @access private
    **/
    private $_wrapper_inline;

    /**
     * @var string, Output Breadcrumbs List.
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
    public function __construct() {

        // Get CI Object.
        $this->CI =& get_instance();

        // Load Breadcrumb Configuration.
        $this->_load_config();

        // Inicialize breadcrumbs properties.
        $this->_home_text      = $this->_config['home_text'];
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
    private function _load_config() {

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
     * add: Add Breadcrumb to breadcrumbs list.
     *
     * @access public
     * @param  array $breadcrumbs, [Required] Breadcrumbs to add.
     * @return void
    **/
    public function add( $breadcrumbs ) {

        if( is_array( $breadcrumbs ) ) {

            foreach( $breadcrumbs as $breadcrumb ) {
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
    public function show() {

        // Get Wrapper Inline Delimiters.
        $wrapper_inline = explode( '|', $this->_wrapper_inline );

        // Get Home Breadcrumb.
        $this->_output = $this->home_breadcrumb( $wrapper_inline );

        // If have breadcrumbs.
        if ( $this->_breadcrumbs && count($this->_breadcrumbs) > 0 ) {

            foreach( $this->_breadcrumbs as $key => $crumb ) {

                // Add divider.
                if( !empty($this->_divider ) )
                    $this->_output .= $wrapper_inline[0] . $this->_divider . $wrapper_inline[1];

                // If is the last element has not link display breadcrumb withou link.
                if( end( array_keys( $this->_breadcrumbs ) ) == $key && !isset( $crumb['href']) ) {

                    $this->_output .= $wrapper_inline[0] . $crumb['text'] . $wrapper_inline[1];
                }
                else {
                    $title = isset( $crumb['title'] )
                           ? $crumb['title']
                           : $crumb['text'];

                    if ( !isset( $crumb['href'] ) || !empty( $crumb['href'] ) ) {
                        $link  = '<a href="'. site_url( $crumb['href'] ).'" title="' . $title .'">';
                        $link .= $crumb['text'];
                        $link .= '</a>';
                    }
                    else
                        $link  = $crumb['text'];

                    $this->_output .= $wrapper_inline[0] . $link . $wrapper_inline[1];
                }
            }
        }

        // If use Wrapper is enable and wrapper is defined.
        if( $this->_config['use_wrapper'] && ! empty( $this->_wrapper ) ) {

            // Get Wrapper Delimiters.
            $wrapper = explode( '|', $this->_wrapper );

            $this->_output .= $wrapper[0] . $this->_output[1] . $wrapper[1]; 
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
    private function home_breadcrumb( $wrapper_inline ) {

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
            if( !$this->_config['unlink_home'] && !empty( $this->_home_link ) ) {

                $link  = '<a href="' . site_url( $this->_home_link ) .'" title="' . strip_tags($this->_home_text) . '">';
                $link .= $this->_home_text;
                $link .= '</a>';

                return $wrapper_inline[0] . $link . $wrapper_inline[1];
            }
            else {
                return $wrapper_inline[0] . $this->_home_text . $wrapper_inline[1];
            }

        return;
    }

   /**
    * clear: Clear Breadcrumbs list.
    * 
    * @access public
    * @return void
    **/
    public function clear() {
        reset($this->_breadcrumbs);
    }

    /**
     * count: get breadcrumbs number.
     * @access public
     * @return number
    */
    public function count() {
        return count( $this->_breadcrumbs );
    }

}

/* End of file breadcrumb.php */
/* Location: ./applications/common/libraries/breadcrumb.php */
