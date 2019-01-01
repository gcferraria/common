<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renderer_List Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2012 Gonçalo Ferraria
 * @version    1.1 list.php 2013-12-02 15:20 gferraria $
 */

class Renderer_List extends ArrayObject {
    
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
     * @var string, list type.
     * @access public
    **/
    public $type;
    
    /**
     * @var array, List data.
     * @access public
    **/
    public $data = array();
    
    /**
     * @var object, Paged Object
     * @access private 
    **/
    private $paged;

    /**
     * __construct: Renderer List Class Constructor.
     * 
     * @access public
     * @param  object $parent   , [Required] parent object.
     * @param  array  $data     , [Required] data.
     * @param  array  $page     , [Optional] Current Page.
     * @param  array  $page_size, [Optional] Current Page Size.
     * @param  array  $page_size, [Optional] Total Rows Number.
     * @return void
    **/
    public function __construct( $parent, $data, $page, $page_size, $total_rows ) {

        // Set object attributes.
        $this->parent   = $parent;
        $this->renderer = $parent->renderer;
        $this->data     = $data;

        $this->paged = new Paged( $page, $page_size, $total_rows );
        
        parent::__construct( $data );
        
        return $this;
    }
    
    /**
     * first: Get the first list object.
     * 
     * @access public
     * @return object
    **/
    public function first() {
        
        if ( $this->count() > 0 )
            return $this->offsetGet(0); 
        
        return;
    }
    
    /**
     * parent: Get parent Object.
     * 
     * @access public 
     * @return object
    **/
    public function parent() { return $this->parent; }
    
    /**
     * paged: Get Paged Object.
     * 
     * @access public 
     * @return object
    **/
    public function paged() { return $this->paged; }
     
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
     * @param  array  $data, [Optional] Aditional Data.
     * @return string
    **/
    public function render( $data = array() ) {
        
        return $this->renderer->render(
            $this->parent->uripath,
            $this->rules(),
            $this,
            $data
        );
    }
}
