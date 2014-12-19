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

$wl_id = 'whitelists';	
$wl_dir = plugin_dir_path(__FILE__);

//installation and uninstallation hooks
register_activation_hook(__FILE__, 'whitelists_activate');
register_deactivation_hook(__FILE__, 'whitelists_deactivate');
	
$wl_class_dir = $wl_dir."classes/";
require_once $wl_class_dir.'WL_Dev.php';
require_once $wl_class_dir.'WL_Data.php';
require_once $wl_class_dir.'WL_Access_Manager.php';
require_once $wl_class_dir.'WL_Admin_Menu.php';
require_once $wl_class_dir.'WL_Core.php';
$whitelists = new Whitelists();
$whitelists->run();



function whitelists_activate() {
	$whitelists = new Whitelists();
	$whitelists->install();
}

function whitelists_deactivate() {
	$whitelists = new Whitelists();
	$whitelists->uninstall();
}