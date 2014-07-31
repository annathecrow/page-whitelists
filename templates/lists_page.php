<div class="wrap">
<h2><?php _e("Whitelists",'whitelists'); ?></h2>
<style>
	.id-column {
		width:2em;
	}
	#wl-lists tfoot tr {
		width:100%;
	}
</style>
<table id="wl-lists" class="wp-list-table widefat fixed">
	<thead>
		<tr>
			<th scope="col" class="manage-column id-column">ID</th>
			<th scope="col" class="manage-column"><?php _e("Title",'whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Whitelisted pages",'whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Assigned to roles",'whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Assigned to users",'whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Allow creation of new pages",'whitelists'); ?></th>
			<!-- <th scope="col" class="manage-column">Date</th> -->
		</tr>
	</thead>
	<tbody>
		<?php
			
			foreach($lists as $ord=>$list) { ?>
					<tr id="wlist-<?php echo $list->get_id(); ?>" class="whitelist-row <?php echo ($ord%2==0)?'alternate':''; ?>">
			<th scope="row" class="id-column"><?php echo $list->get_id(); ?></th>
			<td><span class="wlist-name"><?php echo $list->get_name(); ?></span>
				<div class="row-actions">
					<span class="edit"><a href="#" id="edit-wlist-<?php echo $list->get_id(); ?>"><?php _e("Edit",'whitelists'); ?></a>|</span>
					<span class="trash"><a href="<?php echo wp_create_nonce("delete-wlist-".$list->get_id()); ?>" id="delete-wlist-<?php echo $list->get_id(); ?>"><?php _e("Delete",'whitelists'); ?></a></span>
				</div>
			</td>
			<td class="wlist-pages"><?php $list->the_pages(); ?></td>
			<td class="wlist-roles"><?php $list->the_roles();	?></td>
			<td class="wlist-users"><?php $list->the_users(); ?></td>
			<td class="wlist-strict"><?php echo ($list->is_strict())?__('no','whitelists'):__('yes','whitelists');?></td>
			
			<!--<td class="wlist-time"><abbr title="<?php echo mysql2date( 'Y/m/d h:i:s A', $list->get_time(),true); ?>"><?php echo mysql2date( 'Y/m/d', $list->get_time(),true); ?></abbr></td>-->
		</tr>			
			<?php }; ?>
	</tbody>
</table>
<p><a href="#" id="create-wlist"><?php _e("Create new...",'whitelists'); ?></a>
</p>
</div><img id="spinner" src="<?php echo site_url("/wp-admin/images/wpspin_light.gif");?>"/>