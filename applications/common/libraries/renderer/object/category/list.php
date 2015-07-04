<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Category_List Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2014 Gonçalo Ferraria
 * @version    1.0 list.php 2014-06-07 15:49 gferraria $
 */

class Renderer_Category_List extends Renderer_List {
    
    /**
     * __construct: Renderer Category List Class Constructor.
     * 
     * @access public
     * @param  object $parent   , [Required] parent Object.
     * @param  array  $data     , [Required] data.
     * @param  array  $page     , [Optional] Current Page.
     * @param  array  $page_size, [Optional] Current Page Size.
     * @param  array  $page_size, [Optional] Total Rows Number.
     * @return void
    **/
    public function __construct( $parent, $data, $page, $page_size, $total_rows ) {
        parent::__construct( $parent, $data, $page, $page_size, $total_rows );
        
        // Set List Type.
        $this->type = 'category';
    }
    
    /**
     * rules: Define render rules for object.
     * 
     * @access public
     * @return array
    **/
    public function rules() { 
        return array(
            join( '-', array( '_cats', $this->parent()->uriname ) ),
            '_cats',
        );
    }
    
}

/* End of file list.php */
/* Location: ../applications/common/libraries/renderer/category/content/list.php */
