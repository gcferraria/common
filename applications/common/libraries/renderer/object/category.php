<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Category Class
 *
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2012 - 2018 Gonçalo Ferraria
 * @version    1.5 category.php 2018-04-22 gferraria $
 */

class Renderer_Category extends Renderer_Object {

    /**
     * @var string, category database object.
     * @access public
    **/
    public $object;

    /**
     * __construct: Renderer Category Class Constructor.
     *              Initialize Category Object.
     *
     * @access public
     * @param  mixed  $object  , [Required] Category uripath or Category Object.
     * @param  object $renderer, [Required] Renderer Object.
     * @return void
    **/
    public function __construct( $object, $renderer ) {

        if ( is_string( $object ) ) { // Is an uripath.
            // Get Category Object.
            $category = new Category();
            $category->where( array(
                        'uripath'      => $object,
                        'publish_flag' => 1,
                    )
                )->get();

            if ( !$category->exists() ) {
                show_404( "Category not set with uripath: $object" );
            }
        }
        elseif( is_object( $object ) ) {
            $category = $object;
        }
        else {
            return;
        }

        // Call Parent Constructor.
        parent::__construct(
            $renderer,
            $category->uripath,
            $category->__to_array( ( $renderer->i18n ) ? $renderer->get_language() : NULL )
        );

        // Associate object category.
        $this->object = $category;
        $this->type   = 'category';

        return $this;
    }

    /**
     * parent: Get Parent Category.
     *
     * @access public
     * @return object
    **/
    public function parent() {

        // Get Parent Object.
        $parent = $this->object->caller->get();

        if ( $parent->exists() )
            return $this->renderer->category( $parent->uripath );

        return;
    }

    /**
     * parents: Get Parent Hierarchy.
     *
     * @access public
     * @return array
    **/
    public function parents() {
        $parents = array();

        foreach( $this->object->parents() as $parent ) {
            $parent = $this->renderer->category( $parent );
            array_push( $parents, $parent );
        }

        return $parents;
    }

    /**
     * categories: Get Children Categories.
     *
     * @access public
     * @param  $options, Adicional options
     * @return array
    **/
    public function categories( $options = array() ) {

        $data = array();
        $conditions = array_merge_recursive(array( 'publish_flag' => 1, 'listed' => 1 ), $options);

        if( $this->object->has_views() ) {
            $views = array();
            foreach ( $this->object->views->get() as $view ) {
                $views[] = $view->dest_category_id;
            }

            $children = new Category();
            $children
                ->where_in( 'id', $views )
                ->where( $conditions )
                ->order_by('weight ASC');
        }
        else {
            $children = $this->object
                ->childrens
                ->where( $conditions )
                ->order_by('weight ASC');
        }

        if ( $children ) {
            foreach ( $children->get() as $child ) {

                if( !$this->renderer->legacy )
                    $child = new Renderer_Category_List_Item( $child, $this->renderer );

                array_push( $data, $child );
            }
        }

        if( !$this->renderer->legacy )
            return new Renderer_Category_List( $this, $data, null, null, null );

        return $data;
    }

    /**
     * contents: Get Category Contents.
     *
     * @access public
     * @param  int $page      , [Optional][Default=1] Page of contents.
     * @param  array $options , [Optional] Adicional Options
     * @return array
    **/
    public function contents( $page = 1, $options = array() ) {

        // Get Category Options and get current date.
        $options = array_merge_recursive( $this->options(), $options );

        if( $this->object->has_views() ) {

            $views = array();
            foreach ( $this->object->views->get() as $view ) {
                $views[] = $view->dest_category_id;
            }

            $contents = new Content();
            $contents
                ->where_in_related( 'categories', 'id' , $views )
                ->where( array(
                        'publish_date <=' => date("Y-m-d H:i:s"),
                        'disable_date <'  => date("Y-m-d H:i:s"),
                        'publish_flag'    => 1,
                    )
                );
        }
        else {
            $contents = $this->object->contents
                ->where( array(
                        'publish_date <=' => date("Y-m-d H:i:s"),
                        'disable_date <'  => date("Y-m-d H:i:s"),
                        'publish_flag'    => 1,
                    )
                );
        }

        // Search Text.
        if ( isset( $options['search_text'] ) && ! empty( $options['search_text'] ) ) {
            $contents->distinct(true)
                     ->where_in_related( 'values', 'name' , array('lead','title','description') )
                     ->like_related( 'values', 'value', $options['search_text'] )
                     ->or_like( 'name', $options['search_text'] )
                     ->or_like( 'keywords', $options['search_text']);
        }

        // Exclude Categories 
        if ( isset( $options['exclude'] ) && ! empty( $options['exclude'] ) ) {
            $contents->where_subquery("NOT EXISTS ( 
                    select  1 
                      from  category_content cc
                        ,   category c
                     where  1=1 
                       and  cc.content_id = categories_category_content.content_id
                       and  cc.category_id = c.id
                       and  c.uriname like '%". $options['exclude']."%'
                       and  c.uripath like '%". $this->renderer->base_category()."%'
                )"
            );
        }

        // Order by.
        if ( isset( $options['random_contents'] ) )
            $contents->order_by( 'id', 'random' );

        $contents->order_by( 'weight', 'asc' );
        $contents->order_by( 'publish_date', 'desc' );

        // Limit.
        if ( isset( $options['max_contents'] ) ) {
            $page = 1;
            $options['page_size'] = $options['max_contents'];
        }

        // Offset.
        $contents->get_paged(
                $page,
                isset( $options['page_size'] ) ? $options['page_size'] : 25
            );

        $data = array();
        if ( $contents ) {
            foreach ( $contents as $content ) {
                $content = $this->renderer->content( $content, $this->uripath );
                $content = new Renderer_Content_List_Item( $this, $content );

                if ( isset( $options['results'] ) && ! empty( $options['results'] ) ) {
                    $now     = new DateTime( date('Y-m-d') );
                    $sDate   = new DateTime( date('Y-m-d', strtotime( $content->object->start_date ) ) );
                    $eDate   = new DateTime( date('Y-m-d', strtotime( $content->object->end_date   ) ) );
                    
                    if ( 
                        ( $options['results'] == 'past'    and $eDate > $now ) or 
                        ( $options['results'] == 'future'  and $sDate < $now ) or
                        ( $options['results'] == 'current' and !( $now >= $sDate and $now <= $eDate )  )
                    ) {
                        continue; 
                    }
                }

                if ( isset( $options['date_from'] ) && ! empty( $options['date_from'] ) ) { 
                    $now      = new DateTime( date('Y-m-d') );
                    $sDate    = new DateTime( date('Y-m-d', strtotime( $content->object->start_date ) ) );
                    $eDate    = new DateTime( date('Y-m-d', strtotime( $content->object->end_date   ) ) );
                    $dateFrom = new DateTime( date('Y-m-d', strtotime( $options['date_from']        ) ) );
                    $dateTo   = new DateTime( date('Y-m-d', strtotime( $options['date_to']          ) ) );

                    if ( !( ( $sDate <= $dateTo ) && ($dateFrom <= $eDate) ) ) {
                        continue;
                    }
                }

                array_push( $data, $content );
            }
        }

        // Create an new Content List.
        return new Renderer_Content_List( $this, $data, $page, $contents->paged->page_size, $contents->paged->total_rows );
    }

     /**
     * tops: Get Top Contents.
     *
     * @access public
     * @param  array $options, [Optional] Options to filter results.
     * @return array
    **/
    public function tops( $options = array() ) {

        // Get Category Options and get current date.
        $date    = date("Y-m-d H:i:s");
        $options = array_replace_recursive( $this->options(), $options );

        if( $this->object->has_views() ) {

            $views = array();
            foreach ( $this->object->views->get() as $view )
                $views[] = $view->dest_category_id;

            $contents = new Content();
            $contents
                ->where_in_related( 'categories', 'id' , $views )
                ->include_related_count('counters')
                ->where( array(
                        'publish_date <=' => $date,
                        'disable_date <'  => $date,
                        'publish_flag'    => 1,
                    )
                )
                ->order_by('counters_count DESC');
        }
        else {
            $contents = $this->object
                ->contents
                ->where( array(
                        'publish_date <=' => $date,
                        'disable_date <'  => $date,
                        'publish_flag'    => 1,
                    )
                )
                ->include_related_count('counters')
                ->order_by('counters_count DESC');
        }

        // Min Date
        if( isset( $options['min_date'] ) )
            $contents->where('publish_date >= ', $options['min_date'] );

        // Limit
        if ( isset( $options['max_contents'] ) && is_numeric( intval($options['max_contents']) ) )
            $contents->limit( (int)$options['max_contents'] );

        $data = array();
        if ( $contents ) {
            foreach ( $contents->get() as $content ) {

                $content = $this->renderer->content( $content, $this->uripath );
                $content = new Renderer_Content_List_Item( $this, $content );
                array_push( $data, $content );
            }
        }

        // Create an new Content List.
        return new Renderer_Content_List( $this, $data, null, null, null );
    }

    /**
     * keywords: Gets contents keywords of the category 
     * 
     * @access public
     * TODO: Replace Memory Calculation by Database 
     * @param int $limit, [Required] Max number of returned keywords
     * @return array
     */
    public function keywords( $limit = NULL ) {
        $contents = $this->contents(1, array( 'max_contents' => 999999999 ) );
        $keywords = array();
        foreach ( $contents as $content ) {
            $content = $content->object;
            if ( !empty( $content->keywords ) ) {
                $keywords = array_merge_recursive( $keywords, explode(",", $content->keywords ) ); 
            }
        }

        // Remove duplicates
        $keywords = array_unique($keywords);

        // Limit the number of keywords.
        if ( !is_null( $limit ) ) {
            $keywords = array_slice ($keywords,0,$limit);
        }

        return $keywords;
    }

    /**
     * options: Get Category Options.
     *
     * @access public
     * @param  string $name, [Optional] Option name.
     * @return array
    **/
    public function options( $name = null ) {

        // Get Combined Options for current category.
        $options = $this->object->combined_options();

        if ( !is_null( $name ) )
            return ( isset( $options[ $name ] ) ) ? $options[ $name ] : false ;

        return $options;
    }

    /**
     * categories: Get Templates based on supported content types.
     *
     * @access public
     * @return array
    **/
    public function templates() {

        $types = $this->object
            ->content_types
            ->where( array( 'active_flag' => 1 ) )
            ->order_by('name ASC');

        $data = array();
        if ( $types ) {
            foreach ( $types->get() as $type ) {

                $type = new Renderer_Template( $type, $this->uripath, $this->renderer );
                array_push( $data, $type );
            }
        }

        return $data;
    }

    /**
     * category: Get an child category.
     *
     * @access public
     * @param  string $name, [Required] Category name.
     * @return object
    **/
    public function category( $name ) {
        return $this->child_object( $name, 'category' );
    }

    /**
     * content: Get an child content.
     *
     * @access public
     * @param  string $name, [Required] Content name.
     * @return object
    **/
    public function content( $name ) {
        return $this->child_object( $name, 'content' );
    }

    /**
     * child_object: Get an child object (Category or Content).
     *
     * @access public
     * @param  string $name, [Required] Object name.
     * @param  string $type, [Required] Object type.
     * @return object
    **/
    private function child_object( $name, $type ) {
        $uripath = join( '/', $this->context_path() )  . '/' . $name ;

        return $this->renderer->$type( $uripath );
    }

    /**
     * rules: Define render rules for category.
     *
     * @access public
     * @return array
    **/
    public function rules() {

        return array(
            join( '-', array( '_cat', $this->uriname ) ),
            '_cat',
        );
    }

}