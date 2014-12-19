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
	
$wl_dir = plugin_dir_path(__FILE__);

//installation and uninstallation hooks
register_activation_hook(__FILE__, 'whitelists_activate');
register_deactivation_hook(__FILE__, 'whitelists_deactivate');

foreach ( glob( plugin_dir_path( __FILE__ )."classes/*.php" ) as $file )
    include_once $file;

$wl_data = new WL_Data();
$wl_settings = new WL_Settings($wl_dir);
$whitelists = new Whitelists($wl_data,$wl_settings);
$whitelists->run();



function whitelists_activate() {
	$whitelists = new Whitelists();
	$whitelists->install();
}

function whitelists_deactivate() {
	$whitelists = new Whitelists();
	$whitelists->uninstall();
}