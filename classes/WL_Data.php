<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Database {
	
	private $whitelists_table;
	private $groups_table;
	private $whitelists_groups_junction_table;
	private $groups_users_junction_table;
	
	public function __construct() {
		WL_Dev::log("data class instantiated");
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
