<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Data {
	
	private $WL_list_table;
	private $WL_group_table;
	private $WL_list_group_table;
	private $WL_group_user_table;
	
	public function __construct() {
		WL_Dev::log("data class instantiated");
		global $wpdb;
		$prefix = $wpdb->prefix;
		$WL_table_prefix = $prefix."wl_";
		$WL_list_table = $WL_table_prefix."list";
		$WL_group_table = $WL_table_prefix."group";
		$WL_list_group_table = $WL_table_prefix."list_group";
		$WL_list_page_table = $WL_table_prefix."list_page";
		$WL_group_user_table = $WL_table_prefix."group_user";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$sqls = array();
		
		//whitelists
		$sqls[$WL_list_table] = "CREATE TABLE $WL_list_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
		
		//groups
		$sqls[$WL_group_table] = "CREATE TABLE $WL_group_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
		
		//junction table for list-group relationships
		$sqls[$WL_list_group_table] = "CREATE TABLE $WL_list_group_table (
			list_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY  (list_id, group_id),
			INDEX (group_id),
			FOREIGN KEY (list_id) 
				REFERENCES $WL_list_table(id) 
				ON UPDATE CASCADE,
			FOREIGN KEY (group_id) 
				REFERENCES $WL_group_table(id) 
				ON UPDATE CASCADE
			);";
		
		
		$post_table = $prefix."posts";

		//junction table for group-user relationships
		$sqls[$WL_list_page_table] = "CREATE TABLE $WL_list_page_table (
			list_id INT NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (list_id,post_id),
			INDEX (post_id),
			FOREIGN KEY (list_id) 
				REFERENCES $WL_list_table(id) 
				ON UPDATE CASCADE,
			FOREIGN KEY (post_id) 
				REFERENCES $post_table(ID) 
				ON UPDATE CASCADE
			);";
		
		$user_table = $prefix."users";

		//junction table for group-user relationships
		$sqls[$WL_group_user_table] = "CREATE TABLE $WL_group_user_table (
			group_id INT NOT NULL,
			user_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (group_id,user_id),
			INDEX (group_id),
			FOREIGN KEY (group_id) 
				REFERENCES $WL_group_table(id) 
				ON UPDATE CASCADE,
			FOREIGN KEY (user_id) 
				REFERENCES $user_table(ID) 
				ON UPDATE CASCADE
			);";

		
		foreach ($sqls as $sql) {
			dbDelta( $sql );
		};		
		
		//create or update the tables
		//load sh--stuff into params, or something 
	}
	
	public function create_whitelist() {
		
	}
	
	public function delete_whitelist() {
		
	}
	
	public function create_group() {
		
	}
	
	public function delete_group() {
		
	}
	
	
	public function add_user_to_group($user_id,$group_id) {
		
	}
	
	public function remove_user_from_group($user_id, $group_id) {
		
	}
	
	public function add_whitelist_to_group($user_id,$group_id) {
		
	}
	
	public function remove_whitelist_from_group($user_id, $group_id) {
		
	}
	
	
	/*
	 * good news: WP has a function to add tables to database.
	 * bad news: it's fussy as bitch and it HAS NO DOCUMENTATION. 
	 * -- because we needed another proof I've lost my marbles. Still easier than doing the SQL mating dance manually, though.
	 * Kingdom for a framework.
	 */
}
