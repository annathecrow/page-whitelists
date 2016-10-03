<?php

class WL_List {
	private $id;
	private $name;
	private $time;
	private $strict;
	private $cap;
	private $data;
	private $users;
	private $roles;
	private $page_ids;
	
	
	/**
	 * 
	 * 
	 */
	public function __construct(WL_Data $data,Array $list_info) { 
		$this->data = $data;
		$this->id = $list_info['id'];
		$this->name = $list_info['name'];
		$this->time = $list_info['time'];
		$this->strict = ($list_info['strict']||$list_info['strict']==1)?true:false;
		$this->cap = 'edit_whitelist_'.$this->id;		
	}
	
	public function get_id() {
		return $this->id;
	}
	
	public function the_id() {
		echo $this->id;
		return true;
	}
	
	public function get_name() {
		return $this->name;
	}
	
	public function the_name() {
		echo $this->name;
		return true;
	}
	
	public function get_time() {
		return $this->time;
	}
	
	public function get_cap() {
		return $this->cap;
	}
	
	public function is_strict() {
		return ($this->strict);
	}
	
	public function get_users() {
		if (isset($this->users)) {
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
	
	public function get_user_logins() {
		$users = $this->get_users();
		$namearray = array();
		foreach ($users as $user) {
			$namearray[] = $user->user_login;
		}
		sort($namearray);
		return $namearray;
	}
	
	public function the_users($delimiter = ", ") {
		echo implode($delimiter,$this->get_user_logins());
	}
	
	public function get_roles() {
		if (isset($this->roles)) {
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
	
	public function get_role_names() {
		$roles = $this->get_roles();
		$namearray = array();
		foreach ($roles as $role) {
			$namearray[] = $role->name;
		}
		sort($namearray);
		return $namearray;
	}
	
	public function the_roles($delimiter = ", ") {
		echo implode($delimiter,$this->get_role_names());
	}
	
	public function get_page_ids() {
		if (isset($this->page_ids)) return $this->page_ids;
		global $wpdb;
		$list_page_table = $this->data->get_list_page_table();
		$query = $wpdb->prepare("SELECT * FROM $list_page_table WHERE list_id = %s",$this->id);
		$result = $wpdb->get_results($query,ARRAY_A);
		$pages = array();
		foreach($result as $page_item) {
			$pages[] = $page_item['page_id'];
		}
		sort($pages);
		$this->page_ids = $pages;
		return $pages;
	}	
	
	public function get_pages() {
		if (isset($this->pages)) return $this->pages;
		$page_ids = $this->get_page_ids();
        $pages = array();
        
        if (sizeof($page_ids)==0) { //no pages
            $this->pages = $pages;
            return $pages;
        }
        
		
        $args = array(
            'posts_per_page'=>'-1',
            'include' =>implode(", ",$page_ids),
            'post_type'=>'page',
            'post_status'=>'publish,private,draft,pending,future'
        );
        
        $pages = get_posts($args);
               
		$this->pages = $pages;
		return $pages;
	}
	
	public function the_pages($delimiter = ", ",$full=false) {
		if ($full) {
			$pages = $this->get_pages();
			$out_a = array();
			foreach ($pages as $page) {
				$out_a[] = '<a href="'.get_permalink($page->ID).'">'.$page->post_title."</a> (".$page->ID.")";
			}
			echo implode(", ",$out_a);			
		} else {
			$page_ids = $this->get_page_ids();
			echo implode($delimiter,$page_ids);
		}
		
	}
	
	public function add_user($user) {
		try {			
			if (is_numeric($user)) {
				$user = get_user_by('id',$user);
				if (!$user) {throw new Exception('User not found.',0);};
			} else if (get_class($user) !== 'WP_User') {
				throw new Exception('$user is neither id nor an instance of WP_User.',0);
			}
			if (!isset($this->users)) {
					$this->get_users();
				} 			
			if (!in_array($user, $this->users)) {
				$user->add_cap($this->cap);
				$this->users[]=$user;
			}			
			return true;		
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		}	
	}
	
	public function remove_user($user) {
		try {			
			if (is_numeric($user)) {
				$user = get_user_by('id',$user);
				if (!$user) {throw new Exception('User not found.',0);}
			} else if (get_class($user) !== 'WP_User') {
				throw new Exception('$user is neither numeric nor an instance of WP_User.',0);
			}
			if (!isset($this->users)) {
				$this->get_users();
			}
			$user->remove_cap($this->cap);
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
				$role = get_role($role);
				if ($role == null) throw new Exception('Role not found.',0);
			} else if (get_class($role) === 'WP_Role') {
			} else {
				throw new Exception('$role is neither string nor an instance of WP_User.',0);
			}
			if (!isset($this->roles)) {
				$this->get_roles();
			}
			if (!in_array($role,$this->roles)) {
				$this->roles[]=$role;
				$role->add_cap($this->cap);
			}			
			return true;
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		}		
	}
	
	public function remove_role($role) {
		try {			
			if (is_string($role)) {
				$role = get_role($role);
				if ($role == null) throw new Exception('Role not found.',0);
			} else if (get_class($role) === 'WP_Role') {
			} else {
				throw new Exception('$role is neither string nor an instance of WP_Role.',0);
			}
			
			if (!isset($this->roles)) {
				$this->get_roles();
			}
			$role->remove_cap($this->cap);
			unset($this->roles[in_array($role, $this->roles)]);
			return true;
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		}	
	}
	
	public function add_page($page_id) {
		if(!isset($this->page_ids)) $this->get_page_ids();
		try {
			if (in_array($page_id,$this->page_ids)) {
				throw new Exception ("Page already added to this list.",1);
			}
			$page = get_post($page_id);
			if ($page === null) {
				throw new Exception("Page with this id doesn't exist.",0);
			} else if ($page->post_type !='page') {
				throw new Exception("This id doesn't belong to a page.",0);
			} else {
				global $wpdb;
				$success = $wpdb->insert(
					$this->data->get_list_page_table(),
					array(
						'list_id'=>$this->id,
						'page_id'=>$page_id
					)				
				);
				if ($success===false) {
					throw new Exception("Couldn't write into database.",0);
				} else {
					if (!isset($this->page_ids)) {
						$this->get_page_ids();
					} else {
						$this->page_ids[] = $page_id;
					}					
					return true;
				}
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
	
	public function remove_page($page_id) {
		try {
			$page = get_post($page_id);
			if ($page === null) {
				throw new Exception("Page with this id doesn't exist.",0);
			} else if ($page->post_type !='page') {
				throw new Exception("This id doesn't belong to a page.",0);
			} else {
				global $wpdb;
				$success = $wpdb->delete(
					$this->data->get_list_page_table(),
					array(
						'list_id'=>$this->id,
						'page_id'=>$page_id
					)				
				);
				if ($success===false) {
					throw new Exception("Not in database.",1);
				} else {
					if (!isset($this->page_ids)) {
						$this->get_page_ids();
					} else {
						unset($this->page_ids[in_array($page_id, $this->page_ids)]);
						$this->page_ids = array_values($this->page_ids);
					}					
					return true;
				}
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
				if ($success===false) {
					throw new Exception("Database couldn't be updated.",0);
				} else {
					$this->name = $new_name;
					return true;					
				}
			} else {
				throw new Exception("list with such a name already exists",1);
			}
		} catch(Exception $e) {
			if ($e->getCode()==1) {
				WL_Dev::log($e->getMessage());
				return true;
			} else {
				WL_Dev::error($e);
				return false;
			}
		}
	}
	
	function set_strict($strict = true) {
		try {
		    $strict_num = ($strict)?1:0;
			global $wpdb;
				$success = $wpdb->update(
					$this->data->get_list_table(), 
					array(
						'strict' => $strict_num
					), 
					array ('id'=>$this->id)
				);
				if ($success===false) {
					throw new Exception("Database couldn't be updated.",0);
				} else {
					$this->strict = $strict;
					return true;					
				}			
		} catch (Exception $e) {
			WL_Dev::error($e);
			return false;
		} 
	}
}
