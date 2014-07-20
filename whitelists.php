<?php
/*
Plugin Name: Whitelists
Version: 0.3-alpha
Description: This plugin allows administrators to limit access only to selected pages - either for single users, or for entire roles (also works with roles created by other plugins).  
Author: Anna Frankova
Author URI: http://corvidism.com
Text Domain: whitelists
Domain Path: /languages
*/
	
//installation and uninstallation hooks
register_activation_hook(__FILE__, 'whitelists_activate');
register_deactivation_hook(__FILE__, 'whitelists_deactivate');

foreach ( glob( plugin_dir_path( __FILE__ )."classes/*.php" ) as $file )
    include_once $file;

$wl_data = new WL_Data();
$wl_settings = new WL_Settings(__FILE__);
$whitelists = new Whitelists($wl_data,$wl_settings);
$whitelists->run();



function whitelists_activate() {
	$wl_data = new WL_Data();
	$wl_settings = new WL_Settings(__FILE__);
	$whitelists = new Whitelists($wl_data,$wl_settings);
	$whitelists->install();
}

function whitelists_deactivate() {
	$wl_data = new WL_Data();
	$wl_settings = new WL_Settings(__FILE__);
	$whitelists = new Whitelists($wl_data,$wl_settings);
	$whitelists->uninstall();
}