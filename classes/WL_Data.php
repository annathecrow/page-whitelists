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
				//THOUGHT: shouldn't this test be done somewhere else? Shouldn't this function just expect this list doesn't exist? The "name" isn't a unique key in the database. Would it really be that bad if two lists had same name?
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
			if ($e->getCode()==1) {return true;} else {return false;}
			//THOUGHT: should, or shouldn't this return the existing list as WL_Object?
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
		//add list-editing capability to this role 
	}
	
	public function remove_whitelist_from_role($list_id, $role_id) {
		
	}
	
	
	public function get_whitelists() {
		global $wpdb;
		$query = "SELECT * FROM $this->list_table";
		$raw_lists = $wpdb->get_results($query,ARRAY_A);
		foreach ($raw_lists as $list) {
			$array_of_lists[] = new WL_List($list['id'],$list['name'],$list['time']);
		}
		return $array_of_lists;
	}
}
	 