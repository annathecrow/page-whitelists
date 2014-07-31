<?php
/*
Plugin Name: Page Whitelists
Version: 0.5-alpha
Description: This plugin allows administrators to limit access only to selected pages - either for single users, or for entire roles (also works with roles created by other plugins).  
Author: Corvidism
Author URI: http://corvidism.com
Text Domain: page-whitelists
Domain Path: /languages
*/
	
//installation and uninstallation hooks
register_activation_hook(__FILE__, 'whitelists_activate');

foreach ( glob( plugin_dir_path( __FILE__ )."classes/*.php" ) as $file )
    include_once $file;

$wl_data = new WL_Data();
$whitelists = new Whitelists($wl_data,__FILE__);
$whitelists->run();

function whitelists_activate() {
	global $wpdb;
	$prefix = $wpdb->prefix;
	$wl_table_prefix = $prefix."wl_";			
	update_option('wlist_list_table',$wl_table_prefix."list");
	update_option('wlist_list_page_table',$wl_table_prefix."list_page");
	$wl_data = new WL_Data();
	$wl_data->initialize();
}