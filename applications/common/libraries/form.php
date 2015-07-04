<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Form Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Form
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2011 - 2012 Gonçalo Ferraria
 * @version    1.2 form.php 2012-10-21 12:20 gferraria $
 */

class Form {

    /**
     * @var array, Configuration list.
     * @access public
    **/
    public $config = array();

    /**
     * @var array, Language Definition.
     * @access public
    **/
    public $language = array();

    /**
     * @var string, Form Action.
     * @access public
    **/
    public $action;

    /**
     * @var string, Form Method.
     * @access public
    **/
    public $method;

    /**
     * @var array, Form Attributes.
     * @access public
    **/
    public $attributes;

    /**
     * @var array, Form Fields.
     * @access public
    **/
    public $fields = array();

    /**
     * __construct: Form Class Constructor.
     *              Initialize Form configuration.
     * 
     * @access public
     * @param  array $config, Configuration Data.
     * @return void
    **/
    public function __construct( $config = array() ) {

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
     * _load_config: Load Form Configuration File.
     * 
     * @access private
     * @return void
    **/
    private function _load_config() {

        // Load Form Configuration.
        $this->CI->load->config('form', TRUE);

        // Save in Config array the Form Configuration.
        $this->config = $this->CI->config->item('form');
    }

    /**
     * _load_language: Load Form Language
     *
     * @access private
     * @return void
     */
    private function _load_language() {

        // Load the Form language file.
        $this->CI->lang->load('form');

        $this->lang = $this->CI->lang;
    }

    /**
     * builder: Buil Form. Define the Form Attributes.
     * 
     * @access public
     * @param string $method, [Optional][ Default = 'post' ] Form Method.
     * @param string $action, [Optional] Form Action.
     * @param string $attrs,  [Optional] Form Attributes.
     * @return Form Object
    **/
    function builder( $method = 'post', $action = '' , $attrs = array() ) {

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
     * add_fields: Add fields to Form.
     *
     * @access public
     * @param array $fields,  [Required] Fields List.
     * @param mixed $data,    [Optional] Array or Object with fields data.
     * @return Form Object
    **/
    public function add_fields( $fields, $data = NULL ) {

        if ( !is_array( $fields ) ) {

            log_message(
                'error',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'Fields Parameter is not an array.'
            );

            show_error('Fields Parameter is not an array');
        }

        foreach( $fields as $name => $field ) {
            if ( !$this->_validate_field( $field ) )
                continue;

            // Check if exist data for populate the field value.
            if ( is_array( $data ) ) {

                // If field name exist in data.
                $field['field'] = ( array_key_exists( $field['field'], $data ) )
                                ? $data[ $field['field'] ]
                                : '';
            }
            elseif ( is_object( $data ) ) {

                // If field name is an attribute of Object.
                $field['value'] = ( isset( $data->{ $field['field'] } ) )
                                ? $data->{ $field['field'] }
                                : '';
            }
            elseif ( !isset( $field['value'] ) ) {
                $field['value'] = '';
            }

            // Check if exists help for field
            if ( !isset( $field['help'] ) )
                $field['help'] = '';

            // Add the field Object to the list of form fields.
            $this->fields[ $name ] = $field;
        }

        return $this;
    }

    /**
     * _validate_field: Check if field is valid.
     *
     * @access private
     * @param array $field,  [Required] Field to validate.
     * @return boolean.
    **/
    private function _validate_field( $field ) {

        // Check if the field has type.
        if( !isset( $field['type'] ) )
            return FALSE;

        // Check if the field already exists in fields list.
        if ( array_key_exists( $field['field'], $this->fields ) )
            return FALSE;

        return TRUE;
    }

    /**
     * render_form: Render Form from the template.
     * 
     * @access public
     * @param string $template, [Optional] Form Template.
     * @return string.
    **/
    public function render_form( $template = '' ) {

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
     * render_fields: Render Field from the template.
     * 
     * @access public
     * @param string $template, [Optional] Fields Path Template.
     * @return string.
    **/
    public function render_fields( $fields = array(), $template = '' ) {

        if ( empty( $this->fields ) )
            return;

        if ( empty( $template ) )
            $template = $this->config['fields_template'];

        if ( empty( $fields ) )
            $fields = $this->fields;

        $fields_output = '';
        foreach( $fields as $name => $field ) {

            // If Field is not defined assume the name based in your key.
            if ( !isset( $field['field'] ) )
                $field['field'] = $name;

            // Render Field Attributes
            $field['attrs'] = isset( $field['attrs'] )
                            ? $this->_render_attributes( $field['attrs'] )
                            : '';

            // If in Validation Rules have the required Validator, add this field as required.
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
     * _render_attribues: Transform Attributes in HTML Format.
     * 
     * @access private
     * @param string $attrs, [Required] Attributes to Render.
     * @return string.
    **/
    private function _render_attributes( $attrs = array() ) {

        if ( empty( $attrs ) )
            return;

        $output = '';
        foreach( $attrs as $name => $value ) {
            $output .= $name .'= "'. $value . '"';
        }

        return $output;
    }

}

/* End of file form.php */
/* Location: ./applications/common/libraries/form.php */
