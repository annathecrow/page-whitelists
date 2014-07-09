<?php
	//these will be built by JavaScript, here for reference.
?>

<tr id="wlist_new" class="inline-edit-row quick-edit-row inline-editor" style=""><td colspan="6" class="colspanchange">

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
		



<div id="message" class="updated" <!--"error" for red border --><p>Whitelist <strong>created</strong>.</p></div>