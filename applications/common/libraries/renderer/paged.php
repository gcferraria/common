<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Paged Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2013 Gonçalo Ferraria
 * @version    1.0 paged.php 2013-12-02 16:09 gferraria $
 */

class Paged {
    
    /**
     * @var number, Total Rows number.
     * @access private
     */
    private $total_rows;
    
    /**
     * @var number, Page size.
     * @access private
     */
    private $page_size; 

    /**
     * @var number, Current Page.
     * @access private
     */
    private $page; 
    
    /**
     * __construct: Paged Object Class Constructor.
     * 
     * @access public
     * @param  object $page      , [Required] Current Page.
     * @param  string $page_size , [Required] Page Size.
     * @param  array  $total_rows, [Required] Total rows number.  
     * @return void
    **/
    public function __construct( $page = 1, $page_size = 25, $total_rows = 0 ) {
        $this->total_rows = $total_rows;
        $this->page_size  = $page_size;
        $this->page       = $page;
    }
     
    /**
     * get_total_rows: Return the number of rows.
     * 
     * @access public
     * @return number
     */
    public function get_total_rows() {
        return $this->total_rows;
    } 

    /**
     * get_page: Return the current page.
     * 
     * @access public
     * @return number
     */
    public function get_page() {
        return $this->page;
    } 
    
    /**
     * previous_page: Return the previous page.
     * 
     * @access public
     * @return number
     */
    public function previous_page() {
        return ( $this->has_previous() ) ? $this->page - 1 : $this->page;
    }

    /**
     * next_page: Return the next page.
     * 
     * @access public
     * @return number
     */
    public function next_page() {
        return ( $this->has_next() ) ? $this->page + 1 : $this->page;
    }
     
    /**
     * pages: Return the total number of pages.
     * 
     * @access public
     * @return number
     */
    public function pages() {
        return ceil( $this->total_rows / $this->page_size ) ; 
    } 
    
    /**
     * has_previous: check if existe any previous page.
     * 
     * @access public
     * @return boolean
     */
    public function has_previous() {
        return ( $this->page != 1 ); 
    }
    
    /**
     * has_next: check if existe any next page.
     * 
     * @access public
     * @return boolean
     */
    public function has_next() {
        return ( $this->page != $this->pages() ); 
    }

}
