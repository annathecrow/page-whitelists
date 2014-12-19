<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Data {
	
	private $list_table;
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
			page_id bigint(20) unsigned NOT NULL,
			PRIMARY KEY  (list_id,page_id),
			KEY page_id (page_id)
			);";
		
		foreach ($sqls as $table_name => $sql) {
			try {	
				dbDelta($sql);
			} catch (Exception $e) {
				WL_Dev::log("creation/update of ".$table_name." failed: ".$e->getMessage());
			}			
		};		
		
	}
	
	public function get_list_table() {
		return $this->list_table;
	}
	
	public function get_list_page_table() {
		return $this->list_page_table;
	}
		
	public function create_whitelist($name) {
		global $wpdb;
		$id = 0;
		$time = date('Y-m-d H:i:s');
		//WL_Dev::log('trying to access table '.$table);
		$table = $this->list_table;
		try {
			if ($this->get_whitelist_by('name',$name)==false) {
				$success = $wpdb->insert(
					$this->list_table,
					array(
						'name' => $name,
						'time' => $time
						)
					);
					if (!$success) {
						throw new Exception("Table row could not be written.",0);
					} else {
						$id = $wpdb->insert_id;
						WL_Dev::log('created whitelist '.$id.', '.$name);
						$list_info = array(
							'id' => $id,
							'name' => $name,
							'time' => $time,
						);
						return new WL_List($this, $list_info);
					}
			} else {
				throw new Exception("Whitelist with this name already exists.", 1); 
				//throw exception
			}
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			if ($e->getCode()==1) {return true;} else {return false;}
		}		
		
		
	}
	
	public function delete_whitelist($id) {
		global $wpdb;
		try {
			$list = $this->get_whitelist_by('id',$id); 
			if ($list===false) {
				throw new Exception("Whitelist doesn't exist.", 2);
			} else {	
				$success = $wpdb->delete($this->list_table, array(
					'id' => $id
					)
				);
				if (!$success) {
					throw new Exception("Whitelist couldn't be deleted from the database.", 3);
				} else {
					$query_args = array(
						'orderby'=>'ID'
					);
					$users = $list->get_users();
					foreach($users as $user) {
						$list->remove_user($user->data->ID);
					}
					$roles = $list->get_roles();
					foreach($roles as $role) {
						$list->remove_role($role->name);
					}
					$pages = $list->get_pages();
					foreach($pages as $page_id) {
						$list->remove_page($page_id);
					}
					return array(true,'');
				}
			};
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			switch ($e->getCode()) {
				case 2:
					$message = 'missing'; 
					break;
				case 3:
					$message = 'database error'; 
				
				default:
					$message = 'unknown error';
					break;
			}
			return array(false,$message);
		}
		
		
		
	}
	
	
	
	public function create_role($name, $caps) {
		//create new role
		//
		$id = 0; 
		return $id;
	}
	
	
	public function delete_role($id) {
		
	}
	//not sure if these are supposed to be here. They're... kinda not what this class is for, right? Ugh.
	
	public function get_whitelist_by($tag,$value) {
		global $wpdb;
		try {
			switch ($tag) {
				case 'id':
					$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->list_table WHERE id = %d",$value));
					break;
				case 'name':
					$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->list_table WHERE name = %s",$value));
					break;
				default:
					throw new Exception("Non-existent whitelist field.");
					break;
			}
			if ($row != NULL) {
				$list_info = array(
					'id' => $row->id,
					'name' => $row->name,
					'time' => $row->time
				);
				$list = new WL_List($this, $list_info);
				return $list;
			} else {
				throw new Exception("Whitelist doesn't exist.");
			}
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		} 
	}
	
	public function get_all_whitelists() {
		global $wpdb;
		$query = "SELECT * FROM $this->list_table";
		$raw_lists = $wpdb->get_results($query,ARRAY_A);
		foreach ($raw_lists as $list) {
			$array_of_lists[] = new WL_List($this,$list);
		}
		return $array_of_lists;
	}
}
	 