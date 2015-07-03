<h3><?php _e('Assigned Page Whitelists','page-whitelists'); ?></h3>
<table class="form-table">
	<tbody>
		<tr><th scope="row"><?php _ex('Page Whitelists','on edit user form','page-whitelists'); ?></th>
			<td>
				<ul>
					<?php foreach($whitelists as $wlist):
						?>
						<li><label><input type="checkbox" name="wl_assigned_whitelists[]" value="<?php $wlist->the_id(); ?>" <?php if (in_array($user->user_login, $wlist->get_user_logins())) {echo "checked";} ?>/>
							<?php $wlist->the_name(); ?> - <em><?php
								$pages = $wlist->get_page_ids();
								$listed_pages = array();
								$output = "";
								$num_to_list = 3;
								$num_all = sizeof($pages);
								if ($num_all>$num_to_list) {
									for ($i = 0; $i <$num_to_list;$i++) {
										$listed_pages[] = get_the_title($pages[$i]);
									}
									$output = implode(", ",$listed_pages);
									$n_more = $num_all-$num_to_list;
									$output .= ",... ".sprintf( _n('and %d more.','and %d more.', $n_more, 'page-whitelists'), $n_more );
								} else {
									for ($i = 0; $i <$num_all;$i++) {
										$listed_pages[] = get_the_title($pages[$i]);
									}
									$output = implode(", ",$listed_pages);
								}								
								echo $output;  
							?></em> <a href="<?php echo admin_url('options-general.php?page=wl_lists')."#edit=".$wlist->get_id(); ?>">(<?php _ex('edit','user form: edit whitelist link','page-whitelists'); ?>)</a> 
							</label></li>
					<?php endforeach; ?>
					<li><a href="<?php echo admin_url('options-general.php?page=wl_lists')."#new"; ?>"><?php _ex('Create new...','user form: create new whitelist link','page-whitelists'); ?></a></li>
				</ul>
			</td>
    	</tr>
	</tbody>
</table>