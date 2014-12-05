<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

$query_args = array(
	'orderby'=>'ID'
);
$user_query = new WP_User_Query($query_args);

foreach ($users_query as $user) {
	foreach ($user->allcaps as $cap => $v) {
		if (strpos($cap,"edit_whitelist_")!== false) {
			$user->remove_cap($cap);
		}
	}
} 

$all_roles = get_editable_roles();
foreach ($all_roles as $rolename=>$roledata) {
	$role = get_role($rolename);
	foreach ($roledata->capabilities as $cap => $v) {
		if (strpos($cap,"edit_whitelist_")!== false) {
			$role->remove_cap($cap);
		}
	}
}

$list_table = get_option('wlist_list_table');
delete_option('wlist_list_table');
$list_page_table = get_option('wlist_list_page_table');
delete_option('wlist_list_page_table');
global $wpdb;
$wpdb->query("DROP TABLE IF EXISTS $list_table");
$wpdb->query("DROP TABLE IF EXISTS $list_page_table");
delete_option('wlist_plugin_title');
?>