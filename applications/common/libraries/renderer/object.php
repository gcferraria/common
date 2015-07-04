<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Object Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2012 Gonçalo Ferraria
 * @version    1.0 renderer.php 2012-12-15 19:22 gferraria $
 */

class Renderer_Object {
    
    /**
     * @var object, Renderer Object.
     * @access private
    **/
    public $renderer;
    
    /**
     * @var string, object uripath.
     * @access public
    **/
    public $uripath;
    
    /**
     * @var string, object type.
     * @access public
    **/
    public $type;
    
    /**
     * __construct: Renderer Object Class Constructor.
     * 
     * @access public
     * @param  object $renderer, [Required] Renderer Object.
     * @param  string $uripath,  [Required] Object uripath.
     * @param  array  $data,     [Optional] Object data.  
     * @return void
    **/
    public function __construct( $renderer, $uripath, $data = array() ) {
   
        // Set object attributes.
        $this->uripath  = $uripath;
        $this->renderer = $renderer;
        
        foreach ( $data as $name => $value ) {
            $this->$name = $value;
        }
        
        return $this;
    }
    
    /**
     * context_path: Get Object context Path in array.
     * 
     * @access public
     * @return array
    **/
    public function context_path() {
        
        // Get Base Category.
        $root = $this->renderer->base_category();
        
        // Get uripath and remove base category.
        if ( preg_match( "/". str_replace( '/', '\/', $root  ) . "/", $this->uripath ) or !isset( $this->context_uripath ) ) 
            $path = str_replace( $root, '', $this->uripath );
        else { // Is an uriptah from another site.
            $path = str_replace( $root, '', $this->context_uripath );
        }

        // Remove last slash
        $path = explode( '/', $path );
        array_pop( $path );
        
        return $path;
    }
    
    /**
     * rules: Define render rules for object.
     * 
     * @access public
     * @return array
    **/
    public function rules() { show_error('must subclass rules'); }
    
    /**
     * render: Render an Object.
     * 
     * @access public
     * @param  array  $data,[Optional] Aditional Data.
     * @return string
    **/
    public function render( $data = array() ) {
        
        return $this->renderer->render(
            $this->uripath,
            $this->rules(),
            $this,
            $data
        );
    }
}

/* End of file object.php */
/* Location: ./applications/common/libraries/renderer/object.php */
