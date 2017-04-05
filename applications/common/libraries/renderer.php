<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer Class
 *
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2015 Gonçalo Ferraria
 * @version    1.2 renderer.php 2015-01-24 19:07 gferraria $
**/

// Load Plugins.
require_once('renderer/object.php');
require_once('renderer/list.php');
require_once('renderer/list/item.php');
require_once('renderer/object/category.php');
require_once('renderer/object/content.php');
require_once('renderer/object/template.php');
require_once('renderer/object/content/list.php');
require_once('renderer/object/content/list/item.php');
require_once('renderer/paged.php');
require_once('renderer/object/category/list.php');
require_once('renderer/object/category/list/item.php');

class Renderer {

    /**
     * @var array, Configuration list.
     * @access private
    **/
    private $config = array();

    /**
     * @var string, base category.
     * @access private
    **/
    private $base_category = '/';

    /**
     * @var string, root path to load views.
     * @access public
    **/
    public $location;

    /**
     * @var string, legacy mode
     * @access public
    **/
    public $legacy;

    /**
     * @var boolean, i18n support
     * @access public
    **/
    public $i18n;

    /**
     * @var string, language
     * @access private
     */
    private $language;

    /**
     * __construct: Renderer Class Constructor.
     *              Load Plugins.
     *
     * @access public
     * @return void
    **/
    public function __construct( $config = array() ) {

        // Load Renderer configuration.
        $this->_load_config();

        // Define base category based on config file.
        $this->base_category = $this->config['base_category'];
        $this->location      = $this->config['location'];
        $this->legacy        = $this->config['legacy'];
        $this->i18n          = isset( $this->config['i18n'] ) ? $this->config['i18n'] : FALSE;

        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

    /**
     * _load_config: Load Form Configuration File.
     *
     * @access private
     * @return void
    **/
    private function _load_config() {

        // Get CI instance.
        $CI =& get_instance();

        // Load Form Configuration.
        $CI->load->config( 'renderer', TRUE );

        // Save in Config array the Form Configuration.
        $this->config = $CI->config->item('renderer');
    }

    /**
     * base_category: Get base category.
     *
     * @access public
     * @return string
    **/
    public function base_category() {
        return $this->base_category;
    }

    /**
     * category: Get an Category.
     *
     * @acess  public
     * @param  mixed $object, [Optional] Category Uripath or Category Object.
     * @return mixed
    **/
    public function category( $object ) {

        if ( is_string( $object ) ) {
            $object = str_replace( $this->base_category, '', $object );
            $object = $this->base_category . $object;

            if ( !preg_match( '/.+\/$/', $object ) )
                $object .= '/';
        }

        return new Renderer_Category( $object, $this );
    }

    /**
     * content: Get an Content.
     *
     * @acess  public
     * @param  mixed  $object, [Required] Content Identifier or Content Object.
     * @param  string $parent, [Required] Parent uripath.
     * @return mixed
    **/
    public function content( $object, $parent ) {

        if ( isset($this->config['shared_categories']) && !empty($this->config['shared_categories']) ) {
            foreach ($this->config['shared_categories'] as $shared ) {
                $parent = str_replace( $shared, '', $parent );
            }

            $parent = $this->base_category . $parent;
        }

        if ( !preg_match( '/.+\/$/', $parent ) )
            $parent .= '/';

        return new Renderer_Content( $object, $parent, $this );
    }

    /**
     * render: Render an object.
     *
     * @access public
     * @param  string $uripath, [Required] Object Uripath
     * @param  array  $rules,   [Required] Object rules.
     * @param  object $object,  [Required] Object to Render.
     * @param  array  $data,    [Optional] Aditional Data.
     * @return string
    **/
    public function render( $uripath, $rules = array(), $object, $data = array() ) {

        if ( !isset( $uripath ) || empty( $rules ) )
            return;

        // Remove base category.
        $path = str_replace( $this->base_category, '', $uripath );

        // Find file recursively.
        if ( $path = $this->search( "/$path", $rules ) ) {

            // Get CI instance.
            $CI =& get_instance();

            // Change root if defined.
            if ( $location = $this->location )
                $path = $location . $path;

            // Return file output.
            $CI->load->view(
                $path,
                array_replace_recursive(
                    array( 'object' => $object, 'data' => $data ),
                    $data
                )
            );
        }

        return '';
    }

    /**
     * search: Find recursively an file based on your path.
     *
     * @access public
     * @param  string $path,  [required] File Path.
     * @param  array  $rules, [Required] Object Rules.
     * @return string
    **/
    public function search( $uripath, $rules ) {

        // Parse path.
        $path = explode( '/', $uripath );

        // Remove last position of path.
        array_pop( $path );

        do {
            $local_path = join( '/', $path );

            foreach ( $rules as $rule ) {
                $renderer = join( '/', array( $local_path, $rule ) );

                //print_r( $renderer . "<br />" );

                if ( is_file( APPPATH . 'views/html' . "$renderer.php") )
                    return $renderer;
            }

            // Remove Last position
            array_pop( $path );
        }
        while( !empty( $path ) );

        return false;
    }

    /**
     * set_language: Change language context.
     *
     * @param string $language language code for set context.
     */
    public function set_language( $language ) {
        $this->language = $language;
    }

    /**
     * get_language: Get language context.
     *
     * @return string
     */
    public function get_language() {
        $language = new I18n_Language();
        return $language->get_by_code( $this->language );
    }
}