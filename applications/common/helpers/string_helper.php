<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * sanitize_string: Sanitize a string for use in uri.
 *
 * @access public
 * @param  string $string, [Required] String to sanitize.
 * @return string.
**/

if ( !function_exists('sanitize_string') ) 
{
    function sanitize_string( $string ) 
    {
        return strtolower( preg_replace( "/[^a-z]+/i", "-", $string ) );
    }
}