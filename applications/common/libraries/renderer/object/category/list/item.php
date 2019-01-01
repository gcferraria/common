<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Renderer_Category_List_Item Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2014 Gonçalo Ferraria
 * @version    1.0 renderer.php 2014-06-07 15:51 gferraria $
 */

class Renderer_Category_List_Item extends Renderer_Category {
    
    /**
     * rules: Define render rules for content item.
     * 
     * @access public
     * @return array
    **/
    public function rules() {
        $object = $this->object;
        
        return array(
            join( '-', array( '_view-item', $object->uriname ) ),
            '_view-item',
            join( '-', array( '_cats', $object->uriname ) ),
            '_cats-item',
            join( '-', array( '_cat', $object->uriname ) ),
            '_cat',
        );
    }
    
}
