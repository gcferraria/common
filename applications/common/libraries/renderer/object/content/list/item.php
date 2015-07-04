<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Content_List_Item Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2013 Gonçalo Ferraria
 * @version    1.0 renderer.php 2013-01-10 21:41 gferraria $
 */

class Renderer_Content_List_Item extends Renderer_List_Item {
    
    /**
     * __construct: Renderer Content List Item Class Constructor.
     * 
     * @access public
     * @param  object $parent, [Required] parent object.
     * @param  object $item  , [Required] object list item.
     * @return void
    **/
    public function __construct( $parent, $item ) {
        parent::__construct( $parent, $item );
        
        // Set List Type.
        $this->type = 'content';
    }
    
    /**
     * rules: Define render rules for content item.
     * 
     * @access public
     * @return array
    **/
    public function rules() {
        $object = $this->object;
        
        return array(
            join( '-', array( '_conts', $object->object->content_type->get()->uriname ) ),
            '_conts-item',
        );
    }
    
}

/* End of file item.php */
/* Location: ./applications/common/libraries/renderer/object/content/list/item.php */
