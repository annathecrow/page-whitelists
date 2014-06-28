<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Data {
	
	private $list_table;
	private $list_role_table;
	private $list_page_table;
	
	public function __construct() {
		//WL_Dev::log("data class instantiated");
		global $wpdb;
		$prefix = $wpdb->prefix;
		$wl_table_prefix = $prefix."wl_";
		$this->list_table = $wl_table_prefix."list";
		$this->list_page_table = $wl_table_prefix."list_page";
		//shouldn't this shite be in WP Options?
				 
	}
	
	public function initialize() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$sqls = array();
		
		//whitelists
		$sqls[$this->list_table] = "CREATE TABLE $this->list_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
				
		//junction table for group-user relationships
		$sqls[$this->list_page_table] = "CREATE TABLE $this->list_page_table (
			list_id INT NOT NULL,
			post_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (list_id,post_id),
			INDEX (post_id)
			);";
		
		foreach ($sqls as $table_name => $sql) {
			try {	
				dbDelta($sql);
			} catch (Exception $e) {
				WL_Dev::log("creation/update of ".$table_name." failed: ".$e->getMessage());
			}			
		};		
		
	}
	
		
	public function create_whitelist($name) {
		global $wpdb;
		$id = 0;
		$time = date('Y-m-d H:i:s');
		//WL_Dev::log('trying to access table '.$table);
		$table = $this->list_table;
		try {
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE name = %s",$name));
			if ($row != NULL) {
				$id = $row->id;
				throw new Exception("Whitelist with this name already exists.", 1); //TODO translate
			};			
			$success = $wpdb->insert(
			$this->list_table,
			array(
				'name' => $name,
				'time' => $time
				)
			);
			if (!$success) {
				throw new Exception("Table row could not be written.",0); //TODO translate
			}
			$id = $wpdb->insert_id;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			//do stuff that isn't just logging. Probably should display some native WP error message (if it does even have such a thing)
			if ($e->getCode()==1) {return true;} else {return false;}
		}		
		
		WL_Dev::log('created whitelist '.$id.', '.$name);
		//return array('created',$id); //THIS SHOULD RETURN THE WL_List OBJECT
		return new WL_List($id,$name,$time);
	}
	
	public function delete_whitelist($id) {
		//delete whitelist by id
		//remove all references to it in the junction table
		
	}
	
	public function create_role($name, $caps) {
		//create new role
		//
		$id = 0; 
		return $id;
	}
	
	public function delete_role($id) {
		
	}
	
	public function add_whitelist_to_role($list_id,$role_id) {
		//add role-list pair to junction table
		//add capability to edit the pages in the whitelist role
	}
	
	public function remove_whitelist_from_role($list_id, $role_id) {
		
	}
	
	
	public function get_whitelists() {
		global $wpdb;
		//what am I doing. SERIOUSLY what am I DOING. THere is supposed to be some class or some shit that corresponds to the list, and I don't know what the hell THIS IS ALL SO COMPLICATED OKAY I HAVEN'T STUDIED FOR THIS MY IT BACKGROUND IS BASICALLY "POKED INTO IT LONG ENOUGH UNTIL IT STUCK" AND ALL THIS OOP AND BEST PRACTICES AND SQL ESCAPING IS JUST SO MUCH OVER MY HEAD I FEEL LIKE TRYING TO GARGLE ACID
		
		
		//fetch from database
		//create WL_List from each row
		//return as array
		$query = "SELECT * FROM $this->list_table";
		$raw_lists = $wpdb->get_results($query,ARRAY_A);
		foreach ($raw_lists as $list) {
			$array_of_lists[] = new WL_List($list['id'],$list['name'],$list['time']);
		}
		
		//okay, a problem: the WL_List object should, presumably, have all the info about a list. As in, users, roles, pages. That means I need to poll two databases AND do a pretty complicated sifting of users/roles to get it. And for a listing of lists, I don't even need all that info, because I'm not gonna USE it.
		//BUT, if I'm trying to approach this logically, an object that has half the data here and all the data elsewhere is a BAAAD idea.
		//BUT, you're supposed to poll as few db tables as possible. 
		
		//options: 
			//fetch it all, don't care about the overhead.
			//fetch it partially, deal with incomplete objects later.
			//don't create objects, it's just for html listing anyway.
			//don't have the user/roles and the pages info in the object at all. Fetch it as needed. Maybe store it after the first fetch so it won't have to do it repeatedly in one run.
		
		return $array_of_lists;
	}
	
	/*
	 * good news: WP has a function to add tables to database.
	 * bad news: it's fussy as bitch and it HAS NO DOCUMENTATION. 
	 * -- because we needed another proof I've lost my marbles. Still easier than doing the SQL mating dance manually, though.
	 * Kingdom for a framework.
	 */
}
	 