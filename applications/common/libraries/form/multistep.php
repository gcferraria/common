<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Multistep Form Class
 * 
 * @package    CodeIgniter
 * @subpackage Libraries
 * @category   Form
 * @author     Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright  2014 Gonçalo Ferraria
 * @version    1.0 multistep.php 2014-10-17 08:43 gferraria $
 */

class Multistep extends Form {

    /**
     * @var    array $steps, steps definition.
     * @access public
    **/
    public $steps = array();

    /**
     * @var    int $steps_count, steps number.
     * @access public
    **/
    public $steps_count = 0;

    /**
     * __construct: Multistep Form Class Constructor.
     *              Initialize Form configuration.
     * 
     * @access public
     * @param  array $config, Configuration Array.
     * @param   
     * @return void
    **/
    public function __construct( $config = array() ) {
        parent::__construct( $config );

        log_message(
            'debug',
            __CLASS__ . 'Class Initialized;'
        );
    }

    /**
     * add_fields: Add step fields to Form.
     *
     * @access public
     * @param  array $steps,  [Required] StepFields List.
     * @param  mixed $data ,  [Optional] Array or Object with fields data.
     * @return Form Object
    **/
    public function add_fields( $steps, $data = NULL ) {

        if ( !is_array( $steps ) ) {

            log_message(
                'error',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'Fields Parameter is not an array.'
            );

            show_error('Fields Parameter is not an array');
        }

        foreach( $steps as $step ) {
            $this->steps[] = $step;

            if ( isset( $step['fields'] ) ) {
                parent::add_fields( $step['fields'], $data );
            }
        }

        // Set steps count.
        $this->steps_count = count( $steps );

        return $this;
    }

    /**
     * render_form: Render Multistep Form.
     * 
     * @access public
     * @return string.
    **/
    public function render_form() {
        return parent::render_form( 
            $this->config['form_multistep_template'] 
        ); 
    }

    /**
     * render_fields: Render Field from the template.
     * 
     * @access public
     * @return array.
    **/
    public function render_fields() {
        $steps = array();

        foreach( $this->steps as $step ) {

            $step_fields = array();
            foreach ( $step['fields'] as $name => $field ) 
                $step_fields[] = $this->fields[ $name ];

            array_push( $steps, parent::render_fields( $step_fields ) );
        }

        return $steps;
    }

}

/* End of file multistep.php */
/* Location: ./applications/common/libraries/form/multistep.php */
