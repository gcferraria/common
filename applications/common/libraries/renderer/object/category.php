<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Renderer_Category Class
 *
 * @package    Commom
 * @subpackage Libraries
 * @category   Renderer
 * @author     Gonçalo Ferraria
 */
class Renderer_Category extends Renderer_Object {

    /**
     * category database object.
     * 
     * @var object
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
    public function __construct($object, $renderer) 
    {
        if ( is_string($object) ) // Is an uripath.
        { 
            // Get Category Object.
            $category = new Category();
            $category->where( array(
                    'uripath'      => $object,
                    'publish_flag' => 1,
                )
            )->get();

            if ( !$category->exists() ) 
            {
                show_404( "Category not set with uripath: $object" );
            }
        }
        elseif( is_object($object) ) 
        {
            $category = $object;
        }
        else 
        {
            return NULL;
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
    public function parent() 
    {
        // Get Parent Object.
        $parent = $this->object->caller->get();

        if ( $parent->exists() ) {
            return $this->renderer->category( $parent->uripath );
        }

        return NULL;
    }

    /**
     * parents: Get Parent Hierarchy.
     *
     * @access public
     * @return array
    **/
    public function parents() 
    {
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
     * @param  int $page      , [Optional][Default=1] Page of contents.
     * @param  array $options , [Optional] Adicional Options
     * @return array
    **/
    public function categories($page = 1, $options = array()) {

        // Merge global conditions with custom conditions.
        $conditions = array_merge_recursive(array( 'publish_flag' => 1, 'listed' => 1 ), $options);

        // Get Category Options and get current date.
        $options = array_merge_recursive( $this->options(), $options );

        if( $this->object->has_views() ) 
        {
            $views = array();
            foreach ( $this->object->views->get() as $view ) 
            {
                $views[] = $view->dest_category_id;
            }

            $children = new Category();
            $children
                ->where_in( 'id', $views )
                ->where( $conditions )
                ->order_by('weight ASC');
        }
        else 
        {
            $children = $this->object
                ->childrens
                ->where( $conditions )
                ->order_by('weight ASC');
        }

        $data = array();
        if ( $children ) 
        {
            $children->get_paged( 
                $page, ( isset($options['page_size']) ) ? $options['page_size'] : 25
            );

            foreach ( $children as $child ) 
            {
                $child = new Renderer_Category_List_Item( $child, $this->renderer );
                array_push( $data, $child );
            }
        }

        return new Renderer_Category_List( $this, $data, $page, $children->paged->page_size, $children->paged->total_rows );
    }

    /**
     * _call: invoking when call inaccessible methods.
     *        Used for invoking specific contents using views
     * 
     * @access public 
     * @param  string $name, [Required] Method Name
     * @param  array  $args, [Optional] Options 
     * @return void
    **/
    public function __call($name, $args = array()) 
    {
        if ( $this->renderer->debug ) 
        {
            log_message( "debug", "Calling object method '$name' " . implode(', ', $args) );
        }

        // Get Category Options and get current date.
        $options = array_merge_recursive( $this->options(), $args[0] );

        // Call class dynamically 
        $this->renderer->CI->load->helper('inflector');
        $class = ucfirst(singular($name));

        if ( $this->object->has_views() ) 
        {
            $views = array();
            foreach ( $this->object->views->get() as $view ) 
            {
                $views[] = $view->dest_category_id;
            }

            $contents = new $class();
            $contents
                ->where_in_related( 'categories', 'id' , $views )
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end();
        }
        else 
        {
            $contents = new $class();
            $contents
                ->where_related( 'categories', 'id' , $this->object->id )
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end();
        }

        // Order by.
        if ( isset( $options['order_by'] ) ) 
        {
            $contents->order_by( $options['order_by'], isset( $options['order_direction'] ) 
                                                            ? $options['order_direction'] 
                                                            : 'desc' );
        } 
        else 
        {
            $contents->order_by( 'publish_date', 'desc' );
            $contents->order_by( 'weight', 'asc' );
        }

        // Filter by Key Value
        if ( isset( $options['values'] ) && !empty( $options['values'] ) ) 
        {
            if ( is_array( $options['values']) ) 
            {
                foreach ( $options['values'] as $field => $value ) 
                {
                    $contents->where( $field, $value );
                }
            }
        }

        // Search by text
        if ( isset( $options['search_text'] ) && is_array( $options['search_text'] ) && !empty( $options['search_text'] ) ) 
        {
            $contents->group_start();
            foreach( $options['search_text'] as $field => $value ) {
                $contents->or_like( $field, $value );
            }
            $contents->group_end();
        }

        // Page
        $page = isset( $options['page'] ) ? $options['page'] : 1;

        // Limit.
        if ( isset( $options['max_contents'] ) ) 
        {
            $options['page_size'] = $options['max_contents'];
        }

        $page_size = 25;
        if( isset( $options['contents_page_size'] ) )
        {
            if ( is_array( $options['contents_page_size'] ) ) 
            {
                $options['contents_page_size'] = array_pop( $options['contents_page_size'] );
            }

            $page_size = $options['contents_page_size'];
        } 
        elseif ( isset( $options['page_size'] ) ) 
        {
            if ( is_array( $options['page_size'] ) ) 
            {
                $options['page_size'] = array_pop( $options['page_size'] );
            }
            
            $page_size = $options['page_size'];
        }

        // Offset.
        $contents->get_paged( $page, $page_size );

        $data = array();
        if ( $contents ) 
        {
            foreach ( $contents as $content ) 
            {
                $content = $this->renderer->content( $content, $this->uripath );
                $content = new Renderer_Content_List_Item( $this, $content );
                array_push( $data, $content );
            }
        }

        // Create an new Content List.
        return new Renderer_Content_List( $this, $data, $page, $contents->paged->page_size, $contents->paged->total_rows );
    }

    /**
     * contents: Get Category Contents.
     *
     * @access public
     * @param  int $page      , [Optional][Default=1] Page of contents.
     * @param  array $options , [Optional] Adicional Options
     * @return array
    **/
    public function contents( $page = 1, $options = array() ) 
    {
        // Get Category Options and get current date.
        $options = array_merge_recursive( $this->options(), $options );

        if ( $this->object->has_views() ) 
        {
            $views = array();
            foreach ( $this->object->views->get() as $view ) 
            {
                $views[] = $view->dest_category_id;
            }

            $contents = new Content();
            $contents
                ->where_in_related( 'categories', 'id' , $views )
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end();
        }
        else 
        {
            $contents = new Content();
            $contents
                ->where_related( 'categories', 'id' , $this->object->id )
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end();
        }

        // Search Text.
        if ( isset( $options['search_text'] ) && ! empty( $options['search_text'] ) ) 
        {
            $contents
                ->group_start()
                ->or_like( 'name', $options['search_text'] )
                ->or_like( 'keywords', $options['search_text'])
                ->or_where_subquery("EXISTS (
                    SELECT 1 
                      FROM content_value v
                     WHERE v.content_id = categories_category_content.content_id
                       AND v.value LIKE '%". $options['search_text'] ."%'
                )")
                ->or_where_subquery("EXISTS (
                    SELECT 1 
                      FROM translation t
                     WHERE t.content_id = categories_category_content.content_id
                       AND t.value LIKE '%". $options['search_text'] ."%'
                )")
                ->group_end()
            ;
        }

        // Search by Keyword
        if ( isset( $options['keyword'] ) && ! empty( $options['keyword'] ) ) 
        {
            $contents
                ->like( 'keywords', $options['keyword'])
            ;
        }

        // Filter by Key Value
        if ( isset( $options['values'] ) && ! empty( $options['values'] ) ) 
        {
            if ( is_array( $options['values']) ) 
            {
                $contents->group_start();
                $contents->where_related( 'values', 'name', str_replace( '<>', '' , array_keys($options['values'])));
                foreach ( $options['values'] as $field => $value ) 
                {
                    $contents->where_related( 'values', strpos($field, '<>') ? 'value<>' : 'value', $value );
                }
                $contents->group_end();
            }
        }

        // Exclude Categories by uriname
        if ( isset( $options['exclude'] ) && !empty( $options['exclude'] ) && !is_numeric($options['exclude']) ) 
        {
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
        // Exclude Contents by Id
        else if ( isset( $options['exclude'] ) && !empty( $options['exclude'] ) && is_numeric($options['exclude']) ) 
        {
                $contents->not_like('id', $options['exclude']);
        }

        // Order by.
        if ( isset( $options['random_contents'] ) )
            $contents->order_by( 'id', 'random' );

        $contents->order_by( 'publish_date', 'desc' );
        $contents->order_by( 'weight', 'asc' );

        // Limit.
        if ( isset( $options['max_contents'] ) ) 
        {
            $page = 1;
            $options['page_size'] = $options['max_contents'];
        }

        $page_size = 25;
        if( isset( $options['contents_page_size'] ) )
        {
            if ( is_array( $options['contents_page_size'] ) ) 
            {
                $options['contents_page_size'] = array_pop( $options['contents_page_size'] );
            }

            $page_size = $options['contents_page_size'];
        } 
        elseif ( isset( $options['page_size'] ) ) 
        {
            if ( is_array( $options['page_size'] ) ) 
            {
                $options['page_size'] = array_pop( $options['page_size'] );
            }
            
            $page_size = $options['page_size'];
        }

        // Offset.
        $contents->get_paged( $page, $page_size );

        $data = array();
        if ( $contents ) 
        {
            foreach ( $contents as $content ) 
            {
                $content = $this->renderer->content( $content, $this->uripath );
                $content = new Renderer_Content_List_Item( $this, $content );
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
    public function tops( $options = array() ) 
    {
        // Get Category Options and get current date.
        $date    = date("Y-m-d H:i:s");
        $options = array_replace_recursive( $this->options(), $options );

        if( $this->object->has_views() ) 
        {
            $views = array();
            foreach ( $this->object->views->get() as $view )
                $views[] = $view->dest_category_id;

            $contents = new Content();
            $contents
                ->where_in_related( 'categories', 'id' , $views )
                ->include_related_count('counters')
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end()
                ->order_by('counters_count DESC');
        }
        else 
        {
            $contents = $this->object
                ->contents
                ->where( array( 'publish_date <=' => date("Y-m-d H:i:s"), 'publish_flag' => 1 ) )
                ->group_start()
                ->where('disable_date >=', date("Y-m-d H:i:s"))
                ->or_where('disable_date', '0000-00-00 00:00:00')
                ->group_end()
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
        if ( $contents ) 
        {
            foreach ( $contents->get() as $content ) 
            {
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
     * @param  int $limit, [Required] Max number of returned keywords
     * @return array
    **/
    public function keywords( $limit = NULL, $options = array() ) 
    {
        $contents = $this->contents(1, array( 'max_contents' => 999999999 ) );
        $keywords = array();
        foreach ( $contents as $content ) 
        {
            $content = $content->object;
            if ( !empty( $content->keywords ) ) 
            {
                $keywords = array_merge_recursive( $keywords, explode(",", $content->keywords ) ); 
            }
        }

        // Remove duplicates
        $keywords = array_unique($keywords);

        // Limit the number of keywords.
        if ( !is_null( $limit ) ) 
        {
            shuffle($keywords);
            $keywords = array_slice ($keywords,0,$limit);
        }

        if ( isset( $options['keyword'] ) && !empty($options['keyword']) ) 
        {
            array_unshift($keywords, $options['keyword']);
            $keywords = array_unique($keywords);
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
    public function options( $name = null ) 
    {
        // Get Combined Options for current category.
        $options = $this->object->combined_options();

        if ( !is_null( $name ) )
            return ( isset( $options[ $name ] ) ) ? $options[ $name ] : false ;

        return $options;
    }

    /**
     * templates: Get Templates based on supported content types.
     *
     * @access public
     * @return array
    **/
    public function templates() 
    {
        $types = $this->object
            ->content_types
            ->where( array( 'active_flag' => 1 ) )
            ->order_by('name ASC');

        $data = array();
        if ( $types ) 
        {
            foreach ( $types->get() as $type ) 
            {

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
    public function category( $name ) 
    {
        return $this->_child_object( $name, 'category' );
    }

    /**
     * content: Get an child content.
     *
     * @access public
     * @param  string $name, [Required] Content name.
     * @return object
    **/
    public function content( $name ) 
    {
        return $this->_child_object( $name, 'content' );
    }

    /**
     * _child_object: Get an child object (Category or Content).
     *
     * @access public
     * @param  string $name, [Required] Object name.
     * @param  string $type, [Required] Object type.
     * @return object
    **/
    private function _child_object( $name, $type ) 
    {
        $uripath = join( '/', $this->context_path() )  . '/' . $name ;

        return $this->renderer->$type( $uripath );
    }

    /**
     * rules: Define render rules for category.
     *
     * @access public
     * @return array
    **/
    public function rules() 
    {
        return array(
            join( '-', array( '_cat', $this->uriname ) ),
            '_cat',
        );
    }

}