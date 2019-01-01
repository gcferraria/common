<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * youtube_id_from_url: get youtube video ID from URL.
 *
 * @access public
 * @param  string $url
 * @return string Youtube video id or FALSE if none found.
 */

if ( !function_exists('youtube_id_from_url') ) {

    function youtube_id_from_url( $url ) {

        $pattern =
            '%^# Match any youtube URL
            (?:https?://)?  # Optional scheme. Either http or https
            (?:www\.)?      # Optional www subdomain
            (?:             # Group host alternatives
              youtu\.be/    # Either youtu.be,
            | youtube\.com  # or youtube.com
              (?:           # Group path alternatives
                /embed/     # Either /embed/
              | /v/         # or /v/
              | /watch\?v=  # or /watch\?v=
              )             # End path alternatives.
            )               # End host alternatives.
            ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
            $%x'
        ;

        $result = preg_match( $pattern, $url, $matches );
        if ( $result )
            return $matches[1];

        return false;
    }
}

/**
 * vimeo_id_from_url: get vimeo video ID from URL.
 *
 * @access public
 * @param  string $url
 * @return string Vimeo video id or FALSE if none found.
 */

if ( !function_exists('vimeo_id_from_url') ) {

    function vimeo_id_from_url( $url ) {

        $pattern =
            '%^# Match any youtube URL
            (?:https?://)?  # Optional scheme. Either http or https
            (?:www\.)?      # Optional www subdomain
            player\.vimeo\.com\/video\/
            (\d+)
            $%x'
        ;

        $result = preg_match( $pattern, $url, $matches );
        if ( $result )
            return $matches[1];

        return false;
    }
}



if ( !function_exists('static_url') ) {
	
	/**
	 * static_url: Remove the old static url from file and add the current static url.
	 *
	 * @access public
	 * @param  string $url
	 *
	 * @return string new static url.
	 */
    function static_url( $url ) {
	    $CI =& get_instance();

        return $CI->config->item('static_url') . str_replace( $CI->config->item('static_url'), '', $url);
    }
}

if ( ! function_exists('current_url')) {

	/**
	 * Current URL
	 *
	 * Returns the full URL (including segments) of the page where this
	 * function is placed
	 *
	 * @return	string
	 */
	function current_url() {
		$CI =& get_instance();
		
		$languages = $CI->config->item('languages');
		
		if ( isset( $languages[$CI->uri->segment(1)] ) ) {
			$url = explode('/', $CI->config->site_url());
			array_pop($url);
			return implode('/', $url) . '/' . $CI->uri->uri_string();
		}

		return $CI->config->site_url($CI->uri->uri_string());
	}
}
