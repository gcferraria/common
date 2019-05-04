<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class I18n_Language extends DataMapper 
{
    var $table = 'i18n_language';
    var $has_many = array(
        'websites' => array(
            'class'          => 'settings_website',
            'other_field'    => 'languages',
            'join_self_as'   => 'i18n_language',
            'join_other_as'  => 'settings_website',
            'cascade_delete' => TRUE,
        ),
        'translations' => array(
            'class'          => 'translation',
            'other_field'    => 'language',
            'cascade_delete' => TRUE,
        ),
    );

    public $validation = array(
        'code' => array(
            'type'  => 'text',
            'rules' => array('required','unique'),
        ),
        'name' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'region' => array(
            'type'  => 'text',
            'rules' => array('required'),
        ),
        'active' => array(
            'type'   => 'radiogroup',
            'rules'  => array('required'),
            'value'  => '1',
        ),
        'default' => array(
            'type'   => 'radiogroup',
            'rules'  => array('required'),
            'value'  => '0',
        ),
        'country' => array(
            'type'   => 'country',
            'rules'  => array('required'),
        ),
    );

    /**
     * check if this language is the default language.
     *
     * @return boolean TRUE if is default and FALSE if not.
     */
    public function is_default() 
    {
        return ( $this->default != 0 );
    }

}
