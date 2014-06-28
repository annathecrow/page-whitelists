<div class="wrap">
<h2><?php echo "Whitelists"; ?></h2>
<p>Creating/editing/deleting whitelists.</p>
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
			<th scope="col" class="manage-column">Title</th>
			<th scope="col" class="manage-column">Assigned to roles</th>
			<th scope="col" class="manage-column">Assigned to users</th>
			<th scope="col" class="manage-column">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php
			
			foreach($lists as $ord=>$list) { ?>
					<tr id="wlist-<?php echo $list->get_id(); ?>" class="whitelist-row <?php echo ($ord%2==0)?'alternate':''; ?>">
			<th scope="row" class="id-column"><?php echo $list->get_id(); ?></th>
			<td><a href="##"><?php echo $list->get_name(); ?></a>
				<div class="row-actions">
					<span class="edit"><a href="#">Edit</a>|</span>
					<span class="trash"><a href="#">Delete</a></span>
				</div>
			</td>
			<td><?php echo $list->get_roles(); ?></td>
			<td><?php echo $list->get_users(); ?></td>
			<td><abbr title="<?php echo mysql2date( 'Y/m/d h:i:s A', $list->get_time(),true); ?>"><?php echo mysql2date( 'Y/m/d', $list->get_time(),true); ?></abbr></td>
		</tr>			
			<?php }; ?>
		<tr id="wlist_new" class="inline-edit-row quick-edit-row inline-editor" style=""><td colspan="5" class="colspanchange">

		<fieldset><div class="inline-edit-col">
			<h4>Create New...</h4>
	
			<label>
				<span class="title">Title</span>
				<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value=""></span>
			</label>
			<span class="title">Assigned to users</span>
			<ul class="cat-checklist">
				<li id="user-2"><label class="selectit"><input value="2" type="checkbox" name="users[]" id="user-id-2"> Colonel z Kentucky</label></li>
				<li id="category-3"><label class="selectit"><input value="3" type="checkbox" name="users[]" id="user-id-3"> Sergeant</label></li>
			</ul>
			<span class="title">Assigned to roles</span>
			<ul class="cat-checklist">
				<li id="role-editor"><label class="selectit"><input value="editor" type="checkbox" name="roles[]" id="role-id-2"> Editor</label></li>
				<li id="role-author"><label class="selectit"><input value="author" type="checkbox" name="roles[]" id="role-id-3"> Author</label></li>
				<li id="role-contributor"><label class="selectit"><input value="contributor" type="checkbox" name="roles[]" id="role-id-4"> Contributor</label></li>
				<li id="role-subscriber"><label class="selectit"><input value="subscriber" type="checkbox" name="roles[]" id="role-id-5"> Subscriber</label></li>
			</ul>	
		</div></fieldset>
		<fieldset>
			<div class="inline-edit-col">
				<span class="title">Whitelisted pages</span>
				<ul class="cat-checklist">
					<li id="page-289"><label class="selectit"><input value="289" type="checkbox" name="pages[]" id="page-id-289"> Sergeant's very own page</label></li>
					<li id="page-280"><label class="selectit"><input value="280" type="checkbox" name="pages[]" id="page-id-280"> Colonel can't touch this</label></li>
				</ul>
			</div>
		</fieldset>
		<p class="submit inline-edit-save">
			<a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft">Cancel</a>
			<a accesskey="s" href="#inline-edit" class="button-primary save alignright">Save</a>
			<span class="error" style="display:none"></span>
			<br class="clear">
		</p>
		</td></tr>
	</tbody>
</table>
<p><a href="#">Create new...</a>
</p>
<form action="options.php" method="post"> 
</form></div>