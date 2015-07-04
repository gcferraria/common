<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Content
 *
 * @package    CodeIgniter
 * @subpackage Models
 * @uses       DataMapper
 * @category   Categories
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2015 Gonçalo Ferraria
 * @version    1.1 category.php 2015-01-24 gferraria $
 */

class Content extends DataMapper {

    var $table    = 'content';
    var $has_one  = array(
        'content_type' => array(
            'other_field' => 'contents'
        ),
        'administrator' => array(
            'other_field'   => 'contents',
            'join_other_as' => 'creator',
            'join_table'    => 'content',
        ),
    );
    var $has_many = array(
        'categories' => array(
            'class'          => 'category',
            'other_field'    => 'contents',
            'join_self_as'   => 'content',
            'join_other_as'  => 'category',
            'cascade_delete' => TRUE,
        ),
        'values' => array(
            'class'          => 'content_value',
            'other_field'    => 'content',
            'join_self_as'   => 'content',
            'join_other_as'  => 'content_value',
            'cascade_delete' => TRUE,
        ),
        'counters' => array(
            'class'          => 'content_counter',
            'cascade_delete' => TRUE,
        ),
        'translations' => array(
            'class'          => 'translation',
            'other_field'    => 'content',
            'join_self_as'   => 'content',
            'join_other_as'  => 'translation',
            'cascade_delete' => TRUE,
        )
    );

    public $validation = array(
        'content_type_id' => array(
            'type'     => 'hidden',
            'position' => '-3',
        ),
        'name' => array(
            'type'     => 'text',
            'rules'    => array('required'),
            'position' => '-2',
        ),
        'publish_date' => array(
            'type'     => 'datetime',
            'rules'    => array('required'),
            'position' => '9995',
        ),
        'disable_date' => array(
            'type'  => 'datetime',
            'rules' => array(),
            'position' => '9996',
        ),
        'weight' => array(
            'type'     => 'spinner',
            'rules'    => array('numeric'),
            'value'    => 0,
            'position' => '9997',
        ),
        'publish_flag' => array(
            'type'     => 'radiogroup',
            'rules'    => array('required'),
            'value'    => '1',
            'position' => '9998',
        ),
        'keywords' => array(
            'type' => 'tag',
            'position' => '9999',
        ),
        'categories' => array(
            'type'     => 'category',
            'position' => '10000',
        ),
    );

    /**
    * save: Create an uriname based on name if this name is empty and insert/update
    *       the content types associated at this category.
    *
    * @access public
    * @param  mixed  $object,  [Optional] object to save or array of objects to save.
    * @param  string $relation,[Optional] string to save the object as a specific relationship.
    * @return bool Success or Failure of the validation and save.
    **/
    public function save( $object = '', $relation = '' ) {

        // Start Transaction
        $this->trans_begin();

        $values = array();
        if ( is_array( $object ) && !empty ( $object ) ) {

            if ( isset ( $object['values'] ) ) {
                $values = $object['values'];
                unset ( $object['values'] );
            }

            if ( $ids = $object['categories'] ) {

                // If exists Categories Delete All.
                if ( $this->categories->count() > 0 ) {
                    $objects = $this->categories->get();
                    $this->delete( $objects->all, 'categories' );
                }

                // Associate Categories to Content.
                if ( !empty( $ids ) ) {

                    $categories = array();
                    foreach ( $ids as $id ) {
                        $category = new Category();
                        $category->get_by_id( $id );

                        if ( !$category->exists() )
                            continue;

                        array_push( $categories, $category );
                    }

                    $object['categories'] = $categories;
                }
            }
        }

        parent::save( $object, $relation );

        if ( !empty ( $values ) ) {

            # Get Content Type Object.
            $content_type = $this->content_type->get();

            // Foreach Content Type Field insert content value.
            $fields = $content_type->content_type_fields;
            foreach ( $fields->get() as $field ) {

                // Initialize new Content Value Object.
                $content_value = new Content_Value();

                // Check if content value for this content already exists.
                $content_value->where( array(
                        'name'                  => $field->name,
                        'content_id'            => $this->id,
                        'content_type_field_id' => $field->id,
                    )
                )->get();

                if ( $content_value->exists() ) { // Update Content Value.

                    // Update Value.
                    $content_value->value = $values[ $field->name ];
                    $content_value->save();
                }
                else { // Add Content Value.

                    // Set content Value Attributes.
                    $content_value->name                  = $field->name;
                    $content_value->value                 = $values[ $field->name ];
                    $content_value->content_id            = $this->id;
                    $content_value->content_type_field_id = $field->id;

                    $content_value->save();
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
     * Delete
     *
     * Deletes the current record.
     * If object is supplied, deletes relations between this object and the supplied object(s).
     *
     * @param   mixed $object If specified, delete the relationship to the object or array of objects.
     * @param   string $related_field Can be used to specify which relationship to delete.
     * @return  bool Success or Failure of the delete.
     */
    public function delete( $object = '', $related_field = '' ) {

        // Start Transaction
        $this->trans_begin();

        if ( is_array( $object ) && !empty ( $object ) ) {

            if ( isset ( $object['category'] ) ) {

                // Get Current Category Id.
                $id = $object['category'];
                unset ( $object['category'] );

                // checks if there are more categories associated with this content.
                if ( $this->categories->count() > 1 ) {

                    // deletes only the relationship with the current category.
                    $category = $this->categories->where(
                            array( 'category_id' => $id )
                        )->get();

                    $object['categories'] = $category;
                }
                else {
                    $object = '';
                }
            }
        }

        parent::delete( $object, $related_field );

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
     * as_name_value_array: Gets content values in format name as value.
     *
     * @access public
     * @return array
    **/
    public function as_name_value_array() {

        $values = array();
        foreach ( $this->values->get() as $value ) {
            $values[ $value->name ] = $value->value;
        }

        return $values;
    }

    /**
     * status: Gets Status of Content based on publish_flag,
     *              publish_date and disable_date.
     *
     * Codes:
     * -1 => disable,
     * 0  => pending,
     * 1  => enable
     *
     * @access public
     * @return int
    **/
    public function status() {

        if ( $this->publish_flag == 0 )
            return -1;
        else {

            if (
                $this->disable_date <= date('Y-m-d H:i:s') &&
                $this->disable_date != '0000-00-00 00:00:00'
            ) {
                return -1;
            }
            elseif ( date('Y-m-d H:i:s') < $this->publish_date ) {
                return 0;
            }
            else {
                return 1;
            }
        }
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
    * __to_array: Get Content attribues and your values as array.
    *
    * @access public
    * @param  string $language, Language Code for get Translations
    * @return array
   **/
   public function __to_array( $language = NULL ) {

       // Define base fields to be appear in data.
       $fields = array( 'id', 'name', 'uripath', 'publish_date', 'disable_date', 'keywords' );

       $data = array();
       foreach( $fields as $field ) {
           $data[ $field ] = $this->$field;
       }

       $data = array_merge_recursive( $data, $this->as_name_value_array() );

       if ( !empty( $language ) ) {
            foreach ( $this->translatable_values( $language ) as $key => $value) {
                $data[ $key ] = $value;
            }
        }

       return $data;
   }

}

/* End of file content.php */
/* Location: ./applications/common/models/content.php */