<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Translation extends DataMapper 
{
    var $table    = 'translation';
    var $has_one  = array(
        'category' => array(
            'class'          => 'category',
            'other_field'    => 'translations',
            'join_other_as'  => 'category',
        ),
        'content' => array(
            'class'          => 'content',
            'other_field'    => 'translations',
            'join_other_as'  => 'content',
        ),
        'language' => array(
            'class'          => 'i18n_language',
            'other_field'    => 'translations',
            'join_other_as'  => 'i18n_language',
        ),
    );

    public $validation = array(
        'language_id' => array(
            'type'  => 'select',
            'rules' => array('required'),
        ),
        'name' => array(
            'type'  => 'text',
        ),
        'value' => array(
            'type'  => 'text',
        ),
    );

    /**
     * as_name_value_array: Gets content values in format name as value.
     *
     * @access public
     * @return array
    **/
    public function as_name_value_array() 
    {
        $values = array();
        foreach( $this->values->get() as $value ) 
        {
            $values[ $value->name ] = $value->value;
        }    

        return $values;
    }

}
