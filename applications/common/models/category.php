<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Category
 *
 * @package    CodeIgniter
 * @subpackage Models
 * @uses       DataMapper
 * @category   Categories
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2015 Gonçalo Ferraria
 * @version    1.1 category.php 2015-01-24 gferraria $
 */

class Category extends DataMapper {

    var $table    = 'category';
    var $has_one  = array(
        'childrens' => array(
            'class'          => 'category',
            'other_field'    => 'childrens',
            'join_other_as'  => 'parent',
            'join_self_as'   => 'category',
        ),
        'related_caller' => array(
            'class'        => 'category',
            'other_field'  => 'parent',
            'join_table'   => 'category',
            'reciprocal'   => TRUE,
        ),
        'caller' => array(
            'class'          => 'category',
            'other_field'    => 'related_caller',
            'reciprocal'     => TRUE,
            'cascade_delete' => FALSE,
        ),
        'administrator' => array(
            'other_field'   => 'categories',
            'join_table'    => 'category',
            'join_other_as' => 'creator',
        ),
        'website' => array(
            'class'         => 'settings_website',
            'other_field'   => 'category',
            'join_other_as' => 'website',
        ),
    );

    var $has_many = array(
        'content_types' => array(
            'class'          => 'content_type',
            'other_field'    => 'categories',
            'join_self_as'   => 'category',
            'join_other_as'  => 'content_type',
            'cascade_delete' => TRUE,
        ),
        'options'  => array(
            'class'          => 'category_option',
            'other_field'    => 'category',
            'join_self_as'   => 'category',
            'cascade_delete' => TRUE,
        ),
        'contents' => array(
            'class'          => 'content',
            'other_field'    => 'categories',
            'join_self_as'   => 'category',
            'join_other_as'  => 'content',
            'cascade_delete' => TRUE,
        ),
        'views' => array(
            'class'          => 'category_view',
            'other_field'    => 'category',
            'join_self_as'   => 'category',
            'join_other_as'  => 'view',
            'cascade_delete' => TRUE,
        ),
        'translations' => array(
            'class'          => 'translation',
            'other_field'    => 'category',
            'join_self_as'   => 'category',
            'join_other_as'  => 'translation',
            'cascade_delete' => TRUE,
        )
    );

    public $validation = array(
        'name' => array(
            'type'         => 'text',
            'rules'        => array('required'),
            'translatable' => TRUE,
        ),
        'uriname' => array(
            'type'  => 'text',
            'rules' => array(
                'required',
                'unique_pair' => 'parent_id',
                'uriname'
            )
        ),
		'description' => array(
			'type'         => 'textarea',
		    'translatable' => TRUE,
        ),
        'content_types' => array(
            'type' => 'multiselect',
        ),
        'views' => array(
            'type' => 'category',
        ),
        'weight' => array(
            'type'  => 'spinner',
            'rules' => array('numeric'),
            'value' => 0,
        ),
        'publish_flag' => array(
            'type'  => 'radiogroup',
            'value' => '1',
        ),
        'listed' => array(
            'type'  => 'radiogroup',
            'value' => '0',
        ),
        'exposed' => array(
            'type'  => 'radiogroup',
            'value' => '1',
        ),
    );

    /**
     * parents: Gets hierarchy of parents including me.
     *
     * @access public
     * @return array
    **/
    public function parents() {
        return $this->_parents();
    }

    /**
     * _parents: Gets hierarchy of parents including me.
     *
     * @access private
     * @return array
    **/
    private function _parents() {
        $parents = array();
        $parent  = $this->caller->get();

        if ( $parent->exists() )
            $parents = array_merge( $parent->_parents(), $parents );

        array_push( $parents, $this );

        return $parents;
    }

    /**
     * path_uriname_array: Get array with urinames of my path.
     *
     * @access public
     * @return array
    **/
    public function path_uriname_array() {
        $path = array();
        foreach ( $this->parents() as $parent )
            $path[] = $parent->uriname;

        return $path;
    }

    /**
     * path_name_array: Get array with names of my path.
     *
     * @access public
     * @return array
    **/
    public function path_name_array() {
        $path = array();
        foreach ( $this->parents() as $parent )
            $path[] = $parent->name;

        return $path;
    }

    /**
     * path: Get path.
     *
     * @access public
     * @return string
    **/
    public function path() {
        $path_array = $this->path_uriname_array();
        $path       = implode('/', $path_array );

        if ( sizeof( $path_array ) > 0 )
            $path = '/' . $path . '/';

        return $path;
    }

    /**
     * save: Create an uriname based on name if this name is empty and insert/update
     *       the content types and category views associated at this category.
     *
     * @access public
     * @param  mixed  $object,  [Optional] object to save or array of objects to save.
     * @param  string $relation,[Optional] string to save the object as a specific relationship.
     * @return bool Success or Failure of the validation and save.
     **/
    public function save( $object = '', $relation = '' ) {

        if( empty( $object ) )
            return parent::save( $object, $relation );

        // Start Transaction
        $this->trans_begin();

        if( is_array($object) && !empty($object) ) {

           if ( isset ( $object['views'] ) ) {
                $views = $object['views'];
                unset ( $object['views'] );
            }

            // If exists Content Types Delete All.
            if ( $this->content_types->count() > 0 ) {
                $objects = $this->content_types->get();
                $this->delete( $objects->all, 'content_types' );
            }

            // Associate Content Types to Category.
            if ( $ids = $object['content_types'] ) {
                if ( !empty( $ids ) ) {

                    $content_types = array();
                    foreach ( $ids as $content_type ) {
                        $new = new Content_Type();
                        $new->get_by_id( $content_type );

                        if ( !$new->exists() )
                            continue;

                        array_push( $content_types, $new );
                    }

                    $object['content_types'] = $content_types;
                }
            }

            parent::save( $object, $relation );

            // Fisrt Delete All Categories Views associated at this category.
            foreach ( $this->views->get() as $view ) {
                $view->delete();
            }

            // Second create the relations.
            if ( !empty ( $views ) ) {
                foreach ( $views as $id ) {
                    $category = new Category_View();
                    $category->category_id = $this->id;
                    $category->dest_category_id = $id;

                    $category->save();
                }
            }
        }

        // Check status of transaction.
        if ( $this->trans_status() === FALSE ) {
            // Transaction failed, rollback.
            $this->trans_rollback();

            return FALSE;
        }
        else {
            // Transaction successful, commit.
            $this->trans_commit();

            return TRUE;
        }
    }

    /**
     * delete: Delete current Category your contents and childrens.
     *
     * @access public
     * @param  mixed $object If specified, delete the relationship to the object or array of objects.
     * @param  string $related_field Can be used to specify which relationship to delete.
     * @return bool Success or Failure of the delete.
    **/
    public function delete( $object = '', $related_field = '' ) {

        if( is_array( $object ) )
            return parent::delete( $object, $related_field );

        // Start Transaction
        $this->trans_begin();

        // Delete Your Contents.
        foreach ( $this->contents->get() as $content ) {
            $content->delete( array('category' => $this->id ) );
        }

        // Delete Childrens.
        foreach ( $this->childrens->get() as $children ) {
            $children->delete();
        }

        parent::delete();

        // Check status of transaction.
        if ( $this->trans_status() === FALSE ) {
            // Transaction failed, rollback.
            $this->trans_rollback();

            return FALSE;
        }
        else {
            // Transaction successful, commit.
            $this->trans_commit();

            return TRUE;
        }
    }

    /**
     * inherited_options: Get inherited Options.
     *
     * @access public
     * @return array
    **/
    public function inherited_options() {

        // Get my parent categories.
        $parents = $this->parents();

        // Remove last position of array, corresponding to me.
        array_pop( $parents );

        $options = array();
        foreach ( $parents as $parent ) {

            // Get only inheritable options.
            $inherited = $parent->options->get_where(
                    array( 'inheritable' => 1 )
                );

            foreach ( $inherited as $option )
                $options[ $option->name ] = $option->value;
        }

        return $options;
    }

    /**
     * own_options: Get Own Options.
     *
     * @access public
     * @return array
    **/
    public function own_options() {
        $options = array();
        foreach ( $this->options->get() as $option )
            $options[ $option->name ] = $option->value;

        return $options;
    }

    /**
     * combined_options: Get Combined Options. Include the combination beetween
     *                   my options and my parent options.
     *
     * @access public
     * @return array
    **/
    public function combined_options() {
        return array_merge_recursive(
            $this->inherited_options(),
            $this->own_options()
        );
    }

    /**
     * has_views: Check if this categories has category views.
     *
     * @access public
     * @return boolean
    **/
    public function has_views() {
        return ( $this->views->count() > 0 );
    }

    /**
     * languages: Get Languages for this category
     *
     * @access public
     * @return array
     */
    public function languages() {
        $languages = new I18n_Language();
        $languages->query("
            SELECT l.*
              FROM i18n_language l,
                   i18n_language_settings_website lw,
                   settings_website w,
                   category c
             WHERE w.category_id = c.id
               AND w.id = lw.settings_website_id
               AND lw.i18n_language_id = l.id
               AND '".$this->uripath."' LIKE concat( '','%', c.uripath,'%','')
        ");

        $lang = array();
        foreach ( $languages as $language )
            $lang[] = $language;

        return $lang;
    }

    /**
     * translatable_fields: Get the translatable fields
     *
     * @access public
     * @return array
     */
    public function translatable_fields() {
        $fields = array();
        foreach ( $this->validation as $name => $value ) {
            if ( isset( $value['translatable'] ) )
                $fields[ $name ] = $value;
        }

        return $fields;
    }

    /**
     * translatable_values: Get the translatable values
     *
     * @access public
     * @param  $language, Language to get values.
     * @return array
     */
    public function translatable_values( $language ) {
        $values = array();
        foreach ( $this->translations->where('language_id', $language->id )->get() as $value )
            $values[ $value->name ] = $value->value;

        return $values;
    }

    /**
     * __to_array: Get Category attribues as array
     *
     * @access public
     * @param  string $language, Language Code for get Translations
     * @return array
    **/
    public function __to_array( $language ) {

        // Define fields to be appear in data.
        $fields = array( 'id', 'name', 'uriname', 'description', 'weight', 'publish_flag', 'listed', 'exposed', 'last_update_date' );

        $data = array();
        foreach( $fields as $field )
            $data[ $field ] = $this->$field;

        if ( !empty( $language ) ) {
            foreach ( $this->translatable_values( $language ) as $key => $value) {
                $data[ $key ] = $value;
            }
        }

        return $data;
    }
}

/* End of file category.php */
/* Location: ./applications/common/models/category.php */