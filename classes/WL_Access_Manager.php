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
			wp_die( __('You are not allowed to access this part of the site') );
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
		global $post;
		if (is_admin() || get_post_type($post)!='page') return;
		$page_id = $post->ID;
		//WL_Dev::log("running filter_admin_bar on page $page_id");
		if (!$this->has_access($page_id)) {
			$wp_admin_bar->remove_menu('edit');
		};
	}
	
	function new_page_check($post) {
		if (get_post_type($post) != 'page') return;
		$lists = $this->data->get_user_whitelists($post->post_author);
		if (sizeof($lists)==0) return;
		if ($this->can_create_pages($user)) {
			WL_Dev::log("user can create pages");
			
			$page_id = $post->ID;
			foreach ($lists as $list) {
				$list->add_page($page_id);
			}
		} else {
			wp_die(__('You are not allowed to create new pages.'));
		}		
	}
	
	function access_check() {
		if (is_admin()) add_action( 'pre_get_posts', array($this, 'run_page_filters') );
		//find the right hook that won't million times over but still will work
		add_action( 'wp_before_admin_bar_render', array($this, 'filter_admin_bar') );
	}
	
	function remove_menus() {
		//how do I check for rights? Do I use a capability? Do I roll through the lists every time?
		$remove_menu_items = array(__('Links'));
		global $menu;
		end ($menu);
		while (prev($menu)){
			$item = explode(' ',$menu[key($menu)][0]);
			if(in_array($item[0] != NULL?$item[0]:"" , $remove_menu_items)){
			unset($menu[key($menu)]);}
		}
	}
}
