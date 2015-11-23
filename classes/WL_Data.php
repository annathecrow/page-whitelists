<?php

/**
 * class for manipulation of the custom database tables
 */
class WL_Data {
	
	private $list_table;
	private $list_page_table;
	
	public function __construct() {
		$this->list_table = get_option("wlist_list_table");
		$this->list_page_table = get_option("wlist_list_page_table");		 
	}
	
	public function initialize() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$sqls = array();
		
		//whitelists
		$sqls[$this->list_table] = "CREATE TABLE $this->list_table (
			id INT NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			strict tinyint(1)  NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			UNIQUE KEY id (id)
			);";
				
		//junction table for list-page relationships
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
				WL_Dev::error("creation/update of ".$table_name." failed: ".$e->getMessage());
			}			
		};		
		
	}
	
	public function get_list_table() {
		return $this->list_table;
	}
	
	public function get_list_page_table() {
		return $this->list_page_table;
	}
		
	/**
	 * create whitelist
	 * @param $name
	 */
	public function create_whitelist($name,$strict = 1) {
		global $wpdb;
		$id = 0;
		$time = date('Y-m-d H:i:s');
		$table = $this->list_table;
		try {
			if ($this->get_whitelist_by('name',$name)==false) {
				$success = $wpdb->insert(
					$this->list_table,
					array(
						'name' => $name,
						'time' => $time,
						'strict' => $strict
						)
					);
					if (!$success) {
						throw new Exception("Table row could not be written.",0);
					} else {
						$id = $wpdb->insert_id;
						$list_info = array(
							'id' => $id,
							'name' => $name,
							'time' => $time,
							'strict' => $strict
						);
						return new WL_List($this, $list_info);
					}
			} else {
				throw new Exception("Whitelist with this name already exists.", 1); 
				//throw exception
			}
		} catch (Exception $e) {
			if ($e->getCode()==1) {
				WL_Dev::log($e->getMessage());
				return true;
			} else {
				WL_Dev::error($e);
				return false;
			}
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
					$pages = $list->get_page_ids();
					foreach($pages as $page_id) {
						$list->remove_page($page_id);
					}
					return array(true,'');
				}
			};
		} catch (Exception $e) {
			WL_Dev::error($e->getMessage());
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
					throw new Exception("Non-existent whitelist field.",0);
					break;
			}
			if ($row != NULL) {
				$list_info = array(
					'id' => $row->id,
					'name' => $row->name,
					'time' => $row->time,
					'strict' => $row->strict,
				);
				$list = new WL_List($this, $list_info);
				return $list;
			} else {
				throw new Exception("Whitelist doesn't exist.",0);
			}
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		} 
	}
	
	public function get_all_whitelists() {
		global $wpdb;
		$query = "SELECT * FROM $this->list_table ORDER BY id";
		$raw_lists = $wpdb->get_results($query,ARRAY_A);
		$array_of_lists = array(); 
		foreach ($raw_lists as $list) {
			$array_of_lists[] = new WL_List($this,$list);
		}
		return $array_of_lists;
	}
	
	public function get_user_whitelists($user) {
		try {			
			if (is_numeric($user)) {
				$user = get_user_by('id',$user);
				if (!$user) {throw new Exception('User not found.',0);};
			} else if (get_class($user) !== 'WP_User') {
				throw new Exception('$user is neither id nor an instance of WP_User.',0);
			}
			$all_whitelists = $this->get_all_whitelists();
			$whitelist_ids = array();
			foreach ($user->allcaps as $cap => $v) {
				if (strpos($cap,"edit_whitelist_")!== false) {
					$whitelist_ids[] = str_replace("edit_whitelist_", "", $cap);
				}
			}
			$whitelists = array();
			foreach ($all_whitelists as $list) {
				if (in_array($list->get_id(),$whitelist_ids)) {
					$whitelists[] = $list;
				};
			}
			return $whitelists;	
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		}	
	}
	
	public function get_role_whitelists($role) {
		try {			
			if (is_string($role)) {
				$role = get_role($role);
				if ($role == null) throw new Exception('Role not found.',0);
			} else if (get_class($role) === 'WP_Role') {
			} else {
				throw new Exception('$role is neither string nor an instance of WP_Role.',0);
			}
			//WL_Dev::log("getting user whitelists for user $user->user_login");
			$all_whitelists = $this->get_all_whitelists();
			$whitelist_ids = array();
			foreach ($role->capabilities as $cap => $v) {
				if (strpos($cap,"edit_whitelist_")!== false) {
					$whitelist_ids[] = str_replace("edit_whitelist_", "", $cap);
				}
			}
			$whitelists = array();
			foreach ($all_whitelists as $list) {
				if (in_array($list->get_id(),$whitelist_ids)) {
					$whitelists[] = $list;
				};
			}
			return $whitelists;	
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		}	
	}
	
	public function get_accessible_pages($user) {
		$whitelists = $this->get_user_whitelists($user);
		if (sizeof($whitelists)==0) return false;
		$pages = array();
		$empty_list_failsafe = true;
		foreach ($whitelists as $list) {
			if (!$list) continue;
			$empty_list_failsafe = false;
			$pages = array_merge($pages, $list->get_page_ids());
		}
		if ($empty_list_failsafe) {
			return false;
		} else {
			return $pages;
		}
		
	}
	
	function remove_page_from_all($page_id) {
		//remove all references to this page from database
		try {
			global $wpdb;
			if (!get_post_type($page_id)=='page') return;
			$success = $wpdb->delete($this->list_page_table,array('page_id'=>$page_id));
			if (!$success) {
				throw new Exception("No corresponding entries in the database.");
			}
			WL_Dev::log("page removed from $success lists.");
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
		}	
	}
	
}
	 