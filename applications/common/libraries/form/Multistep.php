<?php
/**
 * Multistep Form Class
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
 * @category Form
 * @author Gonçalo Ferraria <gferraria@gmail.com>
 * @copyright 2014 Gonçalo Ferraria
 * @license http://opensource.org/licenses/MIT  MIT License
 * @link https://codeigniter.com
 * @since Version 1.0 multistep.php 2014-10-17 08:43 gferraria $
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Multistep extends Form 
{
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
     * add_fields: Add step fields to Form.
     *
     * @access public
     * @param  array $steps,  [Required] StepFields List.
     * @param  mixed $data ,  [Optional] Array or Object with fields data.
     * @return Form Object
    **/
    public function add_fields( $steps, $data = NULL ) 
    {
        if ( !is_array( $steps ) ) 
        {
            log_message(
                'error',
                'Library: ' . __CLASS__ . '; Method: ' . __METHOD__ . '; '.
                'Fields Parameter is not an array.'
            );

            show_error('Fields Parameter is not an array');
        }

        foreach( $steps as $step ) 
        {
            $this->steps[] = $step;

            if ( isset( $step['fields'] ) ) 
            {
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
    public function render_form( $template = '' ) 
    {
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
    public function render_fields( $fields = array(), $template = '' ) 
    {
        $steps = array();

        foreach( $this->steps as $step ) 
        {
            $step_fields = array();
            foreach ( $step['fields'] as $name => $field ) 
                $step_fields[] = $this->fields[ $name ];

            array_push( $steps, parent::render_fields( $step_fields ) );
        }

        return $steps;
    }

}