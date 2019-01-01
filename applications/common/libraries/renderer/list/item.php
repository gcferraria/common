<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renderer_List_Item Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2013 Gonçalo Ferraria
 * @version    1.0 renderer.php 2013-01-10 21:30 gferraria $
 */

class Renderer_List_Item {
    
    /**
     * @var object, Renderer Object.
     * @access private
    **/
    public $renderer;
    
    /**
     * @var string, parent object.
     * @access private
    **/
    private $parent;
    
    /**
     * @var object, Object list item.
     * @access public
    **/
    public $item;
    
    /**
     * __construct: Renderer List Item Class Constructor.
     * 
     * @access public
     * @param  object $parent, [Required] parent object.
     * @param  object $item  , [Required] object list item.
     * @return void
    **/
    public function __construct( $parent, $item ) {
        
        // Set object attributes.
        $this->parent   = $parent;
        $this->renderer = $parent->renderer;
        $this->object   = $item;
        
        return $this;
    }
    
    /**
     * parent: Get parent Object.
     * 
     * @access public 
     * @return object
    **/
    public function parent() { return $this->parent; }
    
    /**
     * rules: Define render rules for list item.
     * 
     * @access public
     * @return array
    **/
    public function rules() { show_error('must subclass rules'); }
    
    /**
     * render: Render an Object List Item.
     * 
     * @access public 
     * @param  array  $data,[Optional] Aditional Data.
     * @return string
    **/
    public function render( $data = array() ) {
        
        return $this->renderer->render( 
            $this->parent->uripath, 
            $this->rules(), 
            $this->object, 
            $data 
        );
    }
}
