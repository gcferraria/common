<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
  * extensive_date: Get a date by extense.
  *
  * @access public
  * @param  string  $date, [Optional] [Default=null] Date to convert.
  * @param  boolean $time, [Optional] [Default=true] Show/Hide Time in Result String.
  * @return string
**/
if ( !function_exists('extensive_date') ) 
{
	function extensive_date( $date = null, $time = true ) 
	{
		if ( $date == null )
			$date = date('Y-m-d H:i:s');

		$CI =& get_instance();

		// Parse Day and Month
		$months       = $CI->lang->line('extensive_months');
		$day          = date('d', strtotime( $date ) );
		$month        = date('m', strtotime( $date ) );
		$year         = date('Y', strtotime( $date ) );
		$hour         = date('H', strtotime( $date ) );
		$minutes      = date('i', strtotime( $date ) );

		return sprintf (
            ( $time ) ? '%d de %s de %d às %s:%s' : '%d de %s de %d',
            $day,
            $months[ $month ],
			$year,
			$hour,
			$minutes
        );
	}
}

/**
  * extract_time: Extract Time from datetime.
  *
  * @access public
  * @param  string  $date, [Optional] [Default=null] Date to extract.
  * @return string
**/
if ( !function_exists('extract_time') ) 
{
	function extract_time( $date = null ) 
	{
		if ( $date == null )
			$date = date('Y-m-d H:i:s');

		$CI =& get_instance();

		// Parse Hour and minutes
		$hour    = date('H', strtotime( $date ) );
		$minutes = date('i', strtotime( $date ) );

		return sprintf('%s:%s', $hour, $minutes);
	}
}

/**
  * short_date: Get a date in short format.
  *
  * @access public
  * @param  string  $date, [Optional] [Default=null] Date to convert.
  * @return string
**/
if ( !function_exists('short_date') ) 
{
	function short_date( $date = null ) 
	{
		if ( $date == null )
			$date = date('Y-m-d H:i:s');

		$CI =& get_instance();

		// Parse Day and Month
		$months       = $CI->lang->line('months');
		$day          = date('d', strtotime( $date ) );
		$month        = date('m', strtotime( $date ) );
		$year         = date('Y', strtotime( $date ) );
		$hour         = date('H', strtotime( $date ) );
		$minutes      = date('i', strtotime( $date ) );

		return sprintf( '%s/%d', $months[ $month ], $year );
	}
}

/**
  * get_spent_hours: Get the diference between two dates in hours.
  *
  * @access public
  * @param  string  $date, [Required] Date to check diference.
  * @return DateInterval
**/
if ( !function_exists('get_spent_hours') ) 
{
	function get_spent_hours( $date ) 
	{
		$now  = new DateTime( date('Y-m-d H:i:s') );
    	$date = new DateTime( date('Y-m-d H:i:s', strtotime( $date ) ) );

		return $now->diff( $date );
	}
}