<ul id="wlists-choice">
	<?php foreach($all_wlists as $list) {
		//WL_Dev::log($assigned_wlists);
		if (!in_array($post->ID, $list->get_page_ids())) {
			$check = "";
		} else {
			$check = 'checked="checked"';
		}
		?>
		<li><label class="selectit"><input value="<?php echo $list->get_id(); ?>" type="checkbox" <?php echo $check; ?> name="wlists[]" id="in-wlist-<?php echo $list->get_id(); ?>"> <?php echo $list->get_name(); ?></label></li>		
	<?php } ?>
		
</ul>