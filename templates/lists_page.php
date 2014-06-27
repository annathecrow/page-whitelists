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
			<th scope="col" class="manage-column">Assigned to</th>
			<th scope="col" class="manage-column">Date</th>
		</tr>
	</thead>
	<tbody>
		<tr id="wlist-1" class="whitelist-row alternate">
			<th scope="row" class="id-column">1</th>
			<td><a href="##">My Whitelist</a>
				<div class="row-actions">
					<span class="edit"><a href="#">Edit</a>|</span>
					<span class="trash"><a href="#">Delete</a></span>
				</div>
			</td>
			<td>Contributor</td>
			<td><abbr title="2014/05/23 7:52:41 AM">2014/05/23</abbr></td>
		</tr>
		<tr id="wlist-2" class="whitelist-row">
			<td>2</td>
			<td><a href="#">Special list</a>
				<div class="row-actions">
					<span class="edit"><a href="#">Edit</a>|</span>
					<span class="trash"><a href="#">Delete</a></span>
				</div>
			</td>
			<td>Special role</td>
			<td><abbr title="2014/05/23 7:52:41 AM">2014/05/23</abbr></td>
		</tr>
		<tr id="wlist-3" class="whitelist-row alternate">
			<td>3</td>
			<td><a href="#">Sergeant's List</a>
				<div class="row-actions">
					<span class="edit"><a href="#">Edit</a>|</span>
					<span class="trash"><a href="#">Delete</a></span>
				</div>
			</td>
			<td>sergeant</td>
			<td><abbr title="2014/05/23 7:52:41 AM">2014/05/23</abbr></td>
		</tr>
		<tr id="wlist_new" class="inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post alternate inline-editor" style=""><td colspan="4" class="colspanchange">

		<fieldset class="inline-edit-col-left"><div class="inline-edit-col">
			<h4>Create New...</h4>
	
			<label>
				<span class="title">Title</span>
				<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value=""></span>
			</label>
			<span class="title">Assigned to users</span>
			<ul class="cat-checklist">
				<li id="category-2" class="popular-category"><label class="selectit"><input value="2" type="checkbox" name="post_category[]" id="in-category-2"> aktuality</label>
					<ul class="children">
						<li id="category-4" class="popular-category"><label class="selectit"><input value="4" type="checkbox" name="post_category[]" id="in-category-4"> news</label></li>
					</ul>
				</li>
				<li id="category-7"><label class="selectit"><input value="7" type="checkbox" name="post_category[]" id="in-category-7"> Nezařazené</label></li>
				<li id="category-8" class="popular-category"><label class="selectit"><input value="8" type="checkbox" name="post_category[]" id="in-category-8"> test</label></li>
				<li id="category-1" class="popular-category"><label class="selectit"><input value="1" type="checkbox" name="post_category[]" id="in-category-1"> Uncategorized</label></li>
			</ul>
			<span class="title">Assigned to roles</span>
			<ul class="cat-checklist">
				<li id="category-2" class="popular-category"><label class="selectit"><input value="2" type="checkbox" name="post_category[]" id="in-category-2"> aktuality</label>
					<ul class="children">
						<li id="category-4" class="popular-category"><label class="selectit"><input value="4" type="checkbox" name="post_category[]" id="in-category-4"> news</label></li>
					</ul>
				</li>
				<li id="category-7"><label class="selectit"><input value="7" type="checkbox" name="post_category[]" id="in-category-7"> Nezařazené</label></li>
				<li id="category-8" class="popular-category"><label class="selectit"><input value="8" type="checkbox" name="post_category[]" id="in-category-8"> test</label></li>
				<li id="category-1" class="popular-category"><label class="selectit"><input value="1" type="checkbox" name="post_category[]" id="in-category-1"> Uncategorized</label></li>
			</ul>	
		</div></fieldset>
	
		<fieldset class="inline-edit-col-right"><div class="inline-edit-col">
			<span class="title">Whitelisted pages</span>
			<ul class="cat-checklist">
				<li id="category-2" class="popular-category"><label class="selectit"><input value="2" type="checkbox" name="post_category[]" id="in-category-2"> aktuality</label>
					<ul class="children">
						<li id="category-4" class="popular-category"><label class="selectit"><input value="4" type="checkbox" name="post_category[]" id="in-category-4"> news</label></li>
					</ul>
				</li>
				<li id="category-7"><label class="selectit"><input value="7" type="checkbox" name="post_category[]" id="in-category-7"> Nezařazené</label></li>
				<li id="category-8" class="popular-category"><label class="selectit"><input value="8" type="checkbox" name="post_category[]" id="in-category-8"> test</label></li>
				<li id="category-1" class="popular-category"><label class="selectit"><input value="1" type="checkbox" name="post_category[]" id="in-category-1"> Uncategorized</label></li>
			</ul>

	
		</div></fieldset>
			<p class="submit inline-edit-save">
			<a accesskey="c" href="#inline-edit" class="button-secondary cancel alignleft">Cancel</a>
			<input type="hidden" id="_inline_edit" name="_inline_edit" value="6c6891c483">				<a accesskey="s" href="#inline-edit" class="button-primary save alignright">Save</a>
				<span class="spinner"></span>
						<input type="hidden" name="post_view" value="list">
			<input type="hidden" name="screen" value="edit-post">
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