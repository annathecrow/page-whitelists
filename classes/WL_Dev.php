<?php

class WL_Dev {
	
	/**
	 * logs when WP Debug is on.
	 */
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
	
	/**
	 * logs exceptions with line numbers
	 */
	public static function error($exception) {
	    ///if (WP_DEBUG === true && (!defined('PHPUNIT')||PHPUNIT===false)) {
	    if (WP_DEBUG === true && (!isset($GLOBALS['silent'])||$GLOBALS['silent']===false)) {
	    	error_log($exception->getMessage()." -- on line ".$exception->getLine());
	    }
	}	
}
