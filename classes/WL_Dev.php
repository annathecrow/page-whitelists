<?php

/*
 * Static functions? Aww, you know you wanna.
 * (not really.) 
 */
class WL_Dev {
	
	public static function log($message) {
	    if (WP_DEBUG === true) {
	        if (is_array($message) || is_object($message)) {
	            error_log(print_r($message, true));
	        } else {
	            error_log($message);
	        }
	    }
	}
	
	
}
