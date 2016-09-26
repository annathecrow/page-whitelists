<div class="wrap">
<h1>Page Whitelists - <?php _e('manage whitelists','page-whitelists') ?></h1>
<style>
	.id-column {
		width:2em;
	}
	#wl-lists tfoot tr {
		width:100%;
	}
</style>

<h2><?php _e("Edit Whitelists",'page-whitelists'); ?></h2>
<table id="wl-lists" class="wp-list-table widefat fixed">
	<thead>
		<tr>
			<th scope="col" class="manage-column id-column">ID</th>
			<th scope="col" class="manage-column"><?php _e("Title",'page-whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Whitelisted pages",'page-whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Assigned to roles",'page-whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Assigned to users",'page-whitelists'); ?></th>
			<th scope="col" class="manage-column"><?php _e("Allow creation of new pages",'page-whitelists'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			
			foreach($lists as $ord=>$list) { ?>
					<tr id="wlist-<?php echo $list->get_id(); ?>" class="whitelist-row <?php echo ($ord%2==0)?'alternate':''; ?>">
			<th scope="row" class="id-column"><?php echo $list->get_id(); ?></th>
			<td><span class="wlist-name"><?php echo $list->get_name(); ?></span>
				<div class="row-actions">
					<span class="edit"><a href="#" id="edit-wlist-<?php echo $list->get_id(); ?>"><?php _e("Edit",'page-whitelists'); ?></a>|</span>
					<span class="trash"><a href="<?php echo wp_create_nonce("delete-wlist-".$list->get_id()); ?>" id="delete-wlist-<?php echo $list->get_id(); ?>"><?php _e("Delete",'page-whitelists'); ?></a></span>
				</div>
			</td>
			<td class="wlist-pages">
			    <?php $pages = $list->get_pages();
                    $page_strings = array();
                    $pagelength = sizeof($pages);
                    for($i = 0; $i < $pagelength; $i++) {
                        if (!is_numeric($pages[$i]->ID)) {
                            continue;
                        }
                        $link = '<a href="'.get_permalink($pages[$i]->ID).'">'.$pages[$i]->post_title.'</a> ('.$pages[$i]->ID.')';
                        if ($i < 5) {
                            $page_strings[] ='<span class="wlist-page">'.$link.', </span>';
                        } elseif ($i == $pagelength - 1) {
                            $page_strings[] ='<span class="wlist-page more"><a href="'.$link.'</span>'; //no comma
                        } else {
                            $page_strings[] ='<span class="wlist-page more"><a href="'.$link.', </span>';
                        }
                    };
                    echo implode("",$page_strings);
                    if (sizeof($pages) > 5) {
                        echo '...<a href="" class="more-link">(more)</a>';
                    }
                    //TODO strip dangling comma
			    ?>
			</td>
			<td class="wlist-roles"><?php $list->the_roles();	?></td>
			<td class="wlist-users"><?php $list->the_users(); ?></td>
			<td class="wlist-strict"><?php echo ($list->is_strict())?__('no','page-whitelists'):__('yes','page-whitelists');?></td>
		</tr>			
			<?php }; ?>
	</tbody>
</table>
<p><a href="#" id="create-wlist"><?php _e("Create new...",'page-whitelists'); ?></a>
</p>
</div><img id="spinner" src="<?php echo site_url("/wp-admin/images/wpspin_light.gif");?>"/>