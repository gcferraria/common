<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Content Class
 *
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2012 Gonçalo Ferraria
 * @version    1.0 content.php 2012-12-15 19:30 gferraria $
 */

class Renderer_Content extends Renderer_Object {

    /**
     * @var string, content database object.
     * @access public
    **/
    public $object;

    /**
     * @var string, context uripath.
     * @access public
    **/
    public $context_uripath;

    /**
     * __construct: Renderer Content Class Constructor.
     *              Initialize Content Object.
     *
     * @access public
     * @param  mixed  $object  , [Required] Content identifier or Content Object.
     * @param  string $parent  , [Required] Parent Uripath.
     * @param  object $renderer, [Required] Renderer Object.
     * @return void
    **/
    public function __construct( $object, $parent, $renderer ) {

        if ( is_int( $object ) ) { // Is an Id

            $date    = date("Y-m-d H:i:s");
            $content = new Content();
            $content
            ->where( array(
                    'id'              => $object,
                    'publish_date <=' => $date,
                    'publish_flag'    => 1,
                )
            )
            ->get();

            if ( !$content->exists() ) {
                log_message(
                    'error',
                    'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                    "Content not set with id $object"
                );

                show_404( "Content not set with id: $object" );
            }

            // Keep parent uripath.
            $content->uripath = $parent;
        }
        elseif( is_object( $object ) )
            $content = $object;
        else
            return;

        // Keep context uripath.
        $this->context_uripath = $parent;

        // Call Parent Constructor.
        parent::__construct(
            $renderer,
            $parent,
            $content->__to_array( ( $renderer->i18n ) ? $renderer->get_language() : NULL )
        );

        // Associate object content.
        $this->object = &$content;
        $this->type   = 'content';
        
        // If Context Category does not exists, let's try find the first category for this content
        $category = new Category();
        $category
            ->where( array(
                'uripath'      => $this->object->uripath, 
                'publish_flag' => 1,
            ))
            ->like('uripath', $this->renderer->base_category())
        ;

        if( !$category->exists() ) {
            $categories = $this->categories();
            if ( !empty( $categories ) ) {
                $this->context_uripath = $categories[0]->uripath; 
            }
        }

        return $this;
    }

    /**
     * parent: Get Parent Category.
     *
     * @access public
     * @return object
    **/
    public function parent() {
        $parent = $this->renderer->category( (!empty($this->context_uripath)) ? $this->context_uripath : $this->uripath );

        if ( $parent )
            return $parent;

        return;
    }

    /**
     * categories: Get Children Categories.
     *
     * @access public
     * @return array
    **/
    public function categories() {

        $data     = array();
        $children = $this->object
            ->distinct()
            ->categories
            ->where('publish_flag', 1)
            ->like('uripath', $this->renderer->base_category())
            ->not_like('uriname', 'destaques')
            ->order_by('weight ASC');

        if ( $children ) {
            foreach ( $children->get() as $child ) {

                $child = $this->renderer->category( $child->uripath );
                array_push( $data, $child );
            }
        }

        return $data;
    }

    /**
     * tops: Get Counter Objects
     * 
     * @access public
     * @return int
     *
    **/
    public function tops() {
        $counters = $this->object
            ->counters
            ->count();

        return $counters;
    }

    /**
     * field: Get an content field.
     *
     * @access public
     * @param  string $name, [Required] Field name.
     * @return object
    **/
    public function field( $name ) {

        $value = $this->object->values
            ->where( 'name', $name )
            ->limit(1)
            ->get();

        if ( $value->exists() ) {
            return (object) array(
                'name'  => $value->name,
                'value' => $value->value,
            );
        }

        return false;
    }

    /**
     * rules: Define render rules for content.
     *
     * @access public
     * @return array
    **/
    public function rules() {
        $object = $this->object;

        return array(
            join( '-', array( '_cont', $object->content_type->get()->uriname ) ),
            '_cont',
        );
    }

}