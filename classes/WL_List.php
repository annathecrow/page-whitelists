<?php

class WL_List {
	private $id;
	private $name;
	private $time;
	
	public function __construct($id, $name, $time) {
		$this->id = $id;
		$this->name = $name;
		$this->time = $time;
		
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
	
	public function get_users() {
		$query_args = array(
			'orderby'=>'ID'
		);
		$user_query = new WP_User_Query($query_args);
		$users = $user_query->results;
		$assigned_users= array();
		foreach($users as $key=>$user) {
			if (array_key_exists('edit_whitelist_'.$this->id, $user->caps)){
				$assigned_users[] = $user;
			};
		}
		return $assigned_users;
	}
	
	public function get_roles() {
		//get all roles
		//check which have assigned this list
		//return them as an array of WP_Role instances
		return "";
	}
	
	public function get_pages() {
		
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
			$user->add_cap('edit_whitelist_'.$this->id);
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
			$user->remove_cap('edit_whitelist_'.$this->id);
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
			$role->add_cap('edit_whitelist_'.$this->id);
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
			$role->remove_cap('edit_whitelist_'.$this->id);
			return true;
		} catch (Exception $e) {
			WL_Dev::log($e->getMessage());
			return false;
		}	
	}
	
	public function create() {
		
	}
	
	public function update() {
		
	}
	
	public function delete() {
		
	}
}
