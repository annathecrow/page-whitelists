<?php

class WL_Access_Manager {
	private $data;
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	function on_page_listing() {
		if ( ! is_admin() || ! current_user_can( 'edit_pages' ) ) return FALSE;
		$s = get_current_screen();
		return ( $s instanceof WP_Screen && $s->id === 'edit-page' );
	}
			
	function on_edit_page_form() {
		if ( ! is_admin() || !current_user_can( 'edit_pages' ) ) return false;		
		$s = get_current_screen();
		return ($s instanceof WP_Screen && $s->id === 'page');
	}
	
	function has_access($page,$user = null) {
		if ($user == null) {
			$user = wp_get_current_user();
		}
		$pages = $this->data->get_accessible_pages($user);
		if (!$pages) return true;
		if (sizeof($pages)==0) return false;
		if (in_array($page,$pages)) {
			return true;
		} else {
			return false;
		}
	}
	
	function can_create_new($user = null) {
		if ($user == null) {
			$user = wp_get_current_user();
		}
		$lists = $this->data->get_user_whitelists($user);
		if (sizeof($lists)==0) return true;
		foreach ($lists as $list) {
			if ($list->is_strict()) {
				return false;
			}
		}
		return true;
		//if user doesn't belong to a strict whitelist, return true
	}
	
	function filter_displayed($query) {		
		//WL_Dev::log("running filter_displayed");
		$user = wp_get_current_user();
		$pages = $this->data->get_accessible_pages($user);
		if (!$pages) return true;
		$query->set('post__in',$pages);
	}
	
	function filter_editable() {
		global $post;
		$page_id = $post->ID;
		//WL_Dev::log("running filter_editable on page $page_id");
		if (!$this->has_access($page_id)) {
			wp_die( __('You are not allowed to edit this page.') );
		}
	}
	
	function run_page_filters($query) {
		//WL_Dev::log("running page filters");
		if ($this->on_edit_page_form()) {
			$this->filter_editable();
		};
		if ($this->on_page_listing()) {
			$this->filter_displayed($query);
		}		
	}
	
	function filter_admin_bar() {
		global $wp_admin_bar;
		if (!$this->can_create_new()) {
			$wp_admin_bar->remove_node('new-page');			
		};
		global $post;
		if (is_admin() || get_post_type($post)!='page') return;
		$page_id = $post->ID;
		if (!$this->has_access($page_id)) {
			$wp_admin_bar->remove_menu('edit');			
		};
		
		
	}
	
	function new_page_check($post) {
		if (get_post_type($post) != 'page') return;
		if ($this->can_create_new()) {
			WL_Dev::log("user can create pages");
			$page_id = $post->ID;
			foreach ($lists as $list) {
				$list->add_page($page_id);
			}
		} else {
			wp_die(__('You are not allowed to create new pages.'));
		}		
	}
	function css_cleanup() {
		//WL_Dev::log("adding css style");
		echo '<style>.edit-php.post-type-page .add-new-h2,.post-php.post-type-page .add-new-h2 {display:none;}</style>';
	}

	function filter_menus() {
		if ($this->can_create_new()) return;
		add_action('admin_head',array($this, 'css_cleanup'));
		$page = remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' );
	}
	
	function access_check() {
		if (current_user_can("manage_options")) return;
		if (is_admin()) add_action( 'pre_get_posts', array($this, 'run_page_filters') );
		add_action( 'wp_before_admin_bar_render', array($this, 'filter_admin_bar') );
		add_action('admin_menu',array($this, 'filter_menus'));
		add_action('new_to_auto-draft', array($this,'new_page_check'), 10, 3);
	}
	
	
}
