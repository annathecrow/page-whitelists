<h3>Assigned Page Whitelists</h3>
<table class="form-table">
	<tbody>
		<tr><th scope="row">Page Whitelists</th>
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
							?></em> <a href="<?php echo admin_url('options-general.php?page=wl_lists')."#edit=".$wlist->get_id(); ?>">(edit)</a> 
							</label></li>
					<?php endforeach; ?>
					<li><a href="<?php echo admin_url('options-general.php?page=wl_lists')."#new"; ?>">Create new...</a></li>
				</ul>
			</td>
    	</tr>
	</tbody>
</table>