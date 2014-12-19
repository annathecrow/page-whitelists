<?php

/*
 * Static functions? Aww, you know you wanna.
 * (not really.) 
 */
class WL_Dev {

	public static function log($message) {
	    ///if (WP_DEBUG === true && (!defined('PHPUNIT')||PHPUNIT===false)) {
	    if (WP_DEBUG === true && (!isset($GLOBALS['silent'])||$GLOBALS['silent']===false)) {
	        if (is_array($message) || is_object($message)) {
	            error_log(print_r($message, true));
	        } else {
	            error_log($message);
	        }
	    }
	}
	
	public static function error($exception) {
	    ///if (WP_DEBUG === true && (!defined('PHPUNIT')||PHPUNIT===false)) {
	    if (WP_DEBUG === true && (!isset($GLOBALS['silent'])||$GLOBALS['silent']===false)) {
	    	error_log($exception->getMessage()." -- on line ".$exception->getLine());
	    }
	}
	
	
}
