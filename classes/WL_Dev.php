<?php

class WL_Dev {
	
	/**
	 * Verbose dev logging for the pluggin. Set constant WL_DEBUG to true in wp-config.php to turn it on.
	 */
	public static function log($message) {
	    if (WP_DEBUG === true && defined('WL_DEBUG') && WL_DEBUG === true && (!isset($GLOBALS['silent'])||$GLOBALS['silent']===false)) {
	        if (is_array($message) || is_object($message)) {
	            error_log(print_r($message, true));
	        } else {
	            error_log($message);
	        }
	    }
	}
	
	/**
	 * Error logging. Logs when WP_DEBUG is on, with line numbers.
	 */
	public static function error($exception) {
	    ///if (WP_DEBUG === true && (!defined('PHPUNIT')||PHPUNIT===false)) {
	    if (WP_DEBUG === true && (!isset($GLOBALS['silent'])||$GLOBALS['silent']===false)) {
	    	error_log($exception->getMessage()." -- on line ".$exception->getLine());
	    }
	}	
}
