<?php
/**
 * Form Class
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package CodeIgniter
 * @subpackage Libraries
 * @category   Form
 * @author  Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright   2011 - 2016 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link    https://codeigniter.com
 * @since   Version 1.3 form.php 2016-05-29 gferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Form 
{
    /**
     * Configuration list
     * 
     * @var array
    **/
    public $config = array();

    /**
     * Language Definition.
     *
     * @var array
    **/
    public $language = array();

    /**
     * Form Action
     *  
     * @var string
    **/
    public $action;

    /**
     * Form Method
     * 
     * @var string
    **/
    public $method;

    /**
     * Form Attributes.
     * 
     * @var array
    **/
    public $attributes;

    /**
     * Form Fields
     * 
     * @var array
    **/
    public $fields = array();

    /**
     * Form Class Constructor. Initialize Form configuration.
     * 
     * @param  array $config, Configuration Data.
     * @return void
    **/
    public function __construct( $config = array() ) 
    {
        $this->CI =& get_instance();

        // Load Form configuration.
        $this->_load_config();

        // Load Language File
        $this->_load_language();

        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

    /**
     * Load Form Configuration File.
     * 
     * @return void
    **/
    private function _load_config() 
    {
        // Load Form Configuration.
        $this->CI->load->config('form', TRUE);

        // Save in Config array the Form Configuration.
        $this->config = $this->CI->config->item('form');
    }

    /**
     * Load Form Language
     *
     * @return void
     */
    private function _load_language() 
    {
        // Load the Form language file.
        $this->CI->lang->load('form');

        $this->lang = $this->CI->lang;
    }

    /**
     * Buil Form. Define the Form Attributes.
     * 
     * @param string $method, [Optional][ Default = 'post' ] Form Method.
     * @param string $action, [Optional] Form Action.
     * @param string $attrs,  [Optional] Form Attributes.
     * @return Form Object
    **/
    public function builder( $method = 'post', $action = '' , $attrs = array() ) 
    {
        // Set Form Action. If action is not defined, assume the action as current URI.
        $this->action = empty( $action ) ? $this->CI->uri->uri_string() : $action;

        // Set Form Method.
        $this->method = $method;

        // If exist attributes for the Form, set Form attributes
        if ( count( $attrs ) > 0 )
            $this->attributes = $attrs;

        return $this;
    }

    /**
     * Add fields to Form.
     *
     * @param array $fields, [Required] Fields List.
     * @param mixed $data,   [Optional] Array or Object with fields data.
     * @return Form Object
    **/
    public function add_fields( $fields, $data = NULL ) 
    {
        if ( !is_array( $fields ) ) 
        {
            log_message(
                'error',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'Fields Parameter is not an array.'
            );

            show_error('Fields Parameter is not an array');
        }

        foreach( $fields as $name => $field ) 
        {
            if ( !$this->_validate_field( $field ) )
                continue;

            // Check if exist data for populate the field value.
            if ( is_array( $data ) ) 
            {
                // If field name exist in data.
                $field['field'] = ( array_key_exists( $field['field'], $data ) )
                                ? $data[ $field['field'] ]
                                : '';
            }
            elseif ( is_object( $data ) ) 
            {
                // If field name is an attribute of Object.
                $field['value'] = ( isset( $data->{ $field['field'] } ) )
                                ? $data->{ $field['field'] }
                                : '';
            }
            elseif ( !isset( $field['value'] ) ) 
            {
                $field['value'] = '';
            }

            // Check if exists help for field
            if ( !isset( $field['help'] ) )
                $field['help'] = '';

            // Check if label exists.
            if ( !isset( $field['label'] ) && isset( $this->attributes['data-model'] ) ) 
                $field['label'] = $this->lang->line( $this->attributes['data-model'] . '_' . $name);

            // Add the field Object to the list of form fields.
            $this->fields[ $name ] = $field;
        }

        return $this;
    }

    /**
     * Check if field is valid.
     *
     * @param array $field, [Required] Field to validate.
     * @return boolean.
    **/
    private function _validate_field( $field ) 
    {
        // Check if the field has type.
        if( !isset( $field['type'] ) )
            return FALSE;

        // Check if the field already exists in fields list.
        if ( array_key_exists( $field['field'], $this->fields ) )
            return FALSE;

        return TRUE;
    }

    /**
     * Render Form from the template.
     * 
     * @param string $template, [Optional] Form Template.
     * @return string.
    **/
    public function render_form( $template = '' ) 
    {
        if ( empty( $template ) )
            $template = $this->config['form_template'];

        // Replace Fields by the Fields Output.
        $this->fields = $this->render_fields();
        $this->attrs  = isset( $this->attributes )
                    ? $this->_render_attributes( $this->attributes )
                    : '';

        return $this->CI->load->view( $template, $this, TRUE );
    }

    /**
     * Render Field from the template.
     * 
     * @param string $template, [Optional] Fields Path Template.
     * @return string.
    **/
    public function render_fields( $fields = array(), $template = '' ) 
    {
        if ( empty( $this->fields ) )
            return;

        if ( empty( $template ) )
            $template = $this->config['fields_template'];

        if ( empty( $fields ) )
            $fields = $this->fields;

        $fields_output = '';
        foreach( $fields as $name => $field ) 
        {
            // If Field is not defined assume the name based in your key.
            if ( !isset( $field['field'] ) )
                $field['field'] = $name;

            // Render Field Attributes
            $field['attrs'] = isset( $field['attrs'] )
                            ? $this->_render_attributes( $field['attrs'] )
                            : '';

            // If in Validation Rules have the required Validator, add this field as required.
            if ( isset( $field['rules'] ) ) 
                $field['required'] = in_array( 'required', $field['rules'] );

            // Gets the Field Render.
            $fields_output .= $this->CI->load->view(
                    $template . $field['type'],
                    $field,
                    TRUE
                );
        }

        return $fields_output;
    }

    /**
     * Transform Attributes in HTML Format.
     * 
     * @param string $attrs, [Required] Attributes to Render.
     * @return string.
    **/
    private function _render_attributes( $attrs = array() ) 
    {
        if ( empty( $attrs ) )
            return;

        $output = '';
        foreach( $attrs as $name => $value ) 
        {
            $output .= $name .'= "'. $value . '"';
        }

        return $output;
    }

}
