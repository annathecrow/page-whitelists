<?php

class WL_Access_Manager {
	private $data;
	
	public function __construct($data) {
		$this->data = $data;
	}
	
	/*
	 * Check if we're on page edit form
	 * 
	 * @return bool  
	 */			
	function on_edit_page_form() {
		if ( ! function_exists('get_current_screen') || ! is_admin() || !current_user_can( 'edit_pages' ) ) return false;
		$s = get_current_screen();
		return ($s instanceof WP_Screen && $s->id === 'page');
	}
	
	/*
	 * Check if user has access to a page
	 * 
	 * @param int $page ID of page to check for
	 * @param int $user (optional) ID of user to check for (if not set, checking for current user)
	 * 
	 * @return bool  
	 */			
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
	
	/*
	 * Check if user is allowed to create new pages (does not belong to a stric whitelist)
	 *
	 * @param int $user (optional) ID of user to check for (if not set, checking for current user)
	 * 
	 * @return bool  
	 */
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
	}
	
	/*
	 * Repair page counts on a page listing
	 *
	 * @param $views
	 */
	function repair_page_counts($views) {
		global $wp_query;
		global $wpdb;
		$request = $wp_query->request; //take original request used to build this page
		$new_requests = array();
		$status_types = array('publish','future','draft','pending','private'); //take all possible statuses post can have
		//build queries to get the number of each post status
		$new_requests['all'] = preg_replace('(SELECT.*?FROM)', 'SELECT COUNT(*) FROM', $request);
		foreach ($status_types as $type) {
			$req = "$wpdb->posts.post_status = '$type'";		
			$new_requests[$type] = preg_replace('(\([a-zA-Z0-9_\.]+?post_status.*?\))',"($req)",$new_requests['all']);
			$all_types_request[] = $req;
		}
		$new_requests['all'] = preg_replace('(\([a-zA-Z0-9_\.]+?post_status.*?\))',"(".implode(' OR ',$all_types_request).")",$new_requests['all']); 
		foreach ($new_requests as $type=>$req) {
			$count = $wpdb->get_var($req); //call query, get count
			if (isset($views[$type])) $views[$type] = preg_replace( '/\(.+\)/U', '('.$count.')', $views[$type] );
			//replace counts in html
		}; 
		return $views;		
	}

	/*
	 * Remove all non-whitelisted pages from page listing
	 *
	 * @param WP Query $query
	 */
	function filter_displayed($query) {
		if (!isset($query) || strpos($query->get('post_type'),'page')===false) return; //if the current query doesn't display pages, do nothing		
		$user = wp_get_current_user();
        
        WL_Dev::log("filtering listing");
        
        if (function_exists('get_current_screen')) {
            $s = get_current_screen();
            $screenless = !($s instanceof WP_Screen);
           
        } else {
            $screenless = true;
        }
        
        if ((!$screenless && $s->id == 'edit-page') || $this->data->settings['filter_all_listings']) {
            $pages = $this->data->get_accessible_pages($user);
            if (!$pages) return $query;
            $query->set('post__in',$pages);
            
            
            if (!$screenless) {
                WL_Dev::log("adding repair_page_counts filter");
                add_filter( "views_".$s->id , array($this, 'repair_page_counts'), 10, 1); //attempt to repair page counts     
            }
        }
        return $query;			
	}
		
	/*
	 * Bar user from editing non-whitelisted pages
	 *
	 */	
	function filter_editable() {
		global $typenow;
		if ($typenow == 'page') {
			if (isset($_GET['post'])) {
				$page_id = $_GET['post'];
				if (!$this->has_access($page_id)) {
					wp_die(__('You are not allowed to edit this page.'));			
				};	
			}			
		}		
	}
	
	/*
	 * Bar user from creating pages if the user is assigned to a strict whitelist
	 *
	 */	
	function filter_can_create() {
		global $typenow;
		if ($typenow == 'page') {
			if (!$this->can_create_new()) {
				wp_die(__('You are not allowed to create new pages.'));
			}
		}		
	}
	
	/*
	 * Add newly created page to all user's whitelists
	 *
	 */	
	function add_new_to_list($new,$old,$post) {
		if ($post->post_type != 'page') return;
		if ($new == 'inherit' || $new == 'auto-draft') return;
		if ($old != 'new' && $old != 'auto-draft') return;
		if ($this->can_create_new()) {
			$page_id = $post->ID;
			$lists = $this->data->get_user_whitelists(wp_get_current_user());
			foreach ($lists as $list) {
				$list->add_page($page_id);
			}
		} else {
			wp_die(__('You are not allowed to create new pages.'));
		}
	}
	
	/*
	 * Remove edit link from admin bar if user isn't allowed to edit page
	 *
	 */	
	function filter_admin_bar() {
		global $wp_admin_bar;
		if (!isset($wp_admin_bar)) return;
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
	
	/*
	 * Hide links to add new pages if user isn't allowed to
	 *
	 */	
	function css() {
		if ($this->can_create_new()) return;
		if (!function_exists('get_current_screen')) return;
		$s = get_current_screen();
		if ($s->id == 'edit-page') {
			echo '<style>.edit-php.post-type-page .add-new-h2,.post-php.post-type-page .add-new-h2 {display:none;}</style>';
		} elseif ($s->id == 'pages_page_cms-tpv-page-page') {
			echo '<style>p.cms_tpv_action_add_and_edit_page {display:none}</style>';
		}		  
	}


	/*
	 * Remove Add Page option from menu if user isn't allowed to create new pages 
	 *
	 */
	function filter_menus() {
		if ($this->can_create_new()) return;
		$create_page = remove_submenu_page( 'edit.php?post_type=page', 'post-new.php?post_type=page' );
	}
		
	function access_check() {
		if (current_user_can("manage_options")) return;
		if (is_admin()) {
			add_action( 'pre_get_posts', array($this, 'filter_displayed') );
			add_action('load-post-new.php',array($this,'filter_can_create'));
			add_action('load-post.php',array($this,'filter_editable'));
			add_action('transition_post_status',array($this,'add_new_to_list'),10,3);
			add_action('admin_head',array($this,'css'));
		}
		add_action( 'wp_before_admin_bar_render', array($this, 'filter_admin_bar') );
		add_action('admin_menu',array($this, 'filter_menus'));
	}
	
	
}
