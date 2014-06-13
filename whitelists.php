<?php
/*
Plugin Name: Whitelists
Version: 0.1-alpha
Description: PLUGIN DESCRIPTION HERE
Author: YOUR NAME HERE
Author URI: YOUR SITE HERE
Plugin URI: PLUGIN SITE HERE
Text Domain: whitelists
Domain Path: /languages
*/

if (!class_exists('Whitelists')) {
	
	define('WL_ID','whitelists');
	$class_dir = plugin_dir_path(__FILE__)."classes/";
	require_once $class_dir.'WL_Dev.php';
	require_once $class_dir.'WL_Data.php';
	require_once $class_dir.'WL_Core.php';	
};

if (class_exists('Whitelists')) {
	
	//instantiate the plugin object
	if (!isset($whitelists)) $whitelists = new Whitelists();
	
	//installation and uninstallation hooks
	register_activation_hook(__FILE__, array($whitelists, 'activate'));
    register_deactivation_hook(__FILE__, array($whitelists, 'deactivate'));
	
	//filter hooks	
	add_action('init',array($whitelists, 'init'));
	add_action( 'load-edit.php', array($whitelists, 'filter_displayed') );
	add_action('load-post.php', array($whitelists, 'filter_editable'));
	add_action( 'admin_notices', array($whitelists, 'test'));
	add_action( 'wp_before_admin_bar_render', array($whitelists, 'filter_admin_bar') );
	add_action('new_to_auto-draft',array($whitelists, 'auto_assign_to_whitelist'));
}

l;