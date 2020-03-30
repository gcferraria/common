<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * array_replace_recursive: Merge two arrays and replace commom keys.
 *
 * @access  public
 * @param   array
 * @param   array
 * @return  mixed depends on what the array contains
 */

if( !function_exists('array_replace_recursive') ) 
{
    function array_replace_recursive() 
    {
        // Get array arguments.
        $arrays = func_get_args();

        // Define the original array.
        $original = array_shift( $arrays );

        // Loop through arrays.
        foreach( $arrays as $array ) 
        {
            // Loop through array key/value pairs.
            foreach( $array as $key => $value ) 
            {
                // Value is an array.
                if( is_array( $value ) && isset( $original[ $key ]) ) {
                    $original[$key] = array_replace_recursive( $original[$key], $array[$key] );
                }
                else 
                {
                    $original[$key] = $value;
                }    
            }
        }

        // Return the joined array.
        return $original;
    }
}

/**
 * array_sort: Sort fields by position.
 *
 * @access public
 * @param  array  $array, [Required] Array to sort.
 * @param  string $key  , [Required] key for sort array. Default = position.
 * @return array.
**/

if( !function_exists('array_sort') ) 
{
    function array_sort( &$array, $key ) 
    {
        $sorter = array();
        $ret    = array();

        reset( $array );

        foreach( $array as $ii => $va ) 
        {
            if( !isset( $va[ $key ] ) ) 
            {
                continue;
            }

            $sorter[ $ii ] = $va[ $key ];
        }

        asort($sorter);

        foreach( $sorter as $ii => $va ) 
        {
            $ret[ $ii ]=$array[ $ii ];
        }    

        $array = $ret;
    }
}
