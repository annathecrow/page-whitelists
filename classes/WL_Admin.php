<?php

/**
 * creates settings page
 */
class WL_Admin {
	private $data;
	
	function __construct($data,$file) {
		$this->template_path = plugin_dir_path($file)."templates/";
		$this->template_url = plugin_dir_url($file)."templates/";
		$this->data = $data;
	}
	
	
	public function add_menus() {
		//$plugin_title = __("Page Whitelists",'page-whitelists');
		$plugin_title = "Page Whitelists";
		
		
		add_submenu_page( 
			'options-general.php',
			$plugin_title, //label of the sidebar link
			$plugin_title, //title of the main options page
			'manage_options',
			'wl_lists', //the slug of the options page
			array($this,'render_lists_page')  
		);	
	}
	
	public function render_lists_page() {
		$lists = $this->data->get_all_whitelists();
		require_once $this->template_path."lists_page.php";
	}
	
	public function add_metabox() {
		if (!current_user_can('manage_options')) return;
		add_meta_box(
			'wlist-metabox',
			__('Associated Whitelists','page-whitelists'),
			array($this,'render_metabox'),
			'page',
			'side'
		);
	}
	
	public function render_metabox($post) {
		wp_nonce_field(-1,'wlist_onpage_edit');
		$all_wlists = $this->data->get_all_whitelists();
		require_once $this->template_path."metabox.php";		
	}
	
	public function save_metabox($page_id) {
		if (!isset( $_POST['wlist_onpage_edit'])) {
			return;
		} //nonce not set
		if (!wp_verify_nonce( $_POST['wlist_onpage_edit'])) {
			return;
		}//nonce not validated
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		} //just an autosave
		if (!current_user_can('edit_post',$page_id)) {
			return;
		} //user can't edit post
		
		$wlists = (isset($_POST['wlists']))?$_POST['wlists']:array();
		
		$all_whitelists = $this->data->get_all_whitelists();
		foreach ($all_whitelists as $list) {
			if (!in_array($list->get_id(), $wlists)) {
				$list->remove_page($page_id);
			} else {
				$list->add_page($page_id);
			}
		}
	}
	
	/***************** SCRIPTS AND STYLES **********************/
	
	public function enqueue_assets($hook) {
		$screen = get_current_screen();
		if($screen->id != 'settings_page_wl_lists') {
			return;
		}
		$script_path = $this->template_url. 'js/wl_lists.js';
		$style_path = $this->template_url. 'css/wl_lists.css';
		wp_enqueue_style('style-name', $style_path);
		wp_enqueue_script('wl_lists_js', $script_path, array('jquery'),'1.0.0',true);
		wp_localize_script( 'wl_lists_js', 'jsi18n', array(
			'del' => __( 'Delete', 'page-whitelists' ),
			'title' => __( 'Title', 'page-whitelists' ),
			'allowNew' => __('Allow creation of new pages','page-whitelists'),
			'wlistedPages' => __('Whitelisted pages','page-whitelists'),
			'assignedTo' => __('Assigned to users','page-whitelists'),
			'asToUsers' => __('Assigned to users','page-whitelists'),
			'asToRoles' => __('Assigned to roles','page-whitelists'),
			'cancel' => __('Cancel','page-whitelists'),
			'save' => __('Save','page-whitelists'),
			'createNew' => __('Create new...','page-whitelists'),
			'edit' => __('Edit','page-whitelists'),
			'saveWNameErr' => __('cannot save a whitelist without a name.','page-whitelists'),
			'createdSuccess' => __('Whitelist successfully created.','page-whitelists'),
			'editedSuccess' => __('Whitelist successfully edited.','page-whitelists'),
			'deletedSuccess' => __('Whitelist successfully deleted.','page-whitelists'),
			'err' => __('Error.','page-whitelists'),
			'confirmLeave' => __('You have unsaved changes. Do you want to continue?','page-whitelists'),
			'confirmDelete' => __('Are you sure you want to delete whitelist {listName}?','page-whitelists'),
) );
	}
	
	public function register_ajax() {
		add_action('wp_ajax_wl_delete', array($this,'ajax_delete'));
		add_action('wp_ajax_wl_load', array($this,'ajax_load'));
		add_action('wp_ajax_wl_save', array($this,'ajax_save'));
	}
	
	/***************** AJAX **********************/
	
	public function ajax_delete() {
		if (!current_user_can("manage_options")) die('user not allowed to edit settings');
		$id = $_POST['id'];
		$passed = check_ajax_referer( 'delete-wlist-'.$id, 'nonce', false);
		if (!$passed) die('nonce failed');
		$result = $this->data->delete_whitelist($id);
		if ($result[0]) {
			$reply = 'success';
		} else {
			$reply = $result[1];
		}
		die($reply);
	}
	
	public function ajax_load() {
		//FIRST STEP: build a "fresh" data array
		$data = array();
		$data['pages'] = array();
		$query = new WP_Query('post_type=page&posts_per_page=-1');
		while ($query->have_posts()) {
			$query->the_post();
			$data['pages'][] = array(
				'title'=> $query->post->post_title,
				'id' => $query->post->ID,
				'assigned'=>false
			);
		}
		wp_reset_postdata();
		
		$data['users'] = array();
		$query_args = array(
			'orderby'=>'ID'
		);
		$user_query = new WP_User_Query($query_args); 
		$users = $user_query->results;
		foreach($users as $user) {
			if (!user_can($user,"manage_options")) {
				$data['users'][] = array(
					'login' => $user->user_login,
					'id' => $user->ID,
					'assigned' => false
				);
			}	 
		}
		
		$all_roles = get_editable_roles();
		$data['roles'] = array();
		foreach ($all_roles as $rolename=>$roledata) {
			if (!isset($roledata['capabilities']['manage_options'])) {
				$data['roles'][$rolename] = false;	
			}
		}
		
		//SECOND STEP: mark assigned pages/roles/users
		if (isset($_POST['id'])) {
			$id = $_POST['id'];
			$list = $this->data->get_whitelist_by('id',$id);
			$assigned_pages = $list->get_page_ids();
			foreach ($data['pages'] as $key=>$page) {
				if (in_array($page['id'],$assigned_pages)) {
					$data['pages'][$key]['assigned']=true;
				}
			} 
			$assigned_users = $list->get_user_logins();
			foreach ($data['users'] as $key=>$user) {
				if (in_array($user['login'],$assigned_users)) {
					$data['users'][$key]['assigned']=true;
				}
			}
			 
			$assigned_roles = $list->get_role_names();
			foreach ($data['roles'] as $key=>$role) {
				if (in_array($key,$assigned_roles)) {
					$data['roles'][$key] = true;
				}
			} 
			$data['strict'] = $list->is_strict();
			$data['name'] = $list->get_name();
			$data['id'] = $list->get_id();
			$data['time'] = $list->get_time();
			$data['nonce'] = wp_create_nonce("edit-wlist".$list->get_id());
		} else {
			$data['strict'] = true;
			$data['id'] = '';
			$data['nonce'] = wp_create_nonce("create-wlist");
		}
		die(json_encode($data)); 		
	}
	
	public function ajax_save() {
		try {
			if (!current_user_can("manage_options")) {
				throw new Exception("insufficient capabilities");
			}	
			if ($_POST['name']=='') {
				throw new Exception("name-missing");
			} else {
				$name = $_POST['name'];
			};			
			
			if ($_POST['id']=='') {
				$passed = check_ajax_referer( 'create-wlist', 'nonce', false);
				if (!$passed) throw new Exception("nonce-failed");	
				$list = $this->data->create_whitelist($name,$_POST['strict']);
				if (!$list) {
					throw new Exception("unknown");				
				} elseif (get_class($list)!='WL_List') {
					throw new Exception("name-in-use");
				} else {
					$list_status = "created";
				}		
			} else {
				$passed = check_ajax_referer( 'edit-wlist'.$_POST['id'], 'nonce', false);
				if (!$passed) throw new Exception("nonce-failed");	
				$list = $this->data->get_whitelist_by('id',$_POST['id']);
				if (!$list) {
					throw new Exception("not-found");
				} else {
					$list_status = "edited";
				}
			}
			
			if ($name != $list->get_name()) {
				$renamed = $list->rename($name);
				if (!$renamed) {
					throw new Exception("could-not-rename");
				}
			}
			
			$assigned_pages = $list->get_page_ids();
			if ($_POST['pages']=='') {
				foreach ($assigned_pages as $page) {
					$success = $list->remove_page($page);
				}				
			} else {
				$pages = explode(",",$_POST['pages']);
				foreach ($pages as $page_id) {
					$success = $list->add_page($page_id);
				}
				foreach ($assigned_pages as $page_id) {
					if (!in_array($page_id,$pages)) {
						$list->remove_page($page_id);
					}
				}
			}
			
			$assigned_users = $list->get_users();
			if ($_POST['users']=='') {
				foreach ($assigned_users as $user) {
					$success = $list->remove_user($user);
				}
			} else {
				$users = explode(",",$_POST['users']);
					foreach ($users as $user_id) {
					$success = $list->add_user($user_id);
				}				
				foreach ($assigned_users as $user) {
					if (!in_array($user->ID,$users)) {
						$list->remove_user($user);
					}
				}
			} 
			$assigned_roles = $list->get_roles();
			if ($_POST['roles']=='') {
				foreach ($assigned_roles as $role) {
					$success = $list->remove_role($role);
				}
			} else {
				$roles = explode(",",$_POST['roles']);
					foreach ($roles as $role_name) {
					$success = $list->add_role($role_name);
				}
				
				foreach ($assigned_roles as $role) {
					if (!in_array($role->name,$roles)) {
						$success = $list->remove_role($role);
					}
				}	
			}
			if ($_POST['strict']=='false') {
				$list->set_strict(false);
			} else {
				$list->set_strict();
			}
			
			$result = array(
				"success"=>true,
				"id"=>$list->get_id(),
				"name"=>$list->get_name(),
				"message"=>$list_status,
				"pages"=>$list->get_page_ids(),				
				"users"=>$list->get_user_logins(),
				"roles"=>$list->get_role_names(),
				'strict'=>$list->is_strict(),
			);
			$result['deleteNonce'] = ($list_status=="created")?wp_create_nonce("delete-wlist-".$list->get_id()):null;
			if (!$success) {
				$result['success']=false;
				$result['message']='addition-errors';
			}
			die(json_encode($result));
		} catch (Exception $e) {
			$result = array(
				"success"=>false,
				"message" => $e->getMessage()
			);
			die(json_encode($result));
		}		
	}
}