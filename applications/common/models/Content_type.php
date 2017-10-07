<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content_Type extends DataMapper 
{
    var $table    = 'content_type';
    var $has_many = array(
        'categories' => array(
            'class'          => 'category',
            'other_field'    => 'content_types',
            'join_self_as'   => 'content_type',
            'join_other_as'  => 'category',
            'cascade_delete' => TRUE,
        ),
       'contents' => array(
            'class'          => 'content',
            'other_field'    => 'content_type',
            'join_self_as'   => 'content_type',
            'join_other_as'  => 'contents',
            'cascade_delete' => TRUE,
        ),
        'content_type_fields' => array(
            'class'          => 'content_type_field',
            'other_field'    => 'content_type',
            'join_other_as'  => 'content_type_fields',
            'cascade_delete' => TRUE,
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array('required','unique'),
        ),
        'uriname' => array(
            'type'  => 'text',
            'rules' => array('required','unique','spaces','uriname')
        ),
        'active_flag' => array(
            'type'   => 'radiogroup',
            'rules'  => array('required'),
            'value'  => '1',
        ),
    );

    /**
     * Delete
     *
     * Deletes the current record.
     * If object is supplied, deletes relations between this object and the supplied object(s).
     *
     * @param   mixed $object If specified, delete the relationship to the object or array of objects.
     * @param   string $related_field Can be used to specify which relationship to delete.
     * @return  bool Success or Failure of the delete.
    **/
    public function delete( $object = '', $related_field = '' ) 
    {
        // Start Transaction
        $this->trans_begin();

        // Check if exists content for this content type and .
        // if exists Contents delete all.
        foreach ( $this->contents->get() as $content )
            $content->delete();

        parent::delete( $object, $related_field );

        // Check status of transaction.
        if ( $this->trans_status() === FALSE ) 
        {
            // Transaction failed, rollback.
            $this->trans_rollback();

            return FALSE;
        }
        else 
        {
            // Transaction successful, commit.
            $this->trans_commit();

            return TRUE;
        }
    }

    /**
     * fields: Get Fields associated at this Content Type.
     *         This fields are to be used in form context.
     *
     * @access public
     * @return array
    **/
    public function fields() 
    {
        $fields = $this->content_type_fields;

        $data = array();
        foreach ( $fields->get() as $field ) 
        {
            // Define Field Rules.
            $rules = array();

            if ( $field->required ) // Required Rules.
                array_push( $rules, 'required' );

            $data[ $field->name ] = array(
                'field'        => $field->name,
                'label'        => $field->label,
                'type'         => $field->type,
                'rules'        => $rules,
                'position'     => $field->position,
                'help'         => $field->help,
                'translatable' => $field->translatable,
            );

            // Parse Adicional Args.
            if ( $args = $field->args ) 
            {
                $lines = explode( '\r\n|\n', $args );
                if( isset($lines) and $lines[0] == $args)
                    $lines = explode("|", $args);

                if ( is_array( $lines ) && sizeof( $lines ) > 0 ) 
                {
                    foreach ( $lines as $line ) 
                    {
                        list( $name, $value ) = explode( '=', $line );

                        $values = explode( ',', $value );
                        if ( is_array( $values ) && sizeof( $values ) > 0 ) 
                        {
                            $temp = array();
                            foreach( $values as $value ) 
                            {
                                $value = explode( ':', $value );

                                if ( isset( $value[1] ) || ( isset( $value[1] ) && $value[1] == 0 ) )
                                    $temp[ $value[0] ] = $value[1];
                                else
                                    $temp[] = $value[0];
                            }

                            $values = $temp;
                        }
                        else
                        {
                            $values = $value;
                        }

                        $data[ $field->name ][ $name ] = $values;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * translatable_fields: Get Translatable Fields associated at this Content Type.
     *
     * @access public
     * @return array
    **/
    public function translatable_fields() 
    {
        $fields = $this->fields();

        foreach ( $fields as $name => $attrs ) 
        {
            if ( $attrs['translatable'] == 0 )
                unset( $fields[$name] );
        }

        return $fields;
    }

}