<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renderer_Template Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2013 Gonçalo Ferraria
 * @version    1.0 template.php 2013-03-06 22:30 gferraria $
 */

class Renderer_Template extends Renderer_Object {
    
    /**
     * @var string, content type database object.
     * @access public
    **/
    public $object;
    
    /**
     * __construct: Renderer Template Class Constructor.
     *              
     * @access public
     * @param  mixed  $object  , [Required] Content Type Object.
     * @param  string $parent  , [Required] Parent Uripath.
     * @param  object $renderer, [Required] Renderer Object.
     * @return void
    **/
    public function __construct( $object, $parent, $renderer ) {
        
        // Call Parent Constructor.
        parent::__construct(
            $renderer,
            $parent
        );
        
        $this->type   = 'template';
        $this->object = &$object;
        
        return $this;
    }
    
    /**
     * parent: Get Parent Category.
     * 
     * @access public
     * @return object
    **/
    public function parent() {
        
        $parent = $this->renderer->category( $this->uripath );
        
        if ( $parent )
            return $parent;
        
        return;
    }
    
    /**
     * rules: Define render rules for template.
     * 
     * @access public
     * @return array
    **/
    public function rules() {
        $object = $this->object;
        
        return array(
            join( '-', array( '_template', $object->uriname ) ),
            '_template',
        );
    }
    
}
