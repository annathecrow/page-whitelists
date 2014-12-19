<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Data {
	
	private $list_table;
	private $group_table;
	private $list_group_table;
	private $group_user_table;
	
	public function __construct() {
		WL_Dev::log("data class instantiated");
		global $wpdb;
		$prefix = $wpdb->prefix;
		$wl_table_prefix = $prefix."wl_";
		$this->list_table = $wl_table_prefix."list";
		$this->group_table = $wl_table_prefix."group";
		$this->list_group_table = $wl_table_prefix."list_group";
		$this->list_page_table = $wl_table_prefix."list_page";
		$this->group_user_table = $wl_table_prefix."group_user";		 
	}
	
	public function data_setup() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$sqls = array();
		
		//whitelists
		$sqls[$list_table] = "CREATE TABLE $this->list_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
		
		//groups
		$sqls[$group_table] = "CREATE TABLE $this->group_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
		
		//junction table for list-group relationships
		$sqls[$list_group_table] = "CREATE TABLE $this->list_group_table (
			list_id INT NOT NULL,
			group_id INT NOT NULL,
			PRIMARY KEY  (list_id,group_id),
			INDEX (group_id)			
			);";
		
		//junction table for group-user relationships
		$sqls[$list_page_table] = "CREATE TABLE $this->list_page_table (
			list_id INT NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (list_id,post_id),
			INDEX (post_id)
			);";
			
		//junction table for group-user relationships
		$sqls[$group_user_table] = "CREATE TABLE $this->group_user_table (
			group_id INT NOT NULL,
			user_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (group_id,user_id),
			INDEX (group_id)
			);";

		
		foreach ($sqls as $table_name => $sql) {
			dbDelta($sql);
		};		
		
		
		//create or update the tables
		//load sh--stuff into params, or something
	}
	
	public function create_whitelist($name) {
		//check if name doesn't exist yet - if it does, warn for it (TODO: implement Ajax form field validation for this)
		//this should really use try throw catch...
		global $wpdb;
		$table = $this->list_table;
		WL_Dev::log('trying to access table '.$table);
		if ($wpdb->get_row("SELECT * FROM $table WHERE name = '$name'") != NULL) {
			WL_Dev::log('list with this name already exists');
			return;
		};
		$success = $wpdb->insert(
			$this->list_table,
			array(
				'name' => $name,
				'time' => date('Y-m-d H:i:s')
			)
			);
		$id = $wpdb->insert_id;
		WL_Dev::log('created whitelist '.$id.', '.$name);
		return $wpdb->insert_id;
	}
	
	public function delete_whitelist($id) {
		
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
