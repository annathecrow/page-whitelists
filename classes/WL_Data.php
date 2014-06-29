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
			KEY post_id (post_id)
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
			);//should't these be sql escaped?
			if (!$success) {
				throw new Exception("Table row could not be written.",0); //TODO translate
			} else {
				$id = $wpdb->insert_id;
				WL_Dev::log('created whitelist '.$id.', '.$name);
				return new WL_List($id,$name,$time);
			}
			
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			if ($e->getCode()==1) {return true;} else {return false;}
			//THOUGHT: should, or shouldn't this return the existing list as WL_Object?
		}		
		
		
	}
	
	public function delete_whitelist($id) {
		global $wpdb;
		try {
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->list_table WHERE id = %d",$id));
			if ($row != NULL) {
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
					$user_query = new WP_User_Query($query_args);
					$users = $user_query->results;
					foreach($users as $user) {
						$this->remove_whitelist_user($id, $user->data->ID);
					}
					
					$roles = get_editable_roles();
					foreach($roles as $role) {
						$this->remove_whitelist_role($id, strtolower($role['name'])); //get_editable_roles returns names capitalized, but the get_role() function needs them lowercase. *eyeroll*
					}
					
					return array(true,'');
				}				
			} else {
				throw new Exception("Whitelist doesn't exist.", 2);
			}
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
			//??
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
	
	public function add_whitelist_role($list_id,$role_name) {
		 $role = get_role($role_name);
		 $role->add_cap('edit_whitelist_'.$list_id);
		 //shouldn't these return a value? true/false on success failure? how would I determine this?
	}
	
	public function remove_whitelist_role($list_id, $role_name) {
		$role = get_role($role_name);
		$role->remove_cap('edit_whitelist_'.$list_id);
	}
	
	public function add_whitelist_user($list_id,$user_id) {
		 $user = get_user_by('id',$user_id);
		 $user->add_cap('edit_whitelist_'.$list_id);
	}
	
	public function remove_whitelist_user($list_id, $user_id) {
		$user = get_user_by('id',$user_id);
		$user->remove_cap('edit_whitelist_'.$list_id);
	}
	
	
	public function get_whitelist($id) {
		//get single whitelist
		global $wpdb;
		try {
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->list_table WHERE id = %d",$id));
			if ($row != NULL) {
				$list = new WL_List($row->id,$row->name,$row->time);
				return $list;
			} else {
				WL_Dev::log("Whitelist doesn't exist.");
				return false;
			}
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage);
			return false;
		}
		
		
	}
	
	public function get_all_whitelists() {
		global $wpdb;
		$query = "SELECT * FROM $this->list_table";
		$raw_lists = $wpdb->get_results($query,ARRAY_A);
		foreach ($raw_lists as $list) {
			$array_of_lists[] = new WL_List($list['id'],$list['name'],$list['time']);
		}
		return $array_of_lists;
	}
}
	 