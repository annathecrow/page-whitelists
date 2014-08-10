<?php
/*
Plugin Name: Page Whitelists
Version: 1.0
Description: This plugin allows administrators to limit access only to selected pages - either for single users, or for entire roles (also works with roles created by other plugins).  
Author: Anna Frankova
Author URI: http://corvidism.com
Licence: GPL2

Copyright 2014 Anna Frankova (frankova@corvidism.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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