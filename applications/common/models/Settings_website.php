<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_website extends DataMapper 
{
    var $table    = 'settings_website';
    var $has_many = array(
        'languages' => array(
            'class'          => 'i18n_language',
            'other_field'    => 'websites',
            'join_self_as'   => 'settings_website',
            'join_other_as'  => 'i18n_language',
            'cascade_delete' => TRUE,
        ),
        'newsletter_templates' => array(
            'class'          => 'newsletter_template',
            'other_field'    => 'website',
            'join_self_as'   => 'settings_website',
            'join_other_as'  => 'newsletter_template',
            'cascade_delete' => TRUE,
        )
    );
    var $has_one  = array(
        'category' => array(
            'class'          => 'category',
            'other_field'    => 'website',
            'join_other_as'  => 'category',
        ),
    );

    public $validation = array(
        'name' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'domain' => array(
            'type'  => 'text',
            'rules' => array('required', 'valid_domain', 'unique_pair' => 'category_id' ),
        ),
        'title' => array(
            'type' => 'text',
            'rules' => array('required', 'min_string_size' => 1, 'max_string_size' => 70 ),
        ),
        'description' => array(
            'type' => 'textarea',
            'rules' => array('required', 'min_string_size' => 70, 'max_string_size' => 160 ),
        ),
        'keywords' => array(
            'type'  => 'tag',
            'rules' => array('required')
        ),
        'languages' => array(
            'type' => 'multiselect',
        ),
    );

    /**
     * save: Create an website and save your languages.
     *
     * @access public
     * @param  mixed  $object,  [Optional] object to save or array of objects to save.
     * @param  string $relation,[Optional] string to save the object as a specific relationship.
     * @return bool Success or Failure of the validation and save.
     **/
    public function save( $object = '', $relation = '' ) 
    {
        if( empty( $object ) )
            return parent::save( $object, $relation );

        // Start Transaction
        $this->trans_begin();

        if( is_array($object) && !empty($object) ) 
        {
            // If exists Languages Delete All.
            if ( $this->languages->count() > 0 ) 
            {
                $objects = $this->languages->get();
                $this->delete( $objects->all, 'languages' );
            }

            // Associate Languages to Website.
            if ( $ids = $object['languages'] ) 
            {
                if ( !empty( $ids ) ) 
                {
                    $languages = array();
                    foreach ( $ids as $language ) 
                    {
                        $new = new i18n_language();
                        $new->get_by_id( $language );

                        if ( !$new->exists() )
                            continue;

                        array_push( $languages, $new );
                    }

                    $object['languages'] = $languages;
                }
            }

            parent::save( $object, $relation );
        }

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

}
