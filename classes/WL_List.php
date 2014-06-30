<?php

class WL_List {
	private $id;
	private $name;
	private $time;
	private $cap;
	private $data;
	
	
	/**
	 * 
	 * 
	 */
	public function __construct(WL_Data &$data,Array $list_info) { 
		$this->data = $data;
		$this->id = $list_info['id'];
		$this->name = $list_info['name'];
		$this->time = $list_info['time'];
		$this->cap = 'edit_whitelist_'.$this->id;		
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function get_time() {
		return $this->time;
	}
	
	public function get_cap() {
		return $this->cap;
	}
	
	public function get_users() {
		if (isset($this->users)) {
			WL_Dev::log("users array exists.");
			WL_Dev::log($this->users);
			return $this->users;
		} 
		
		$query_args = array(
			'orderby'=>'ID'
		);
		$user_query = new WP_User_Query($query_args);
		$users = $user_query->results;
		$assigned_users= array();
		foreach($users as $key=>$user) {
			if (array_key_exists($this->cap, $user->caps)){
				$assigned_users[] = $user;
			};
		}
		$this->users = $assigned_users;
		return $assigned_users;
	}
	
	public function get_roles() {
		if (isset($this->roles)) {
			WL_Dev::log("roles array exists.");
			WL_Dev::log($this->roles);
			return $this->roles;
		}
		$all_roles = get_editable_roles();
		$assigned_roles = array();
		foreach ($all_roles as $role_name=>$role_data) {
			if (array_key_exists($this->cap,$role_data['capabilities'])) {
				$assigned_roles[] = get_role($role_name);
			}
		}
		$this->roles = $assigned_roles;
		return $assigned_roles;
	}
	
	public function get_pages() {
		//return assigned pages
	}
	
	public function add_user($user) {
		try {			
			if (is_numeric($user)) {
				WL_Dev::log('$user is numeric');
				$user = get_user_by('id',$user);
				if (!$user) {throw new Exception('User not found.');};
			} else if (get_class($user) === 'WP_User') {
				WL_Dev::log('$user should be WP_User instance');
			} else {
				throw new Exception('$user is neither string nor an instance of WP_User.');
			}
			$user->add_cap($this->cap);
			if (isset($this->users)) {
				WL_Dev::log("users array exists, adding.");
				$this->users[]=$user;
			} else {
				WL_Dev::log("users array doesn't exist, creating.");
				$this->users = array($user);
			} 
			return true;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			return false;
		}	
	}
	
	public function remove_user($user) {
		try {			
			if (is_numeric($user)) {
				WL_Dev::log('$user is numeric');
				$user = get_user_by('id',$user);
				if (!$user) {throw new Exception('User not found.');}
			} else if (get_class($user) === 'WP_User') {
				WL_Dev::log('$user should be WP_User instance');
			} else {
				throw new Exception('$user is neither numeric nor an instance of WP_User.');
			}
			$user->remove_cap($this->cap);
			if (!isset($this->users)) {
				WL_Dev::log("roles array doesn't exist, fetching.");
				$this->get_users();
			} else {
				WL_Dev::log("roles array exists.");
				WL_Dev::log($this->users);
			}
			unset($this->users[in_array($user, $this->users)]);
			return true;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			return false;
		}
	}
	
	public function add_role($role) {
		try {			
			if (is_string($role)) {
				WL_Dev::log('$role is string');
				$role = get_role($role);
				if ($role == null) throw new Exception('Role not found.');
			} else if (get_class($role) === 'WP_Role') {
				WL_Dev::log('$role should be WP_Role instance');
			} else {
				throw new Exception('$role is neither string nor an instance of WP_User.');
			}
			$role->add_cap($this->cap);
			if (isset($this->roles)) {
				WL_Dev::log("role array exists, adding.");
				WL_Dev::log($this->roles);
				$this->roles[]=$role;
			} else {
				WL_Dev::log("role array doesn't exist, creating.");
				$this->roles = array($role);
			}
			return true;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			return false;
		}		
	}
	
	public function remove_role($role) {
		try {			
			if (is_string($role)) {
				WL_Dev::log('$role is string');
				$role = get_role($role);
				if ($role == null) throw new Exception('Role not found.');
			} else if (get_class($role) === 'WP_Role') {
				WL_Dev::log('$role should be WP_Role instance');
			} else {
				throw new Exception('$role is neither string nor an instance of WP_User.');
			}
			$role->remove_cap($this->cap);
			if (!isset($this->roles)) {
				WL_Dev::log("roles array doesn't exist, fetching.");
				$this->get_roles();
			} else {
				WL_Dev::log("roles array exists.");
				WL_Dev::log($this->roles);
			}
			unset($this->roles[in_array($role, $this->roles)]);
			return true;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			return false;
		}	
	}
	
	public function add_page($page_id) {
		//add page id to db
		//add page to param
	}
	
	public function remove_page($page_id) {
		
	}
	
	public function rename($new_name) {
		//
		try {
			if (!$this->data->get_whitelist_by('name', $new_name)) {
				global $wpdb;
				$success = $wpdb->update(
					$this->data->get_list_table(), 
					array(
						'name' => $new_name
					), 
					array ('id'=>$this->id)
				);
				if (!$success) {
					throw new Exception("Database coulnd't be updated.");
				} else {
					$this->name = $new_name;
					return true;					
				}
			} else {
				throw new Exception("list with such a name already exists");
			}
		} catch(Exception $e) {
			WL_Dev::error($e);
			return false;
		}
	}
}
